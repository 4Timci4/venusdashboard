<?php
/**
 * Venus IT Help Desk - Hizmet Modeli
 * 
 * Hizmet türlerinin veritabanı işlemlerini yönetir
 */

require_once 'Model.php';

class Service extends Model {
    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description', 'order', 'is_active'];
    
    /**
     * Tüm aktif hizmetleri getirir
     */
    public function getAllActive() {
        return $this->getWhere('is_active = 1', [], 'order ASC');
    }
    
    /**
     * Kategori ID'sine göre hizmetleri getirir
     */
    public function getByCategoryId($categoryId) {
        return $this->getWhere('category_id = :category_id AND is_active = 1', 
            [':category_id' => $categoryId], 
            'order ASC');
    }
}
