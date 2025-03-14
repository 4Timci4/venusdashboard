<?php
/**
 * Venus IT Help Desk - Durum Türü Modeli
 * 
 * Ticket durum türlerinin veritabanı işlemlerini yönetir
 */

require_once 'Model.php';

class StatusType extends Model {
    protected $table = 'status_types';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description', 'color', 'order', 'is_active'];
    
    /**
     * Tüm aktif durum türlerini getirir
     */
    public function getAllActive() {
        return $this->getWhere('is_active = 1', [], 'order ASC');
    }
}
