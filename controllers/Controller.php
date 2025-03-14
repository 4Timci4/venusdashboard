<?php
/**
 * Venus IT Help Desk - Temel Kontrolcü Sınıfı
 * 
 * Tüm kontrolcüler için temel fonksiyonları içerir
 */

abstract class Controller {
    /**
     * View dosyasını çağırır ve verileri aktarır
     * 
     * @param string $view Görünüm dosyası yolu
     * @param array $data Görünüme aktarılacak veriler
     */
    protected function view($view, $data = []) {
        // Veri değişkenlerini çıkart
        extract($data);
        
        // View dosyasının tam yolunu belirle
        $viewPath = BASE_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            // Çıktı tamponunu başlat
            ob_start();
            
            // View dosyasını dahil et
            include $viewPath;
            
            // Tamponlanmış çıktıyı al ve temizle
            $content = ob_get_clean();
            
            // Eğer layout bilgisi varsa, layoutu kullan
            if (isset($layout)) {
                $layoutPath = BASE_PATH . '/views/layouts/' . $layout . '.php';
                
                if (file_exists($layoutPath)) {
                    include $layoutPath;
                } else {
                    echo $content;
                }
            } else {
                echo $content;
            }
        } else {
            // View dosyası bulunamadıysa hata göster
            echo "View dosyası bulunamadı: " . $viewPath;
        }
    }
    
    /**
     * JSON formatında veri döndürür
     * 
     * @param array $data JSON'a çevrilecek veri
     * @param int $status HTTP durum kodu
     */
    protected function json($data, $status = 200) {
        // HTTP durum kodunu ayarla
        http_response_code($status);
        
        // JSON başlığını ayarla
        header('Content-Type: application/json');
        
        // Veriyi JSON formatına çevir ve gönder
        echo json_encode($data);
        exit;
    }
    
    /**
     * Form verilerini alır ve temizler
     * 
     * @param array $fields Alınacak form alanları
     * @param string $method HTTP metodu (GET veya POST)
     * @return array Temizlenmiş form verileri
     */
    protected function getFormData($fields = [], $method = 'POST') {
        $data = [];
        $source = $method === 'POST' ? $_POST : $_GET;
        
        if (empty($fields)) {
            // Tüm verileri al
            return ValidationHelper::sanitize($source);
        }
        
        // Belirtilen alanları al
        foreach ($fields as $field) {
            $data[$field] = isset($source[$field]) ? $source[$field] : null;
        }
        
        return ValidationHelper::sanitize($data);
    }
    
    /**
     * Başka bir URL'ye yönlendirir
     * 
     * @param string $url Yönlendirilecek URL
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Oturum mesajı oluşturur
     * 
     * @param string $key Mesaj anahtarı
     * @param string $message Mesaj içeriği
     * @param string $type Mesaj tipi (success, error, warning, info)
     */
    protected function setFlashMessage($key, $message, $type = 'info') {
        $_SESSION['flash_messages'][$key] = [
            'message' => $message,
            'type' => $type
        ];
    }
    
    /**
     * Oturum mesajını alır ve siler
     * 
     * @param string $key Mesaj anahtarı
     * @return array|null Mesaj varsa dizi, yoksa null
     */
    protected function getFlashMessage($key) {
        if (isset($_SESSION['flash_messages'][$key])) {
            $message = $_SESSION['flash_messages'][$key];
            unset($_SESSION['flash_messages'][$key]);
            return $message;
        }
        
        return null;
    }
    
    /**
     * Tüm oturum mesajlarını alır
     * 
     * @param bool $clear Mesajları silsin mi
     * @return array Tüm flash mesajlar
     */
    protected function getAllFlashMessages($clear = true) {
        $messages = isset($_SESSION['flash_messages']) ? $_SESSION['flash_messages'] : [];
        
        if ($clear) {
            $_SESSION['flash_messages'] = [];
        }
        
        return $messages;
    }
    
    /**
     * CSRF token kontrolü yapar
     * 
     * @param string $token Kontrol edilecek token
     * @return bool Token geçerli ise true, değilse false
     */
    protected function validateCsrfToken($token) {
        return AuthHelper::validateCsrfToken($token);
    }
    
    /**
     * CSRF token değerini alır
     * 
     * @return string CSRF token
     */
    protected function getCsrfToken() {
        return AuthHelper::getCsrfToken();
    }
}
