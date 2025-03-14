<?php
/**
 * Venus IT Help Desk - Temel Model Sınıfı
 * 
 * Tüm modeller için temel fonksiyonları içerir.
 */

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Tüm kayıtları getirir
     */
    public function getAll($orderBy = null, $limit = null, $offset = null) {
        $sql = "SELECT * FROM " . $this->table;
        
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
     * ID'ye göre tek kayıt getirir
     */
    public function getById($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE " . $this->primaryKey . " = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Belirli bir koşula göre kayıtları getirir
     */
    public function getWhere($conditions, $values = [], $orderBy = null, $limit = null, $offset = null) {
        $sql = "SELECT * FROM " . $this->table . " WHERE " . $conditions;
        
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
        
        foreach ($values as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Toplam kayıt sayısını getirir
     */
    public function count($conditions = null, $values = []) {
        $sql = "SELECT COUNT(*) FROM " . $this->table;
        
        if ($conditions) {
            $sql .= " WHERE " . $conditions;
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($values && !empty($values)) {
            foreach ($values as $key => $value) {
                $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $paramType);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    /**
     * Yeni kayıt ekler
     */
    public function create($data) {
        $data = $this->filterData($data);
        
        if (empty($data)) {
            return false;
        }
        
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ':' . $field;
        }, $fields);
        
        $sql = "INSERT INTO " . $this->table . " (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $paramType);
        }
        
        $result = $stmt->execute();
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Mevcut kaydı günceller
     */
    public function update($id, $data) {
        $data = $this->filterData($data);
        
        if (empty($data)) {
            return false;
        }
        
        $setSql = [];
        foreach ($data as $field => $value) {
            $setSql[] = $field . ' = :' . $field;
        }
        
        $sql = "UPDATE " . $this->table . " SET " . implode(', ', $setSql) . " 
                WHERE " . $this->primaryKey . " = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        foreach ($data as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $paramType);
        }
        
        return $stmt->execute();
    }

    /**
     * Kaydı siler
     */
    public function delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE " . $this->primaryKey . " = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Doldurulabilir alanları filtreler
     */
    protected function filterData($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Özel SQL sorgusu çalıştırır
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }
        
        $stmt->execute();
        
        return $stmt;
    }
}
