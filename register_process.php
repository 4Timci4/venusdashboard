<?php
/**
 * Venus IT Help Desk - Kayıt İşlemi
 * 
 * Yeni kullanıcı kaydı için form işleme
 */

// Konfigürasyon ve temel gereksinimleri yükle
require_once 'config/config.php';
require_once 'controllers/AuthController.php';

// Auth controller oluştur
$authController = new AuthController();

// Kayıt işlemini gerçekleştir
$authController->register();
