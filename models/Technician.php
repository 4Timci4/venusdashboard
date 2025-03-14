<?php
/**
 * Venus IT Help Desk - Teknisyen Modeli
 * 
 * Teknisyen işlemlerinin veritabanı işlemlerini yönetir
 */

require_once 'Model.php';

class Technician extends Model {
    protected $table = 'technicians';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'specialization', 'is_active'];
    
    /**
     * Kullanıcı ID'sine göre teknisyen bilgisini getirir
     */
    public function getByUserId($userId) {
        return $this->getOne('user_id = :user_id', [':user_id' => $userId]);
    }
    
    /**
     * Tüm aktif teknisyenleri getirir
     */
    public function getAllActive() {
        $sql = "SELECT 
                t.*,
                u.username,
                u.full_name,
                u.email,
                u.profile_image
                FROM 
                {$this->table} t
                JOIN users u ON t.user_id = u.id
                WHERE t.is_active = 1
                ORDER BY u.full_name ASC";
        
        return $this->query($sql)->fetchAll();
    }
    
    /**
     * Teknisyenin ticket istatistiklerini getirir
     */
    public function getStats($technicianId) {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM tickets WHERE technician_id = :id) AS total_tickets,
                (SELECT COUNT(*) FROM tickets WHERE technician_id = :id AND status_id = 1) AS open_tickets,
                (SELECT COUNT(*) FROM tickets WHERE technician_id = :id AND status_id = 5) AS resolved_tickets
                ";
        
        $params = [':id' => $technicianId];
        
        return $this->query($sql, $params)->fetch();
    }
}
