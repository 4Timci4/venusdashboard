<?php
/**
 * Venus IT Help Desk - Hizmet Kategorisi Modeli
 * 
 * Hizmet kategorilerinin veritabanı işlemlerini yönetir
 */

require_once 'Model.php';

class ServiceCategory extends Model {
    protected $table = 'service_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description', 'is_active'];
    
    /**
     * Tüm aktif kategorileri getirir
     */
    public function getAllActive() {
        return $this->getWhere('is_active = 1', [], 'name ASC');
    }
    
    /**
     * Kategori ile alakalı alt kategorileri getirir
     */
    public function getSubcategories($categoryId) {
        $subcategoryModel = new SubCategory();
        return $subcategoryModel->getByCategoryId($categoryId);
    }
}
