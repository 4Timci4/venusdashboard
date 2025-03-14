<?php
/**
 * Venus IT Help Desk - Ticket Detay Sayfası
 */

// Temel yapılandırma ve veritabanı bağlantısı
require_once '../config/init.php';

// Controller dosyasını dahil et
require_once BASE_PATH . '/controllers/TicketController.php';

// Ticket ID'si kontrolü
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    // Geçersiz ID, ana sayfaya yönlendir
    header('Location: ' . BASE_URL . '/tickets.php');
    exit;
}

// Controller oluştur ve view metodunu çağır
$controller = new TicketController();
$controller->viewTicket($id);
