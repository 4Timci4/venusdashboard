<?php
/**
 * Venus IT Help Desk - Ticket Kapatma
 */

// Temel yapılandırma ve veritabanı bağlantısı
require_once '../config/init.php';

// Controller dosyasını dahil et
require_once BASE_PATH . '/controllers/TicketController.php';

// Ticket ID'si kontrolü
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    // Geçersiz ID, ana sayfaya yönlendir
    header('Location: ' . BASE_URL . '/tickets.php');
    exit;
}

// Controller oluştur ve close metodunu çağır
$controller = new TicketController();
$controller->close($id);
