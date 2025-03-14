<?php
/**
 * Venus IT Help Desk - Basit Login İşlemi
 * 
 * Bu dosya form gönderimi ile kullanıcı girişi yapmak için kullanılır
 */

// Oturum başlat (eğer daha önce başlatılmamışsa)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Temel yapılandırma dosyasını yükle
require_once 'config/config.php';
require_once BASE_PATH . '/models/User.php';

// Gerekli yardımcı sınıfları yükle
require_once BASE_PATH . '/helpers/AuthHelper.php';

// Form verileri gönderilmiş mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // CSRF token kontrolü
    if (!isset($_POST['csrf_token']) || !AuthHelper::validateCsrfToken($_POST['csrf_token'])) {
        // CSRF hatası durumunda hata mesajı göster ve login sayfasına yönlendir
        $_SESSION['login_error'] = 'Güvenlik doğrulaması başarısız oldu. Lütfen tekrar deneyin.';
        header('Location: ' . BASE_URL . '/index.php?page=login');
        exit;
    }
    
    // Form doğrulama
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Kullanıcı adı ve şifre alanları zorunludur.';
        header('Location: ' . BASE_URL . '/index.php?page=login');
        exit;
    }
    
    try {
        // User modeli oluştur
        $userModel = new User();
        
        // Kullanıcı girişi
        $user = $userModel->login($username, $password);
        
        if ($user) {
            // Başarılı giriş - kullanıcıyı ana sayfaya yönlendir
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        } else {
            // Başarısız giriş
            $_SESSION['login_error'] = 'Geçersiz kullanıcı adı veya şifre.';
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }
    } catch (Exception $e) {
        // Hata durumu
        $_SESSION['login_error'] = 'Sunucu hatası: ' . $e->getMessage();
        header('Location: ' . BASE_URL . '/index.php?page=login');
        exit;
    }
} else {
    // POST olmayan istekleri login sayfasına yönlendir
    header('Location: ' . BASE_URL . '/index.php?page=login');
    exit;
}
