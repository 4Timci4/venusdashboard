# Venus IT Help Desk / Ticket Sistemi

Venus IT Help Desk sistemi, IT destek taleplerini yönetmek ve takip etmek için tasarlanmış modern bir web uygulamasıdır.

## Proje Özellikleri

- Modern ve temiz kullanıcı arayüzü (TailwindCSS)
- Responsive tasarım
- Ticket oluşturma ve takip sistemi
- Kullanıcı, teknisyen ve yönetici rolleri
- Detaylı istatistikler ve raporlama
- Zengin metin editörü (Quill.js)
- Dosya ekleme sistemi
- AJAX form gönderimi
- Oturum yönetimi ve güvenlik

## Kurulum Adımları

Projeyi çalıştırmak için aşağıdaki adımları takip edin:

### 1. XAMPP'ı Başlatın

- XAMPP Control Panel'i açın
- Apache ve MySQL servislerini başlatın

### 2. Veritabanını Kurun

- PhpMyAdmin'e gidin (http://localhost/phpmyadmin)
- Yeni bir veritabanı oluşturun: `venus_it_desk`
- `database.sql` dosyasını import edin:
  - PhpMyAdmin'de `venus_it_desk` veritabanını seçin
  - "İçe Aktar" sekmesine tıklayın
  - "Dosya Seç" ile `database.sql` dosyasını seçin
  - "Git" butonuna tıklayın

### 3. Veritabanı Bağlantı Ayarlarını Yapılandırın

`config/database.php` dosyasını açın ve veritabanı bağlantı bilgilerinizi kontrol edin:

```php
private $host = 'localhost';
private $db_name = 'venus_it_desk';
private $username = 'root';
private $password = '';  // XAMPP için genellikle boştur
```

### 4. Projeyi Tarayıcıda Açın

Web tarayıcınızdan şu adresi ziyaret edin: `http://localhost/venus_it_desk/`

### 5. Sisteme Giriş Yapın

Varsayılan admin hesabıyla giriş yapın:

- **Kullanıcı adı:** admin
- **Şifre:** admin123

## Proje Yapısı

```
venus_it_desk/
├── assets/              # CSS, JS ve yüklenen dosyaları içerir
├── config/              # Yapılandırma dosyaları
├── controllers/         # Kontrolcü sınıflar
├── helpers/             # Yardımcı sınıflar
├── models/              # Veritabanı model sınıfları
├── views/               # Görünüm dosyaları
├── api/                 # API endpointleri
├── .htaccess            # URL yönlendirmeleri
├── index.php            # Ana giriş noktası
└── database.sql         # Veritabanı şeması
```

## Geliştirme

Eğer projeyi geliştirmek isterseniz:

1. Gerekli değişiklikleri yapın
2. Apache'yi yeniden başlatın (genellikle gerekli değildir)
3. Tarayıcınızda sayfayı yenileyin

## Sorun Giderme

**Veritabanı Bağlantı Hatası:**
- Veritabanı adının doğru olduğunu kontrol edin
- Kullanıcı adı ve şifrenin doğru olduğunu kontrol edin
- MySQL servisinin çalıştığından emin olun

**Dosya Yükleme Sorunları:**
- `assets/uploads` dizininizin yazılabilir olduğundan emin olun (chmod 755 veya 775)

**404 Hatası:**
- `.htaccess` dosyasının doğru yapılandırıldığından emin olun
- Apache'nin mod_rewrite modülünün etkin olduğundan emin olun

## Lisans

Bu proje açık kaynaklıdır ve MIT lisansı altında lisanslanmıştır.
