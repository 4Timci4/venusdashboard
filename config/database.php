<?php
/**
 * Venus IT Help Desk - Veritabanı Bağlantı Sınıfı
 * 
 * PDO kullanarak güvenli veritabanı bağlantısı sağlar
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'venus_it_desk';
    private $username = 'root';
    private $password = '';
    private $conn;
    private static $instance = null;

    // Singleton pattern
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function __construct() {
        // Private constructor for singleton
    }

    // Veritabanı bağlantısını sağlar
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch(PDOException $e) {
            echo "Veritabanı bağlantı hatası: " . $e->getMessage();
        }

        return $this->conn;
    }

    // Prepared statement hazırlar
    public function prepare($sql) {
        return $this->getConnection()->prepare($sql);
    }

    // Transaction işlemleri
    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }

    public function commit() {
        return $this->getConnection()->commit();
    }

    public function rollback() {
        return $this->getConnection()->rollback();
    }

    // Son eklenen ID
    public function lastInsertId() {
        return $this->getConnection()->lastInsertId();
    }
}
