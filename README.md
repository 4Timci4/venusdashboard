# Venus IT Help Desk / Ticket Sistemi

Venus IT Help Desk, IT destek taleplerini yönetmek için geliştirilmiş modern bir web uygulamasıdır. MVC mimarisi kullanan sistem, kullanıcıların IT sorunlarını raporlamasını ve takip etmesini sağlar.

## Özellikler

- TailwindCSS ile modern kullanıcı arayüzü
- Tam responsive tasarım
- Ticket yönetim sistemi (kategori, öncelik, durum takibi)
- 3 kullanıcı rolü: son kullanıcı, teknisyen, yönetici
- İstatistikler ve raporlama
- Zengin metin editörü ile açıklamalar
- Dosya ekleme sistemi

## Teknik Yapı

- MVC mimarisi
- Modüler yapı
- SEO dostu URL yönlendirme
- PDO ile güvenli veritabanı işlemleri

## Kurulum

1. **Gereksinimler**
   - PHP 7.4+
   - MySQL 5.7+
   - XAMPP, WAMP veya benzer ortam

2. **XAMPP'ı Başlatın**
   - Apache ve MySQL servislerini çalıştırın

3. **Veritabanı Kurulumu**
   - PhpMyAdmin'de `venus_it_desk` veritabanı oluşturun
   - `database.sql` dosyasını içe aktarın

4. **Konfigürasyon**
   - `config/database.php` dosyasındaki bağlantı bilgilerini kontrol edin

5. **Projeyi Tarayıcıda Açın**
   - `http://localhost/venus_it_desk/`

6. **Giriş Bilgileri**
   - Yönetici: admin / admin123
   - Teknisyen: technician / tech123
   - Kullanıcı: user / user123

## Proje Yapısı

```
venus_it_desk/
├── assets/          # CSS, JS ve yüklenen dosyalar
├── config/          # Yapılandırma dosyaları 
├── controllers/     # MVC kontrolcüleri
├── helpers/         # Yardımcı sınıflar
├── models/          # Veri modelleri
├── views/           # Görünüm dosyaları
├── api/             # API endpointleri
└── index.php        # Ana giriş noktası
```

## Kullanıcı Rolleri

- **Kullanıcı:** Ticket oluşturma, takip etme, yorum ekleme
- **Teknisyen:** Ticket çözme, durum güncelleme, cevap verme
- **Yönetici:** Tüm sistemi yönetme, raporlama, ayarları değiştirme

## Sorun Giderme

- Veritabanı bağlantı sorunu: Bağlantı bilgilerini ve MySQL servisini kontrol edin
- Dosya yükleme sorunu: `assets/uploads` klasörünün yazma izinlerini kontrol edin
- 404 hatası: `.htaccess` dosyasının ve mod_rewrite modülünün doğru yapılandırıldığından emin olun

## Güvenlik Notları

- Varsayılan şifreleri değiştirin
- Üretim ortamında DEBUG modunu kapatın

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır.
