<?php
/**
 * Venus IT Help Desk - Aktivite Logu Modeli
 * 
 * Ticket ile ilgili aktivitelerin logs veritabanı işlemlerini yönetir
 */

require_once 'Model.php';

class ActivityLog extends Model {
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id', 'ticket_id', 'action', 'details', 'created_at'];
    
    /**
     * Belirli sayıda son aktivite kayıtlarını getirir
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT 
                al.*,
                u.username,
                u.full_name
                FROM 
                {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        $params = [':limit' => $limit];
        
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * Belirli ticket idlerine ait aktivite kayıtlarını getirir
     */
    public function getByTicketIds($ticketIds, $limit = 10) {
        if (empty($ticketIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($ticketIds), '?'));
        
        $sql = "SELECT 
                al.*,
                u.username,
                u.full_name
                FROM 
                {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.ticket_id IN ({$placeholders})
                ORDER BY al.created_at DESC
                LIMIT {$limit}";
        
        return $this->query($sql, $ticketIds)->fetchAll();
    }
    
    /**
     * Belirli bir kullanıcıya ait son aktivite kayıtlarını getirir
     */
    public function getByUserId($userId, $limit = 10) {
        $sql = "SELECT 
                al.*,
                t.subject as ticket_subject
                FROM 
                {$this->table} al
                LEFT JOIN tickets t ON al.ticket_id = t.id
                WHERE al.user_id = :user_id
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        $params = [
            ':user_id' => $userId,
            ':limit' => $limit
        ];
        
        return $this->query($sql, $params)->fetchAll();
    }
}
