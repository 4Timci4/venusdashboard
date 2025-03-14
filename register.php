<?php
/**
 * Venus IT Help Desk - Kayıt Sayfası
 * 
 * Yeni kullanıcı kaydı için sayfa
 */

// Konfigürasyon ve temel gereksinimleri yükle
require_once 'config/config.php';
require_once 'controllers/AuthController.php';

// Auth controller oluştur
$authController = new AuthController();

// Kayıt formunu göster
$authController->showRegisterForm();
