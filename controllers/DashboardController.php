<?php
/**
 * Venus IT Help Desk - Dashboard Kontrolcüsü
 * 
 * Dashboard ve ana sayfa işlemleri
 */

require_once 'Controller.php';
require_once BASE_PATH . '/models/Ticket.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/ServiceCategory.php';
require_once BASE_PATH . '/models/StatusType.php';
require_once BASE_PATH . '/models/ActivityLog.php';
require_once BASE_PATH . '/models/Technician.php';
require_once BASE_PATH . '/helpers/AuthHelper.php';

class DashboardController extends Controller {
    private $ticketModel;
    private $userModel;
    
    public function __construct() {
        $this->ticketModel = new Ticket();
        $this->userModel = new User();
    }
    
    /**
     * Dashboard sayfasını gösterir
     */
    public function index() {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Ticket istatistiklerini al
        $stats = $this->ticketModel->getStatistics();
        
        // Son ticketları al
        $recentTickets = [];
        
        if (AuthHelper::isAdmin() || AuthHelper::isTechnician()) {
            // Yönetici ve teknisyen için tüm son ticketlar
            $recentTickets = $this->ticketModel->getAllWithDetails('created_at DESC', 5);
        } else {
            // Normal kullanıcı için sadece kendi ticketları
            $recentTickets = $this->ticketModel->getByUserId(AuthHelper::getUserId(), 'created_at DESC', 5);
        }
        
        // Aktif teknisyenleri al
        $technicians = $this->userModel->getTechnicians();
        
        // Kategorilere göre ticket sayılarını al
        $categoryModel = new ServiceCategory();
        $categories = $categoryModel->getAll();
        
        $categoryStats = [];
        foreach ($categories as $category) {
            $count = $this->ticketModel->count("category_id = :category_id", [':category_id' => $category['id']]);
            $categoryStats[] = [
                'name' => $category['name'],
                'count' => $count
            ];
        }
        
        // Durum türlerine göre ticket sayılarını al
        $statusTypeModel = new StatusType();
        $statuses = $statusTypeModel->getAll();
        
        $statusStats = [];
        foreach ($statuses as $status) {
            $count = $this->ticketModel->count("status_id = :status_id", [':status_id' => $status['id']]);
            $statusStats[] = [
                'name' => $status['name'],
                'color' => $status['color'],
                'count' => $count
            ];
        }
        
        // Son aktiviteleri al
        $logModel = new ActivityLog();
        $recentLogs = $logModel->getRecent(10);
        
        // View'a veri gönder
        $this->view('dashboard/index', [
            'stats' => $stats,
            'recentTickets' => $recentTickets,
            'technicians' => $technicians,
            'categoryStats' => $categoryStats,
            'statusStats' => $statusStats,
            'recentLogs' => $recentLogs,
            'layout' => 'main',
            'messages' => $this->getAllFlashMessages()
        ]);
    }
    
    /**
     * Kişisel dashboard sayfasını gösterir (normal kullanıcılar için)
     */
    public function userDashboard() {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Kullanıcının ticketlarını al
        $tickets = $this->ticketModel->getByUserId(AuthHelper::getUserId(), 'created_at DESC');
        
        // Kullanıcının ticketlarının durumlarına göre sayılarını hesapla
        $statusTypeModel = new StatusType();
        $statuses = $statusTypeModel->getAll();
        
        $ticketsByStatus = [];
        foreach ($statuses as $status) {
            $count = $this->ticketModel->count(
                "user_id = :user_id AND status_id = :status_id", 
                [':user_id' => AuthHelper::getUserId(), ':status_id' => $status['id']]
            );
            
            $ticketsByStatus[] = [
                'name' => $status['name'],
                'color' => $status['color'],
                'count' => $count
            ];
        }
        
        // Kullanıcının detaylarını al
        $user = AuthHelper::getUser();
        
        // View'a veri gönder
        $this->view('dashboard/user', [
            'user' => $user,
            'tickets' => $tickets,
            'ticketsByStatus' => $ticketsByStatus,
            'totalTickets' => count($tickets),
            'layout' => 'main',
            'messages' => $this->getAllFlashMessages()
        ]);
    }
    
    /**
     * Teknisyen dashboard sayfasını gösterir
     */
    public function technicianDashboard() {
        // Giriş kontrolü
        AuthHelper::requireLogin();
        
        // Teknisyen kontrolü
        if (!AuthHelper::isAdmin() && !AuthHelper::isTechnician()) {
            $this->redirect(BASE_URL . '/index.php');
            return;
        }
        
        // Teknisyen ID'sini al
        $technicianModel = new Technician();
        $technician = $technicianModel->getByUserId(AuthHelper::getUserId());
        
        if (!$technician && !AuthHelper::isAdmin()) {
            $this->setFlashMessage('error', 'Teknisyen kaydınız bulunamadı.', 'error');
            $this->redirect(BASE_URL . '/index.php');
            return;
        }
        
        $technicianId = $technician ? $technician['id'] : null;
        
        // Admin tüm ticketları görüntüleyebilir
        if (AuthHelper::isAdmin()) {
            $assignedTickets = $this->ticketModel->getAllWithDetails('created_at DESC');
        } else {
            // Teknisyene atanmış ticketları al
            $assignedTickets = $this->ticketModel->getByTechnicianId($technicianId, 'created_at DESC');
        }
        
        // Durum türlerine göre atanmış ticket sayılarını hesapla
        $statusTypeModel = new StatusType();
        $statuses = $statusTypeModel->getAll();
        
        $ticketsByStatus = [];
        foreach ($statuses as $status) {
            $conditions = "status_id = :status_id";
            $params = [':status_id' => $status['id']];
            
            if (!AuthHelper::isAdmin() && $technicianId) {
                $conditions .= " AND technician_id = :technician_id";
                $params[':technician_id'] = $technicianId;
            }
            
            $count = $this->ticketModel->count($conditions, $params);
            
            $ticketsByStatus[] = [
                'name' => $status['name'],
                'color' => $status['color'],
                'count' => $count
            ];
        }
        
        // Son aktiviteleri al
        $logModel = new ActivityLog();
        
        if (AuthHelper::isAdmin()) {
            $recentLogs = $logModel->getRecent(10);
        } else {
            $ticketIds = array_column($assignedTickets, 'id');
            $recentLogs = empty($ticketIds) ? [] : $logModel->getByTicketIds($ticketIds, 10);
        }
        
        // View'a veri gönder
        $this->view('dashboard/technician', [
            'assignedTickets' => $assignedTickets,
            'ticketsByStatus' => $ticketsByStatus,
            'totalAssigned' => count($assignedTickets),
            'recentLogs' => $recentLogs,
            'layout' => 'main',
            'messages' => $this->getAllFlashMessages()
        ]);
    }
    
    /**
     * Performans raporlarını gösterir (sadece admin)
     */
    public function reports() {
        // Giriş ve admin kontrolü
        AuthHelper::requireAdmin();
        
        // Tarih aralığı parametrelerini al
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        
        // Teknisyen bazında ticket istatistikleri
        $sql = "SELECT 
                t.technician_id,
                u.full_name as technician_name,
                COUNT(t.id) as total_tickets,
                SUM(CASE WHEN t.status_id = 5 THEN 1 ELSE 0 END) as resolved_tickets,
                AVG(TIMESTAMPDIFF(HOUR, t.created_at, t.updated_at)) as avg_resolution_time
                FROM tickets t
                JOIN technicians tech ON t.technician_id = tech.id
                JOIN users u ON tech.user_id = u.id
                WHERE t.created_at BETWEEN :start_date AND :end_date
                GROUP BY t.technician_id, u.full_name
                ORDER BY total_tickets DESC";
        
        $params = [
            ':start_date' => $startDate . ' 00:00:00',
            ':end_date' => $endDate . ' 23:59:59'
        ];
        
        $technicianStats = $this->ticketModel->query($sql, $params)->fetchAll();
        
        // Kategori bazında ticket istatistikleri
        $sql = "SELECT 
                sc.name as category_name,
                COUNT(t.id) as total_tickets,
                SUM(CASE WHEN t.status_id = 5 THEN 1 ELSE 0 END) as resolved_tickets,
                AVG(TIMESTAMPDIFF(HOUR, t.created_at, IFNULL(t.closed_at, NOW()))) as avg_resolution_time
                FROM tickets t
                JOIN service_categories sc ON t.category_id = sc.id
                WHERE t.created_at BETWEEN :start_date AND :end_date
                GROUP BY sc.name
                ORDER BY total_tickets DESC";
        
        $categoryStats = $this->ticketModel->query($sql, $params)->fetchAll();
        
        // Durum bazında ticket sayıları
        $sql = "SELECT 
                st.name as status_name,
                st.color,
                COUNT(t.id) as count
                FROM tickets t
                JOIN status_types st ON t.status_id = st.id
                WHERE t.created_at BETWEEN :start_date AND :end_date
                GROUP BY st.name, st.color
                ORDER BY count DESC";
        
        $statusStats = $this->ticketModel->query($sql, $params)->fetchAll();
        
        // Zaman içinde açılan ticket sayıları (grafik için)
        $sql = "SELECT 
                DATE(t.created_at) as date,
                COUNT(t.id) as count
                FROM tickets t
                WHERE t.created_at BETWEEN :start_date AND :end_date
                GROUP BY DATE(t.created_at)
                ORDER BY date";
        
        $timelineData = $this->ticketModel->query($sql, $params)->fetchAll();
        
        // View'a veri gönder
        $this->view('dashboard/reports', [
            'technicianStats' => $technicianStats,
            'categoryStats' => $categoryStats,
            'statusStats' => $statusStats,
            'timelineData' => $timelineData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'layout' => 'main',
            'messages' => $this->getAllFlashMessages()
        ]);
    }
}
