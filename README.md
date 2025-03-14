# Venus IT Help Desk / Ticket Sistemi

Venus IT Help Desk sistemi, IT destek taleplerini yönetmek ve takip etmek için tasarlanmış modern bir web uygulamasıdır. MVC (Model-View-Controller) mimarisi üzerine kurulu olan bu sistem, kullanıcıların IT problemlerini raporlamasını, teknisyenlerin bu problemleri takip etmesini ve yöneticilerin süreci izlemesini sağlar.

## Proje Özellikleri

- Modern ve temiz kullanıcı arayüzü (TailwindCSS)
- Tam responsive tasarım (mobil, tablet ve masaüstü)
- Kapsamlı ticket oluşturma ve takip sistemi
  - Kategori ve alt kategori yönetimi
  - Öncelik seviyeleri
  - Durum takibi
  - Yorumlar ve aktivite günlüğü
- Çoklu kullanıcı rolleri
  - Son kullanıcı: Ticket oluşturma ve takip
  - Teknisyen: Ticketları çözme ve yönetme
  - Yönetici: Sistem konfigürasyonu ve raporlama
- Detaylı istatistikler ve raporlama araçları
  - Çözüm süreleri
  - Teknisyen performansı
  - Kategori bazlı analizler
- Zengin metin editörü (Quill.js) ile formatlanmış açıklamalar
- Gelişmiş dosya ekleme ve yönetim sistemi
- AJAX tabanlı form gönderimi ve sayfa yüklemeleri
- Güvenli oturum yönetimi ve yetkilendirme
- API endpointleri ile dış sistem entegrasyonu

## Teknik Mimari

Venus IT Help Desk, modern ve modüler bir yapıda geliştirilmiştir:

- **MVC Mimarisi**: Model-View-Controller desenini takip ederek kod organizasyonu
- **Modüler Yapı**: Her bir işlev kendi kontrolörü altında gruplandırılmış
- **URL Yönlendirme**: Temiz URL yapısı için .htaccess tabanlı SEO dostu yönlendirme
- **Veritabanı Soyutlama**: PDO kullanarak güvenli veritabanı işlemleri
- **Yardımcı Sınıflar**: Kimlik doğrulama, doğrulama ve dosya işlemleri için özel yardımcılar

## Kurulum Adımları

Projeyi çalıştırmak için aşağıdaki adımları takip edin:

### 1. Gereksinimleri Sağlayın

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- XAMPP, WAMP, LAMP veya benzeri bir yerel geliştirme ortamı

### 2. XAMPP'ı Başlatın

- XAMPP Control Panel'i açın
- Apache ve MySQL servislerini başlatın

### 3. Veritabanını Kurun

- PhpMyAdmin'e gidin (http://localhost/phpmyadmin)
- Yeni bir veritabanı oluşturun: `venus_it_desk`
- `database.sql` dosyasını import edin:
  - PhpMyAdmin'de `venus_it_desk` veritabanını seçin
  - "İçe Aktar" sekmesine tıklayın
  - "Dosya Seç" ile `database.sql` dosyasını seçin
  - "Git" butonuna tıklayın

### 4. Veritabanı Bağlantı Ayarlarını Yapılandırın

`config/database.php` dosyasını açın ve veritabanı bağlantı bilgilerinizi kontrol edin:

```php
private $host = 'localhost';
private $db_name = 'venus_it_desk';
private $username = 'root';
private $password = '';  // XAMPP için genellikle boştur
```

### 5. Proje Klasörlerini Oluşturun

Proje içindeki `create_directories.bat` dosyasını çalıştırın (Windows) veya aşağıdaki klasörlerin varlığından emin olun:

- `assets/uploads/`

Klasörlerin yazma izinlerinin olduğundan emin olun (Linux/Mac: `chmod 755 assets/uploads`).

### 6. Projeyi Tarayıcıda Açın

Web tarayıcınızdan şu adresi ziyaret edin: `http://localhost/venus_it_desk/`

### 7. Sisteme Giriş Yapın

Varsayılan hesaplarla giriş yapın:

**Yönetici Hesabı:**
- **Kullanıcı adı:** admin
- **Şifre:** admin123

**Teknisyen Hesabı:**
- **Kullanıcı adı:** technician
- **Şifre:** tech123

**Kullanıcı Hesabı:**
- **Kullanıcı adı:** user
- **Şifre:** user123

## Proje Yapısı

```
venus_it_desk/
├── assets/                  # Frontend kaynakları
│   ├── css/                 # Stil dosyaları
│   ├── js/                  # JavaScript dosyaları
│   └── uploads/             # Yüklenen dosyaların saklandığı yer
├── config/                  # Yapılandırma dosyaları
│   ├── config.php           # Genel uygulama yapılandırması
│   └── database.php         # Veritabanı bağlantı ayarları
├── controllers/             # Kontrolcüler (MVC'nin C kısmı)
│   ├── Controller.php       # Temel kontrolcü sınıfı
│   ├── AuthController.php   # Kimlik doğrulama kontrolcüsü
│   ├── DashboardController.php  # Dashboard kontrolcüsü
│   └── TicketController.php # Ticket işlemleri kontrolcüsü
├── helpers/                 # Yardımcı sınıflar
│   ├── AuthHelper.php       # Kimlik doğrulama yardımcısı
│   ├── ValidationHelper.php # Veri doğrulama yardımcısı
│   └── FileHelper.php       # Dosya işlemleri yardımcısı
├── models/                  # Veri modelleri (MVC'nin M kısmı)
│   ├── Model.php            # Temel model sınıfı
│   ├── User.php             # Kullanıcı modeli
│   ├── Ticket.php           # Ticket modeli
│   ├── Technician.php       # Teknisyen modeli
│   ├── ServiceCategory.php  # Hizmet kategorisi modeli
│   ├── SubCategory.php      # Alt kategori modeli
│   ├── StatusType.php       # Durum tipi modeli
│   └── ActivityLog.php      # Aktivite kaydı modeli
├── views/                   # Görünüm dosyaları (MVC'nin V kısmı)
│   ├── auth/                # Kimlik doğrulama görünümleri
│   ├── components/          # Yeniden kullanılabilir bileşenler
│   ├── dashboard/           # Dashboard görünümleri
│   ├── layouts/             # Sayfa şablonları
│   └── tickets/             # Ticket görünümleri
├── api/                     # API endpointleri
├── .htaccess                # URL yönlendirme kuralları
├── create_directories.bat   # Gerekli dizinleri oluşturan batch dosyası
├── index.php                # Ana giriş noktası ve router
└── database.sql             # Veritabanı şeması ve örnek veriler
```

## Kullanıcı Rolleri ve Yetenekleri

### Kullanıcı (Son Kullanıcı)
- Kendi ticket'larını oluşturabilir ve takip edebilir
- Mevcut ticket'lara yorum ekleyebilir
- Dosya ekleyebilir
- Kendi profilini düzenleyebilir

### Teknisyen
- Kendisine atanan ticket'ları görebilir
- Ticket durumunu güncelleyebilir
- Ticket'lara cevap verebilir
- Ticket'ları kapatabilir
- Teknisyen dashboard'ına erişebilir

### Yönetici
- Tüm ticket'ları görebilir ve yönetebilir
- Teknisyen atayabilir
- Kategori/alt kategori yönetimi yapabilir
- Kullanıcı hesaplarını yönetebilir
- Raporlama araçlarına erişebilir
- Sistem ayarlarını değiştirebilir

## Arayüzler

- **Dashboard**: İstatistikler, son aktiviteler ve özet bilgiler
- **Ticket Listeleme**: Filtreleme ve sıralama özellikleriyle
- **Ticket Detay**: Tüm mesajlar, dosyalar ve durum güncellemeleri
- **Ticket Oluşturma**: Dinamik kategori/alt kategori seçimi
- **Profil Yönetimi**: Kullanıcı bilgileri ve şifre değiştirme
- **Yönetim Paneli**: Sistem yapılandırması (sadece yöneticiler için)

## Geliştirme

Eğer projeyi geliştirmek isterseniz:

1. Yerel bir kod editörü kullanarak (VS Code, PHPStorm vb.) projeyi açın
2. Gerekli değişiklikleri yapın
3. Değişikliklerinizi test edin (Apache'yi yeniden başlatmak genellikle gerekli değildir)
4. Tarayıcınızda sayfayı yenileyin

## API Kullanımı

Sistem, harici uygulamalarla entegrasyon için bir dizi API endpoint'i sağlar:

- `api/auth/login`: Oturum açma ve token alma
- `api/tickets/subcategories`: Bir kategoriye ait alt kategorileri alma
- `api/tickets/status`: Ticket durumunu güncelleme
- `api/tickets/assign`: Ticket'a teknisyen atama

Tüm API çağrıları JSON formatında yanıt verir.

## Sorun Giderme

**Veritabanı Bağlantı Hatası:**
- Veritabanı adının doğru olduğunu kontrol edin
- Kullanıcı adı ve şifrenin doğru olduğunu kontrol edin
- MySQL servisinin çalıştığından emin olun

**Dosya Yükleme Sorunları:**
- `assets/uploads` dizininizin yazılabilir olduğundan emin olun (chmod 755 veya 775)
- PHP'nin dosya yükleme limitlerini kontrol edin (php.ini'deki upload_max_filesize ve post_max_size)

**404 Hatası:**
- `.htaccess` dosyasının doğru yapılandırıldığından emin olun
- Apache'nin mod_rewrite modülünün etkin olduğundan emin olun

**Boş Sayfa Hatası:**
- PHP hata günlüklerini kontrol edin (XAMPP/logs/php_error_log)
- display_errors'ı geçici olarak aktifleştirin (php.ini veya .htaccess ile)

## Güvenlik Notları

- Varsayılan şifreleri değiştirmeyi unutmayın
- Üretim ortamında config.php içindeki DEBUG değerini false olarak ayarlayın
- Düzenli olarak güvenlik güncellemelerini kontrol edin
- Hassas bilgileri içeren dosyaları .htaccess ile koruyun

## Lisans

Bu proje açık kaynaklıdır ve MIT lisansı altında lisanslanmıştır.

## İletişim ve Destek

Soru, öneri veya katkılarınız için GitHub üzerinden issue açabilir veya pull request gönderebilirsiniz.
