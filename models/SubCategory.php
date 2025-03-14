<?php
/**
 * Venus IT Help Desk - Alt Kategori Modeli
 * 
 * Alt kategorilerin veritabanı işlemlerini yönetir
 */

require_once 'Model.php';

class SubCategory extends Model {
    protected $table = 'sub_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['category_id', 'name', 'description', 'is_active'];
    
    /**
     * Kategori ID'sine göre alt kategorileri getirir
     */
    public function getByCategoryId($categoryId) {
        return $this->getWhere('category_id = :category_id AND is_active = 1', [':category_id' => $categoryId], 'name ASC');
    }
    
    /**
     * Tüm aktif alt kategorileri getirir
     */
    public function getAllActive() {
        return $this->getWhere('is_active = 1', [], 'name ASC');
    }
    
    /**
     * Alt kategorileri kategorilerle birlikte getirir
     */
    public function getAllWithCategories() {
        $sql = "SELECT 
                sc.*,
                c.name as category_name
                FROM 
                {$this->table} sc
                JOIN service_categories c ON sc.category_id = c.id
                WHERE sc.is_active = 1
                ORDER BY c.name ASC, sc.name ASC";
        
        return $this->query($sql)->fetchAll();
    }
}
