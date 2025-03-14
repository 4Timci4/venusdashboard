<?php
/**
 * Venus IT Help Desk - Ticket Yorum Modeli
 * 
 * Ticket yorumlarının veritabanı işlemlerini yönetir
 */

require_once 'Model.php';

class TicketComment extends Model {
    protected $table = 'ticket_comments';
    protected $primaryKey = 'id';
    protected $fillable = ['ticket_id', 'user_id', 'comment', 'created_at', 'updated_at'];
    
    /**
     * Ticket ID'sine göre yorumları getirir
     */
    public function getByTicketId($ticketId) {
        return $this->getWhere('ticket_id = :ticket_id', 
            [':ticket_id' => $ticketId], 
            'created_at ASC');
    }
    
    /**
     * Yorum detaylarını kullanıcı bilgileri ile birlikte getirir
     */
    public function getWithUserDetails($commentId) {
        $sql = "SELECT tc.*, u.full_name, u.email, u.avatar 
                FROM {$this->table} tc
                LEFT JOIN users u ON tc.user_id = u.id
                WHERE tc.id = :comment_id";
                
        return $this->queryOne($sql, [':comment_id' => $commentId]);
    }
}
