<?php
/**
 * Venus IT Help Desk - API Login Endpoint
 * 
 * AJAX ile kullanıcı girişi yapmak için kullanılan API endpoint'i
 * Bu dosya AuthController'ı kullanmak yerine direkt login işlemi yapar
 */

// Oturum başlat
session_start();

// Hata raporlamasını etkinleştir
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Temel yapılandırma dosyasını yükle
require_once '../../config/config.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/helpers/ValidationHelper.php';

// JSON başlık ayarla
header('Content-Type: application/json');

// Debug bilgisi
$debug = [
    'post_data' => $_POST,
    'session' => $_SESSION,
    'request_method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'Belirtilmemiş'
];

// Sadece POST istekleri kabul edilir
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Geçersiz istek. Sadece POST istekleri kabul edilir.',
        'debug' => $debug
    ]);
    exit;
}

// CSRF token kontrolü
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode([
        'success' => false, 
        'message' => 'Güvenlik doğrulaması başarısız oldu.',
        'debug' => $debug
    ]);
    exit;
}

// Form verilerini al ve temizle
$username = isset($_POST['username']) ? ValidationHelper::sanitize($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : ''; // Şifreyi sanitize etme

// Form doğrulama
if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Kullanıcı adı ve şifre alanları zorunludur.',
        'debug' => $debug
    ]);
    exit;
}

try {
    // User modeli oluştur
    $userModel = new User();
    
    // Kullanıcı girişi
    $user = $userModel->login($username, $password);
    
    if ($user) {
        // Başarılı giriş
        echo json_encode([
            'success' => true,
            'message' => 'Giriş başarılı. Yönlendiriliyorsunuz...',
            'redirect' => BASE_URL . '/index.php'
        ]);
    } else {
        // Başarısız giriş
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Geçersiz kullanıcı adı veya şifre.'
        ]);
    }
} catch (Exception $e) {
    // Hata durumunda JSON hatası döndür
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Sunucu hatası: ' . $e->getMessage(),
        'debug' => $debug
    ]);
}
