<?php
/**
 * Venus IT Help Desk - Ticket Model Sınıfı
 */

require_once 'Model.php';

class Ticket extends Model {
    protected $table = 'tickets';
    protected $fillable = [
        'user_id', 'request_type_id', 'status_id', 'service_id', 
        'technician_id', 'category_id', 'subcategory_id', 'subject', 
        'description', 'impact', 'impact_details', 'activity', 'priority'
    ];

    /**
     * Detaylarıyla birlikte ticket getirir
     */
    public function getWithDetails($id) {
        $sql = "SELECT t.*, 
                u.username, u.full_name, u.email, 
                r.name as request_type, r.color as request_color,
                s.name as status, s.color as status_color,
                serv.name as service,
                tech_user.full_name as technician_name,
                cat.name as category,
                subcat.name as subcategory
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN request_types r ON t.request_type_id = r.id
                LEFT JOIN status_types s ON t.status_id = s.id
                LEFT JOIN services serv ON t.service_id = serv.id
                LEFT JOIN technicians tech ON t.technician_id = tech.id
                LEFT JOIN users tech_user ON tech.user_id = tech_user.id
                LEFT JOIN service_categories cat ON t.category_id = cat.id
                LEFT JOIN sub_categories subcat ON t.subcategory_id = subcat.id
                WHERE t.id = :id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Tüm ticketları detayları ile getirir
     */
    public function getAllWithDetails($orderBy = 'created_at DESC', $limit = null, $offset = null) {
        $sql = "SELECT t.*, 
                u.username, u.full_name, 
                r.name as request_type, r.color as request_color,
                s.name as status, s.color as status_color,
                serv.name as service,
                tech_user.full_name as technician_name,
                cat.name as category,
                subcat.name as subcategory
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN request_types r ON t.request_type_id = r.id
                LEFT JOIN status_types s ON t.status_id = s.id
                LEFT JOIN services serv ON t.service_id = serv.id
                LEFT JOIN technicians tech ON t.technician_id = tech.id
                LEFT JOIN users tech_user ON tech.user_id = tech_user.id
                LEFT JOIN service_categories cat ON t.category_id = cat.id
                LEFT JOIN sub_categories subcat ON t.subcategory_id = subcat.id";
                
        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
            
            if ($offset) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Filtreleme ve arama ile ticketları getirir
     */
    public function search($search = null, $filters = [], $orderBy = 'created_at DESC', $limit = null, $offset = null) {
        $params = [];
        $conditions = [];
        
        // Arama terimi varsa koşul ekle
        if ($search) {
            $conditions[] = "(t.subject LIKE :search OR t.description LIKE :search OR u.full_name LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        // Filtreleri ekle
        if (!empty($filters)) {
            if (isset($filters['status_id']) && !empty($filters['status_id'])) {
                $conditions[] = "t.status_id = :status_id";
                $params[':status_id'] = $filters['status_id'];
            }
            
            if (isset($filters['request_type_id']) && !empty($filters['request_type_id'])) {
                $conditions[] = "t.request_type_id = :request_type_id";
                $params[':request_type_id'] = $filters['request_type_id'];
            }
            
            if (isset($filters['service_id']) && !empty($filters['service_id'])) {
                $conditions[] = "t.service_id = :service_id";
                $params[':service_id'] = $filters['service_id'];
            }
            
            if (isset($filters['technician_id']) && !empty($filters['technician_id'])) {
                $conditions[] = "t.technician_id = :technician_id";
                $params[':technician_id'] = $filters['technician_id'];
            }
            
            if (isset($filters['category_id']) && !empty($filters['category_id'])) {
                $conditions[] = "t.category_id = :category_id";
                $params[':category_id'] = $filters['category_id'];
            }
            
            if (isset($filters['priority']) && !empty($filters['priority'])) {
                $conditions[] = "t.priority = :priority";
                $params[':priority'] = $filters['priority'];
            }
            
            if (isset($filters['user_id']) && !empty($filters['user_id'])) {
                $conditions[] = "t.user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            if (isset($filters['date_from']) && !empty($filters['date_from'])) {
                $conditions[] = "t.created_at >= :date_from";
                $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
            }
            
            if (isset($filters['date_to']) && !empty($filters['date_to'])) {
                $conditions[] = "t.created_at <= :date_to";
                $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
            }
        }
        
        // Ana sorgu
        $sql = "SELECT t.*, 
                u.username, u.full_name, 
                r.name as request_type, r.color as request_color,
                s.name as status, s.color as status_color,
                serv.name as service,
                tech_user.full_name as technician_name,
                cat.name as category,
                subcat.name as subcategory
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN request_types r ON t.request_type_id = r.id
                LEFT JOIN status_types s ON t.status_id = s.id
                LEFT JOIN services serv ON t.service_id = serv.id
                LEFT JOIN technicians tech ON t.technician_id = tech.id
                LEFT JOIN users tech_user ON tech.user_id = tech_user.id
                LEFT JOIN service_categories cat ON t.category_id = cat.id
                LEFT JOIN sub_categories subcat ON t.subcategory_id = subcat.id";
                
        // Koşulları ekle
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        // Sıralama, limit ve offset
        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
            
            if ($offset) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }
        
        $stmt = $this->db->prepare($sql);
        
        // Parametreleri bağla
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Ticket durumunu günceller
     */
    public function updateStatus($id, $statusId, $userId = null) {
        // Status güncellemesi
        $updateResult = $this->update($id, ['status_id' => $statusId]);
        
        if ($updateResult) {
            // Aktivite logu oluştur
            $ticket = $this->getById($id);
            $statusModel = new StatusType();
            $status = $statusModel->getById($statusId);
            
            if ($ticket && $status) {
                $logModel = new ActivityLog();
                $logData = [
                    'user_id' => $userId,
                    'ticket_id' => $id,
                    'action' => 'status_update',
                    'details' => "Ticket durumu '" . $status['name'] . "' olarak güncellendi."
                ];
                
                $logModel->create($logData);
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Ticket'ı bir teknisyene atar
     */
    public function assignToTechnician($id, $technicianId, $userId = null) {
        // Teknisyen ataması
        $updateResult = $this->update($id, ['technician_id' => $technicianId]);
        
        if ($updateResult) {
            // Aktivite logu oluştur
            $ticket = $this->getById($id);
            
            if ($ticket) {
                $techModel = new Technician();
                $technician = $techModel->getById($technicianId);
                
                if ($technician) {
                    $userModel = new User();
                    $techUser = $userModel->getById($technician['user_id']);
                    
                    $logModel = new ActivityLog();
                    $logData = [
                        'user_id' => $userId,
                        'ticket_id' => $id,
                        'action' => 'assign_technician',
                        'details' => "Ticket '" . $techUser['full_name'] . "' adlı teknisyene atandı."
                    ];
                    
                    $logModel->create($logData);
                }
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Ticket'ı kapatır
     */
    public function closeTicket($id, $userId = null) {
        // Ticket'ı kapat
        $updateResult = $this->update($id, [
            'status_id' => 6, // Kapatıldı statüsü
            'closed_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($updateResult) {
            // Aktivite logu oluştur
            $logModel = new ActivityLog();
            $logData = [
                'user_id' => $userId,
                'ticket_id' => $id,
                'action' => 'close_ticket',
                'details' => "Ticket kapatıldı."
            ];
            
            $logModel->create($logData);
            
            return true;
        }
        
        return false;
    }

    /**
     * Ticket eklerini getirir
     */
    public function getAttachments($ticketId) {
        $sql = "SELECT * FROM ticket_attachments WHERE ticket_id = :ticket_id ORDER BY uploaded_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Ticket yorumlarını getirir
     */
    public function getComments($ticketId) {
        $sql = "SELECT c.*, u.username, u.full_name, u.role_id
                FROM ticket_comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.ticket_id = :ticket_id
                ORDER BY c.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ticket_id', $ticketId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Ticket istatistiklerini getirir
     */
    public function getStatistics() {
        $stats = [];
        
        // Durum bazında ticket sayıları
        $sql = "SELECT s.name, s.color, COUNT(t.id) as count 
                FROM status_types s
                LEFT JOIN tickets t ON s.id = t.status_id
                GROUP BY s.id
                ORDER BY s.id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['by_status'] = $stmt->fetchAll();
        
        // Kategori bazında ticket sayıları
        $sql = "SELECT c.name, COUNT(t.id) as count 
                FROM service_categories c
                LEFT JOIN tickets t ON c.id = t.category_id
                GROUP BY c.id
                ORDER BY count DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['by_category'] = $stmt->fetchAll();
        
        // Öncelik bazında ticket sayıları
        $sql = "SELECT 
                CASE 
                    WHEN priority = 1 THEN 'Düşük'
                    WHEN priority = 2 THEN 'Orta'
                    WHEN priority = 3 THEN 'Yüksek'
                    WHEN priority = 4 THEN 'Kritik'
                    ELSE 'Belirtilmemiş'
                END as priority_name,
                COUNT(id) as count
                FROM tickets
                GROUP BY priority
                ORDER BY priority";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['by_priority'] = $stmt->fetchAll();
        
        // Toplam ticket sayısı
        $sql = "SELECT COUNT(id) as total FROM tickets";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total'] = $stmt->fetchColumn();
        
        // Açık ticket sayısı
        $sql = "SELECT COUNT(id) as open FROM tickets WHERE status_id != 6"; // 6 = Kapatıldı
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['open'] = $stmt->fetchColumn();
        
        return $stats;
    }

    /**
     * Kullanıcıya ait ticketları getirir
     */
    public function getByUserId($userId, $orderBy = 'created_at DESC', $limit = null, $offset = null) {
        $sql = "SELECT t.*, 
                u.username, u.full_name, 
                r.name as request_type, r.color as request_color,
                s.name as status, s.color as status_color,
                serv.name as service,
                tech_user.full_name as technician_name,
                cat.name as category,
                subcat.name as subcategory
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN request_types r ON t.request_type_id = r.id
                LEFT JOIN status_types s ON t.status_id = s.id
                LEFT JOIN services serv ON t.service_id = serv.id
                LEFT JOIN technicians tech ON t.technician_id = tech.id
                LEFT JOIN users tech_user ON tech.user_id = tech_user.id
                LEFT JOIN service_categories cat ON t.category_id = cat.id
                LEFT JOIN sub_categories subcat ON t.subcategory_id = subcat.id
                WHERE t.user_id = :user_id";
                
        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
            
            if ($offset) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Teknisyene atanmış ticketları getirir
     */
    public function getByTechnicianId($technicianId, $orderBy = 'created_at DESC', $limit = null, $offset = null) {
        $sql = "SELECT t.*, 
                u.username, u.full_name, 
                r.name as request_type, r.color as request_color,
                s.name as status, s.color as status_color,
                serv.name as service,
                tech_user.full_name as technician_name,
                cat.name as category,
                subcat.name as subcategory
                FROM tickets t
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN request_types r ON t.request_type_id = r.id
                LEFT JOIN status_types s ON t.status_id = s.id
                LEFT JOIN services serv ON t.service_id = serv.id
                LEFT JOIN technicians tech ON t.technician_id = tech.id
                LEFT JOIN users tech_user ON tech.user_id = tech_user.id
                LEFT JOIN service_categories cat ON t.category_id = cat.id
                LEFT JOIN sub_categories subcat ON t.subcategory_id = subcat.id
                WHERE t.technician_id = :technician_id";
                
        if ($orderBy) {
            $sql .= " ORDER BY " . $orderBy;
        }
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
            
            if ($offset) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':technician_id', $technicianId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
