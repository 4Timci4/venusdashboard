<?php
/**
 * Venus IT Help Desk - İstek Türü Modeli
 * 
 * Ticket istek türlerinin veritabanı işlemlerini yönetir
 */

require_once 'Model.php';

class RequestType extends Model {
    protected $table = 'request_types';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description', 'order', 'is_active'];
    
    /**
     * Tüm aktif istek türlerini getirir
     */
    public function getAllActive() {
        return $this->getWhere('is_active = 1', [], 'order ASC');
    }
}
