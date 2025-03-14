<?php
/**
 * Venus IT Help Desk - Kullanıcı Model Sınıfı
 */

require_once 'Model.php';

class User extends Model {
    protected $table = 'users';
    protected $fillable = ['username', 'password', 'email', 'full_name', 'department', 'role_id'];

    /**
     * Kullanıcı adına göre kullanıcı getirir
     */
    public function getByUsername($username) {
        $sql = "SELECT * FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * E-posta adresine göre kullanıcı getirir
     */
    public function getByEmail($email) {
        $sql = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Kullanıcıyı oluştururken şifreyi hash'ler
     */
    public function create($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        }
        
        return parent::create($data);
    }

    /**
     * Kullanıcı güncellenirken şifreyi hash'ler
     */
    public function update($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        } else {
            unset($data['password']);
        }
        
        return parent::update($id, $data);
    }

    /**
     * Doğru kullanıcı adı ve şifre ile giriş yapar
     */
    public function login($username, $password) {
        $user = $this->getByUsername($username);
        
        if (!$user) {
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            // Yeni hash algoritması varsa şifreyi güncelle
            if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => HASH_COST])) {
                $this->update($user['id'], ['password' => $password]);
            }
            
            // Oturum bilgilerini ayarla
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];
            
            return $user;
        }
        
        return false;
    }

    /**
     * Kullanıcı çıkış yapar
     */
    public function logout() {
        // Oturum değişkenlerini temizle
        $_SESSION = [];
        
        // Oturum çerezini sil
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        
        // Oturumu sonlandır
        session_destroy();
        
        return true;
    }

    /**
     * Kullanıcı rolünü getirir
     */
    public function getRole($userId) {
        $sql = "SELECT r.* FROM roles r
                JOIN users u ON r.id = u.role_id
                WHERE u.id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Kullanıcının rolünü kontrol eder
     */
    public function hasRole($userId, $roleId) {
        $sql = "SELECT 1 FROM users WHERE id = :user_id AND role_id = :role_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':role_id', $roleId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Teknisyen olan kullanıcıları getirir
     */
    public function getTechnicians() {
        $sql = "SELECT u.*, t.id as technician_id, t.specialty 
                FROM users u
                JOIN technicians t ON u.id = t.user_id
                WHERE t.is_active = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
