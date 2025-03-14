<?php
/**
 * Venus IT Help Desk - API Ticket Status Endpoint
 * 
 * Ticket durumunu güncelleyen API endpoint'i
 */

// Temel yapılandırma dosyasını yükle
require_once '../../config/config.php';

// TicketController'ı yükle
require_once BASE_PATH . '/controllers/TicketController.php';

// Ticket ID'sini al
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// TicketController örneği oluştur
$controller = new TicketController();

// Durumu güncelle
$controller->updateStatus($id);
