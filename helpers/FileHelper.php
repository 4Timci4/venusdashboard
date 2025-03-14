<?php
/**
 * Venus IT Help Desk - Dosya Yardımcı Sınıfı
 * 
 * Dosya yükleme ve dosya işlemleri için yardımcı fonksiyonlar
 */

class FileHelper {
    /**
     * Dosya yükler ve güvenli bir isim oluşturur
     * 
     * @param array $file $_FILES dizisinden bir dosya
     * @param string $targetDir Hedef dizin
     * @param string $filePrefix Dosya adı prefixi
     * @param array $allowedTypes İzin verilen dosya uzantıları dizisi
     * @param int $maxSize İzin verilen maksimum dosya boyutu
     * @return array|false Başarılı olursa dosya bilgilerini içeren dizi, başarısız olursa false
     */
    public static function uploadFile($file, $targetDir, $filePrefix = '', $allowedTypes = null, $maxSize = null) {
        // Dosya yüklenmiş mi kontrol et
        if (!isset($file) || !is_array($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Dosya uzantısı kontrolü
        if ($allowedTypes === null) {
            $allowedTypes = explode(',', ALLOWED_EXTENSIONS);
        }
        
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedTypes)) {
            return false;
        }
        
        // Dosya boyutu kontrolü
        if ($maxSize === null) {
            $maxSize = MAX_FILE_SIZE;
        }
        
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        // Hedef dizini kontrol et ve oluştur
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // Güvenli dosya adı oluştur
        $safeName = $filePrefix . uniqid() . '_' . time() . '.' . $fileExtension;
        $targetPath = $targetDir . '/' . $safeName;
        
        // Dosyayı taşı
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                'name' => $file['name'], // Orijinal dosya adı
                'path' => $targetPath,   // Sunucudaki tam yol
                'filename' => $safeName, // Yeni dosya adı
                'type' => $file['type'], // MIME tipi
                'size' => $file['size'], // Boyut (byte)
                'extension' => $fileExtension
            ];
        }
        
        return false;
    }
    
    /**
     * Birden fazla dosya yükler
     * 
     * @param array $files $_FILES dizisi (çoklu dosya)
     * @param string $targetDir Hedef dizin
     * @param string $filePrefix Dosya adı prefixi
     * @param array $allowedTypes İzin verilen dosya uzantıları dizisi
     * @param int $maxSize İzin verilen maksimum dosya boyutu
     * @return array Başarıyla yüklenen dosyaların bilgilerini içeren dizi
     */
    public static function uploadMultipleFiles($files, $targetDir, $filePrefix = '', $allowedTypes = null, $maxSize = null) {
        $uploadedFiles = [];
        
        // Tekli dosya yükleme için yeniden düzenle
        if (isset($files['name']) && !is_array($files['name'])) {
            return [self::uploadFile($files, $targetDir, $filePrefix, $allowedTypes, $maxSize)];
        }
        
        // Çoklu dosya yükleme için dizileri yeniden düzenle
        for ($i = 0; $i < count($files['name']); $i++) {
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            $result = self::uploadFile($file, $targetDir, $filePrefix, $allowedTypes, $maxSize);
            
            if ($result !== false) {
                $uploadedFiles[] = $result;
            }
        }
        
        return $uploadedFiles;
    }
    
    /**
     * Dosyayı siler
     * 
     * @param string $filePath Silinecek dosyanın yolu
     * @return bool İşlem başarılı olursa true, başarısız olursa false
     */
    public static function deleteFile($filePath) {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }
    
    /**
     * Dosya boyutunu okunabilir formatta döndürür
     * 
     * @param int $bytes Byte cinsinden dosya boyutu
     * @param int $decimals Ondalık basamak sayısı
     * @return string Okunabilir dosya boyutu (örn. "1.5 MB")
     */
    public static function formatFileSize($bytes, $decimals = 2) {
        $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        
        return sprintf("%.{$decimals}f %s", $bytes / pow(1024, $factor), $size[$factor]);
    }
    
    /**
     * Dosya MIME tipine göre ikon döndürür
     * 
     * @param string $filename Dosya adı veya uzantısı
     * @return string Font Awesome ikon sınıfı
     */
    public static function getFileIcon($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $iconMap = [
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'ppt' => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
            'txt' => 'fa-file-alt',
            'zip' => 'fa-file-archive',
            'rar' => 'fa-file-archive',
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'gif' => 'fa-file-image',
            'mp3' => 'fa-file-audio',
            'wav' => 'fa-file-audio',
            'mp4' => 'fa-file-video',
            'avi' => 'fa-file-video',
            'mov' => 'fa-file-video',
            'html' => 'fa-file-code',
            'css' => 'fa-file-code',
            'js' => 'fa-file-code',
            'php' => 'fa-file-code'
        ];
        
        return isset($iconMap[$extension]) ? $iconMap[$extension] : 'fa-file';
    }
    
    /**
     * Dosya uzantısından MIME tipini tahmin eder
     * 
     * @param string $filename Dosya adı
     * @return string MIME tipi
     */
    public static function getMimeType($filename) {
        $mimeTypes = [
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'wmv' => 'video/x-ms-wmv',
            
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];
        
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return isset($mimeTypes[$ext]) ? $mimeTypes[$ext] : 'application/octet-stream';
    }
}
