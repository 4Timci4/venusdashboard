<?php
/**
 * Venus IT Help Desk - Genel Yapılandırma Dosyası
 * 
 * Uygulama genelinde kullanılan yapılandırma ayarlarını içerir.
 */

// Uygulama bilgileri
define('APP_NAME', 'Venus IT Help Desk');
define('APP_VERSION', '1.0.0');

// Temel URL ve yollar
$base_path = str_replace('\\', '/', dirname(dirname(__FILE__)));
$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http');
$base_url .= '://' . $_SERVER['HTTP_HOST'];
$base_url .= str_replace(rtrim($_SERVER['DOCUMENT_ROOT'], '/'), '', $base_path);

define('BASE_PATH', $base_path);
define('BASE_URL', $base_url);
define('ASSETS_URL', $base_url . '/assets');
define('UPLOADS_PATH', $base_path . '/assets/uploads');
define('UPLOADS_URL', $base_url . '/assets/uploads');

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Hata raporlama
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Oturum ayarları
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
session_start();

// CSRF koruma anahtarı
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Güvenlik ayarları
define('HASH_COST', 10); // Password hash cost

// Dosya yükleme limitleri
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip,rar');

// Sayfalama ayarları
define('ITEMS_PER_PAGE', 15);

// Kullanıcı rolleri
define('ROLE_ADMIN', 1);
define('ROLE_TECHNICIAN', 2);
define('ROLE_USER', 3);

// Ticket öncelik seviyeleri
define('PRIORITY_LOW', 1);
define('PRIORITY_MEDIUM', 2);
define('PRIORITY_HIGH', 3);
define('PRIORITY_CRITICAL', 4);

// Dosya yükleme yolunu kontrol et ve oluştur
if (!file_exists(UPLOADS_PATH)) {
    mkdir(UPLOADS_PATH, 0755, true);
}

// Veritabanı bağlantısını dahil et
require_once 'database.php';
