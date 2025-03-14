<?php
/**
 * Venus IT Help Desk - API Subcategories Endpoint
 * 
 * Servis kategorisine göre alt kategorileri getiren API endpoint'i
 */

// Temel yapılandırma dosyasını yükle
require_once '../../config/config.php';

// TicketController'ı yükle
require_once BASE_PATH . '/controllers/TicketController.php';

// Kategori ID'sini al
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// TicketController örneği oluştur
$controller = new TicketController();

// Alt kategorileri getir
$controller->getSubcategories($categoryId);
