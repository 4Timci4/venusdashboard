<?php
// Oturum başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Temel yapılandırma dosyasını yükle
require_once __DIR__ . '/config.php';

// Yardımcı sınıfları yükle
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/ValidationHelper.php';
require_once __DIR__ . '/../helpers/FileHelper.php';

// Hata raporlamayı ayarla
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zaman dilimini ayarla
date_default_timezone_set('Europe/Istanbul');

// Karakter kodlamasını ayarla
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8'); 