<?php
/**
 * Venus IT Help Desk - Doğrulama Yardımcı Sınıfı
 * 
 * Form verilerinin doğrulanması ve güvenli hale getirilmesi için yardımcı fonksiyonlar
 */

class ValidationHelper {
    /**
     * Girdi verisini temizler (XSS koruması için)
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }
        
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Boş veya null değil mi kontrol eder
     */
    public static function required($value) {
        if (is_array($value)) {
            return !empty($value);
        }
        
        return isset($value) && trim($value) !== '';
    }
    
    /**
     * E-posta formatı geçerli mi kontrol eder
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Tam sayı mı kontrol eder
     */
    public static function integer($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
    
    /**
     * Pozitif tam sayı mı kontrol eder
     */
    public static function positiveInteger($value) {
        return self::integer($value) && $value > 0;
    }
    
    /**
     * Minimum uzunluk kontrolü
     */
    public static function minLength($value, $min) {
        return mb_strlen($value, 'UTF-8') >= $min;
    }
    
    /**
     * Maksimum uzunluk kontrolü
     */
    public static function maxLength($value, $max) {
        return mb_strlen($value, 'UTF-8') <= $max;
    }
    
    /**
     * İki değer eşleşiyor mu kontrol eder
     */
    public static function match($value1, $value2) {
        return $value1 === $value2;
    }
    
    /**
     * Alfanumerik mi kontrol eder
     */
    public static function alphanumeric($value) {
        return ctype_alnum($value);
    }
    
    /**
     * Düzgün bir kullanıcı adı mı kontrol eder (harfler, rakamlar, alt çizgi)
     */
    public static function username($value) {
        return preg_match('/^[a-zA-Z0-9_]+$/', $value);
    }
    
    /**
     * Güçlü şifre mi kontrol eder
     * (minimum 8 karakter, en az bir harf ve bir rakam)
     */
    public static function strongPassword($value) {
        return mb_strlen($value, 'UTF-8') >= 8 && 
               preg_match('/[A-Za-z]/', $value) && 
               preg_match('/[0-9]/', $value);
    }
    
    /**
     * Dosya boyutu maksimum limiti aşmıyor mu kontrol eder
     */
    public static function fileSize($fileSize, $maxSize) {
        return $fileSize <= $maxSize;
    }
    
    /**
     * Dosya uzantısı izin verilenlerde mi kontrol eder
     */
    public static function fileExtension($fileName, $allowedExtensions) {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedArray = explode(',', $allowedExtensions);
        
        foreach ($allowedArray as &$ext) {
            $ext = strtolower(trim($ext));
        }
        
        return in_array($extension, $allowedArray);
    }
    
    /**
     * Url formatı geçerli mi kontrol eder
     */
    public static function url($value) {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Tarih formatı geçerli mi kontrol eder (YYYY-MM-DD)
     */
    public static function date($value) {
        $date = date_parse_from_format('Y-m-d', $value);
        return $date['error_count'] === 0 && $date['warning_count'] === 0;
    }
    
    /**
     * Tarih-saat formatı geçerli mi kontrol eder (YYYY-MM-DD HH:MM:SS)
     */
    public static function datetime($value) {
        $date = date_parse_from_format('Y-m-d H:i:s', $value);
        return $date['error_count'] === 0 && $date['warning_count'] === 0;
    }
    
    /**
     * Veri dizisini doğrular
     * 
     * @param array $data Doğrulanacak veriler
     * @param array $rules Doğrulama kuralları
     * @return array Hata mesajları dizisi, hata yoksa boş dizi
     */
    public static function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = isset($data[$field]) ? $data[$field] : null;
            
            foreach ($fieldRules as $rule => $ruleValue) {
                // 'required' kuralı
                if ($rule === 'required' && $ruleValue && !self::required($value)) {
                    $errors[$field] = 'Bu alan gereklidir.';
                    break;
                }
                
                // Eğer gerekli değilse ve değer boşsa, diğer kuralları atla
                if (($rule !== 'required' || !$ruleValue) && !self::required($value)) {
                    continue;
                }
                
                // Diğer kurallar
                if ($rule === 'email' && $ruleValue && !self::email($value)) {
                    $errors[$field] = 'Geçerli bir e-posta adresi giriniz.';
                    break;
                }
                
                if ($rule === 'integer' && $ruleValue && !self::integer($value)) {
                    $errors[$field] = 'Tam sayı olmalıdır.';
                    break;
                }
                
                if ($rule === 'positiveInteger' && $ruleValue && !self::positiveInteger($value)) {
                    $errors[$field] = 'Pozitif tam sayı olmalıdır.';
                    break;
                }
                
                if ($rule === 'minLength' && !self::minLength($value, $ruleValue)) {
                    $errors[$field] = 'En az ' . $ruleValue . ' karakter olmalıdır.';
                    break;
                }
                
                if ($rule === 'maxLength' && !self::maxLength($value, $ruleValue)) {
                    $errors[$field] = 'En fazla ' . $ruleValue . ' karakter olmalıdır.';
                    break;
                }
                
                if ($rule === 'match' && !self::match($value, $data[$ruleValue])) {
                    $errors[$field] = 'Değerler eşleşmiyor.';
                    break;
                }
                
                if ($rule === 'alphanumeric' && $ruleValue && !self::alphanumeric($value)) {
                    $errors[$field] = 'Sadece harfler ve rakamlar kullanılabilir.';
                    break;
                }
                
                if ($rule === 'username' && $ruleValue && !self::username($value)) {
                    $errors[$field] = 'Kullanıcı adı sadece harf, rakam ve alt çizgi içerebilir.';
                    break;
                }
                
                if ($rule === 'strongPassword' && $ruleValue && !self::strongPassword($value)) {
                    $errors[$field] = 'Şifre en az 8 karakter olmalı ve en az bir harf ile bir rakam içermelidir.';
                    break;
                }
                
                if ($rule === 'url' && $ruleValue && !self::url($value)) {
                    $errors[$field] = 'Geçerli bir URL giriniz.';
                    break;
                }
                
                if ($rule === 'date' && $ruleValue && !self::date($value)) {
                    $errors[$field] = 'Geçerli bir tarih giriniz (YYYY-MM-DD).';
                    break;
                }
                
                if ($rule === 'datetime' && $ruleValue && !self::datetime($value)) {
                    $errors[$field] = 'Geçerli bir tarih-saat giriniz (YYYY-MM-DD HH:MM:SS).';
                    break;
                }
                
                if ($rule === 'custom' && is_callable($ruleValue)) {
                    $result = call_user_func($ruleValue, $value);
                    if ($result !== true) {
                        $errors[$field] = $result;
                        break;
                    }
                }
            }
        }
        
        return $errors;
    }
}
