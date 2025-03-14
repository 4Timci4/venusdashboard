<?php
/**
 * Venus IT Help Desk - Kimlik Doğrulama Kontrolcüsü
 * 
 * Kullanıcı girişi, çıkışı ve oturum yönetimi işlemleri
 */

require_once 'Controller.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/helpers/AuthHelper.php';
require_once BASE_PATH . '/helpers/ValidationHelper.php';

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Login formunu gösterir
     */
    public function showLoginForm() {
        // Kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
        if (AuthHelper::isLoggedIn()) {
            $this->redirect(BASE_URL . '/index.php');
            return;
        }
        
        // CSRF token oluştur
        $csrf_token = $this->getCsrfToken();
        
        // Login formunu göster
        $this->view('auth/login', [
            'csrf_token' => $csrf_token,
            'error' => $this->getFlashMessage('login_error')
        ]);
    }
    
    /**
     * Kullanıcı girişi işlemini gerçekleştirir
     */
    public function login() {
        // POST metoduyla gelmemişse login sayfasına yönlendir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/login.php');
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('login_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'error');
            $this->redirect(BASE_URL . '/login.php');
            return;
        }
        
        // Form verilerini al
        $data = $this->getFormData(['username', 'password']);
        
        // Form doğrulama
        $errors = ValidationHelper::validate($data, [
            'username' => ['required' => true],
            'password' => ['required' => true]
        ]);
        
        if (!empty($errors)) {
            $this->setFlashMessage('login_error', 'Kullanıcı adı ve şifre alanları zorunludur.', 'error');
            $this->redirect(BASE_URL . '/login.php');
            return;
        }
        
        // Kullanıcı girişi
        $user = $this->userModel->login($data['username'], $data['password']);
        
        if ($user) {
            // Başarılı giriş
            $this->setFlashMessage('success', 'Giriş başarılı. Hoş geldiniz, ' . $user['full_name'], 'success');
            $this->redirect(BASE_URL . '/index.php');
        } else {
            // Başarısız giriş
            $this->setFlashMessage('login_error', 'Geçersiz kullanıcı adı veya şifre.', 'error');
            $this->redirect(BASE_URL . '/login.php');
        }
    }
    
    /**
     * Kullanıcı çıkışı
     */
    public function logout() {
        // Kullanıcı çıkışını gerçekleştir
        $this->userModel->logout();
        
        // Başarılı çıkış mesajı
        $this->setFlashMessage('success', 'Çıkış işleminiz başarıyla gerçekleştirildi.', 'success');
        
        // Login sayfasına yönlendir
        $this->redirect(BASE_URL . '/login.php');
    }
    
    /**
     * Ajax ile kullanıcı girişi
     */
    public function ajaxLogin() {
        // Sadece POST ve AJAX istekleri kabul edilir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            $this->json(['success' => false, 'message' => 'Geçersiz istek.'], 400);
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->json(['success' => false, 'message' => 'Güvenlik doğrulaması başarısız oldu.'], 403);
            return;
        }
        
        // Form verilerini al
        $data = $this->getFormData(['username', 'password']);
        
        // Form doğrulama
        $errors = ValidationHelper::validate($data, [
            'username' => ['required' => true],
            'password' => ['required' => true]
        ]);
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'message' => 'Kullanıcı adı ve şifre alanları zorunludur.'], 400);
            return;
        }
        
        // Kullanıcı girişi
        $user = $this->userModel->login($data['username'], $data['password']);
        
        if ($user) {
            // Başarılı giriş
            $this->json([
                'success' => true,
                'message' => 'Giriş başarılı. Yönlendiriliyorsunuz...',
                'redirect' => BASE_URL . '/index.php'
            ]);
        } else {
            // Başarısız giriş
            $this->json(['success' => false, 'message' => 'Geçersiz kullanıcı adı veya şifre.'], 401);
        }
    }
    
    /**
     * Şifremi unuttum formunu gösterir
     */
    public function showForgotPasswordForm() {
        // CSRF token oluştur
        $csrf_token = $this->getCsrfToken();
        
        // Şifremi unuttum formunu göster
        $this->view('auth/forgot_password', [
            'csrf_token' => $csrf_token,
            'error' => $this->getFlashMessage('forgot_error'),
            'success' => $this->getFlashMessage('forgot_success')
        ]);
    }
    
    /**
     * Şifre sıfırlama e-postası gönderir
     */
    public function forgotPassword() {
        // POST metoduyla gelmemişse forgot password sayfasına yönlendir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/forgot_password.php');
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('forgot_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'error');
            $this->redirect(BASE_URL . '/forgot_password.php');
            return;
        }
        
        // Form verilerini al
        $data = $this->getFormData(['email']);
        
        // Form doğrulama
        $errors = ValidationHelper::validate($data, [
            'email' => ['required' => true, 'email' => true]
        ]);
        
        if (!empty($errors)) {
            $this->setFlashMessage('forgot_error', 'Geçerli bir e-posta adresi giriniz.', 'error');
            $this->redirect(BASE_URL . '/forgot_password.php');
            return;
        }
        
        // E-posta ile kullanıcıyı bul
        $user = $this->userModel->getByEmail($data['email']);
        
        if (!$user) {
            // E-posta bulunamadı - ama güvenlik için aynı mesajı göster
            $this->setFlashMessage('forgot_success', 'Şifre sıfırlama talimatları e-posta adresinize gönderildi.', 'success');
            $this->redirect(BASE_URL . '/forgot_password.php');
            return;
        }
        
        // Gerçek uygulamada burada şifre sıfırlama e-postası gönderilir
        // Şimdilik sadece başarılı mesajı gösterelim
        $this->setFlashMessage('forgot_success', 'Şifre sıfırlama talimatları e-posta adresinize gönderildi.', 'success');
        $this->redirect(BASE_URL . '/forgot_password.php');
    }
    
    /**
     * Kullanıcı profil sayfasını gösterir
     */
    public function showProfile() {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Kullanıcı bilgilerini al
        $user = AuthHelper::getUser();
        
        // Profil sayfasını göster
        $this->view('auth/profile', [
            'user' => $user,
            'csrf_token' => $this->getCsrfToken(),
            'error' => $this->getFlashMessage('profile_error'),
            'success' => $this->getFlashMessage('profile_success')
        ]);
    }
    
    /**
     * Kullanıcı profilini günceller
     */
    public function updateProfile() {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // POST metoduyla gelmemişse profil sayfasına yönlendir
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/profile.php');
            return;
        }
        
        // CSRF token kontrolü
        if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
            $this->setFlashMessage('profile_error', 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.', 'error');
            $this->redirect(BASE_URL . '/profile.php');
            return;
        }
        
        // Form verilerini al
        $data = $this->getFormData(['full_name', 'email', 'department', 'current_password', 'new_password', 'confirm_password']);
        
        // Güncellenecek veriyi hazırla
        $updateData = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'department' => $data['department']
        ];
        
        // Form doğrulama
        $errors = ValidationHelper::validate($updateData, [
            'full_name' => ['required' => true],
            'email' => ['required' => true, 'email' => true]
        ]);
        
        if (!empty($errors)) {
            $this->setFlashMessage('profile_error', 'Lütfen tüm zorunlu alanları doldurun ve geçerli bir e-posta adresi girin.', 'error');
            $this->redirect(BASE_URL . '/profile.php');
            return;
        }
        
        // Şifre değiştirilecek mi?
        if (!empty($data['current_password']) && !empty($data['new_password'])) {
            // Şifre doğrulama
            $passwordErrors = ValidationHelper::validate([
                'new_password' => $data['new_password'],
                'confirm_password' => $data['confirm_password']
            ], [
                'new_password' => ['required' => true, 'minLength' => 8, 'strongPassword' => true],
                'confirm_password' => ['required' => true, 'match' => 'new_password']
            ]);
            
            if (!empty($passwordErrors)) {
                $this->setFlashMessage('profile_error', 'Şifre en az 8 karakter olmalı ve şifreler eşleşmelidir.', 'error');
                $this->redirect(BASE_URL . '/profile.php');
                return;
            }
            
            // Mevcut kullanıcıyı al
            $user = AuthHelper::getUser();
            
            // Mevcut şifreyi kontrol et
            if (!password_verify($data['current_password'], $user['password'])) {
                $this->setFlashMessage('profile_error', 'Mevcut şifre yanlış.', 'error');
                $this->redirect(BASE_URL . '/profile.php');
                return;
            }
            
            // Yeni şifreyi ekle
            $updateData['password'] = $data['new_password'];
        }
        
        // Kullanıcıyı güncelle
        $result = $this->userModel->update(AuthHelper::getUserId(), $updateData);
        
        if ($result) {
            $this->setFlashMessage('profile_success', 'Profil bilgileriniz başarıyla güncellendi.', 'success');
        } else {
            $this->setFlashMessage('profile_error', 'Profil güncellenirken bir hata oluştu.', 'error');
        }
        
        $this->redirect(BASE_URL . '/profile.php');
    }
}
