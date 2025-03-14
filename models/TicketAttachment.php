<?php
/**
 * Venus IT Help Desk - Ticket Ekleri Modeli
 * 
 * Ticket dosya eklerinin veritabanı işlemlerini yönetir
 */

require_once 'Model.php';

class TicketAttachment extends Model {
    protected $table = 'ticket_attachments';
    protected $primaryKey = 'id';
    protected $fillable = ['ticket_id', 'file_name', 'file_path', 'file_type', 'file_size', 'created_at', 'updated_at'];
    
    /**
     * Ticket ID'sine göre ekleri getirir
     */
    public function getByTicketId($ticketId) {
        return $this->getWhere('ticket_id = :ticket_id', 
            [':ticket_id' => $ticketId], 
            'created_at DESC');
    }
    
    /**
     * Ek detaylarını getirir
     */
    public function getAttachmentDetails($attachmentId) {
        return $this->getById($attachmentId);
    }
    
    /**
     * Dosya yolu ile eki bulur
     */
    public function getByFilePath($filePath) {
        return $this->getOne('file_path = :file_path', [':file_path' => $filePath]);
    }
    
    /**
     * Belirli bir ticketa ait ekleri siler
     */
    public function deleteByTicketId($ticketId) {
        return $this->delete('ticket_id = :ticket_id', [':ticket_id' => $ticketId]);
    }
}
