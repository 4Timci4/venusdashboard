<?php
/**
 * Venus IT Help Desk - Kimlik Doğrulama Yardımcı Sınıfı
 * 
 * Oturum yönetimi ve kullanıcı doğrulama işlemleri için yardımcı fonksiyonlar
 */

class AuthHelper {
    /**
     * Kullanıcının giriş yapmış olup olmadığını kontrol eder
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Giriş yapmış kullanıcı kimliğini döndürür
     */
    public static function getUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Giriş yapmış kullanıcı adını döndürür
     */
    public static function getUsername() {
        return isset($_SESSION['username']) ? $_SESSION['username'] : null;
    }
    
    /**
     * Kullanıcının belirli bir rolü olup olmadığını kontrol eder
     */
    public static function hasRole($role) {
        if (!self::isLoggedIn() || !isset($_SESSION['role_id'])) {
            return false;
        }
        
        // Rol ID'sini kontrol et
        return $_SESSION['role_id'] == $role;
    }
    
    /**
     * Kullanıcı yönetici mi kontrol eder
     */
    public static function isAdmin() {
        return self::hasRole(ROLE_ADMIN);
    }
    
    /**
     * Kullanıcı teknisyen mi kontrol eder
     */
    public static function isTechnician() {
        return self::hasRole(ROLE_TECHNICIAN);
    }
    
    /**
     * Kullanıcı normal kullanıcı mı kontrol eder
     */
    public static function isUser() {
        return self::hasRole(ROLE_USER);
    }
    
    /**
     * Oturum kontrolü yapar, giriş yapılmamışsa login sayfasına yönlendirir
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
    }
    
    /**
     * Yönetici rolü gerektirir, değilse yönlendirir
     */
    public static function requireAdmin() {
        self::requireLogin();
        
        if (!self::isAdmin()) {
            header('Location: ' . BASE_URL . '/403.php');
            exit;
        }
    }
    
    /**
     * Teknisyen veya yönetici rolü gerektirir, değilse yönlendirir
     */
    public static function requireTechnicianOrAdmin() {
        self::requireLogin();
        
        if (!self::isTechnician() && !self::isAdmin()) {
            header('Location: ' . BASE_URL . '/403.php');
            exit;
        }
    }
    
    /**
     * CSRF token'ı oluşturur veya alır
     */
    public static function getCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * CSRF token'ı doğrular
     */
    public static function validateCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Kullanıcı verilerini güvenli şekilde alır
     */
    public static function getUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        require_once BASE_PATH . '/models/User.php';
        $userModel = new User();
        return $userModel->getById(self::getUserId());
    }
}
