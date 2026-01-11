# Emlak Arayış Platformu: Teknik Analiz ve İyileştirme Önerileri

**Yazar:** Manus AI
**Tarih:** 11 Ocak 2026

## 1. Giriş

Bu rapor, `emlakarayis.com` platformunun kaynak kodları (PHP) ve veritabanı şeması (MySQL) incelenerek hazırlanmıştır. Amacı, mevcut teknik altyapıyı değerlendirmek, tespit edilen sorunları ve eksiklikleri detaylandırmak ve platformun güvenlik, performans, ölçeklenebilirlik ve sürdürülebilirlik açısından iyileştirilmesi için somut teknik öneriler sunmaktır.

## 2. Projeye Genel Bakış

Emlak Arayış, emlak danışmanları arasında müşteri taleplerini eşleştirmeyi hedefleyen, talep odaklı bir platformdur. Proje, PHP ve MySQL kullanılarak geliştirilmiştir. Sağlanan dosyalar, bir yönetim paneli (`admin/`), API uç noktaları (`api/`), yardımcı fonksiyonlar (`includes/functions.php`) ve ana sayfa ile kullanıcı arayüzü dosyalarını içermektedir. Platformun temel iş akışı, kullanıcıların telefon numarası ve admin onayı ile doğrulandıktan sonra müşteri arayışlarını sisteme girmesi ve diğer danışmanların bu arayışları görmesi üzerine kuruludur.

## 3. Teknik Analiz

### 3.1. Veritabanı Yapısı

`tuncerda_emlak_arayis.sql` dosyası incelendiğinde aşağıdaki tablolar ve temel yapıları tespit edilmiştir:

*   **`admins`**: Yönetim paneli kullanıcıları için `id`, `username`, `password` (hashed), `created_at` alanlarını içerir.
*   **`users`**: Emlak danışmanlarının bilgilerini saklar. `id`, `phone`, `password_hash` (opsiyonel), `agent_name`, `agency_name`, `city`, `district`, `instagram`, `is_verified`, `is_active`, `remember_token`, `created_at` gibi alanlara sahiptir. `password_hash` alanı bazı kullanıcılar için `NULL` olarak görünmektedir, bu da OTP tabanlı girişin öncelikli olduğunu düşündürmektedir.
*   **`searches`**: Müşteri arayışlarını tutar. `id`, `user_id` (users tablosuna referans), `transaction_type` (ENUM: 'satilik', 'kiralik'), `property_type` (ENUM: çeşitli mülk tipleri), `city`, `district`, `neighborhood`, `max_price` (DECIMAL(15,2)), `features`, `special_note`, `duration_days`, `status` (ENUM: 'active', 'expired', 'closed'), `expires_at`, `created_at` alanlarını içerir.
*   **`verification_requests`**: Kullanıcı doğrulama taleplerini yönetir. `id`, `phone`, `agent_name`, `agency_name`, `instagram`, `verification_code`, `status` (ENUM: 'pending', 'approved', 'rejected'), `admin_note`, `created_at`, `processed_at` alanlarına sahiptir.

**Gözlemler:**
*   Veri tipleri genel olarak doğru seçilmiştir (örn. `max_price` için `DECIMAL`, `transaction_type` ve `property_type` için `ENUM`).
*   Tablolar arasında `user_id` ile temel bir ilişki kurulmuştur, ancak veritabanı seviyesinde Foreign Key kısıtlamaları tanımlanmamıştır.
*   Coğrafi bilgiler (`city`, `district`, `neighborhood`) string olarak tutulmaktadır. Bu, ileride daha karmaşık coğrafi sorgular ve performans için bir kısıtlama oluşturabilir.

### 3.2. Kod Yapısı ve Mimarisi

Proje, modern bir framework kullanmak yerine düz PHP (procedural) yaklaşımıyla geliştirilmiştir. Ana dizinde çeşitli `.php` dosyaları bulunmakta ve bunlar `require_once` ile `config.php` ve `includes/functions.php` dosyalarını içermektedir.

*   **`config.php`**: Veritabanı bağlantı bilgileri, site ayarları, WhatsApp admin numarası, renk paleti, mülk ve işlem tipleri gibi global sabitleri ve değişkenleri tanımlar. `PDO` ile veritabanı bağlantısı burada kurulur.
*   **`includes/functions.php`**: HTML escape (`e()`), tarih/saat formatlama (`timeAgo()`, `remainingTime()`), fiyat formatlama (`formatPrice()`), telefon numarası formatlama (`formatPhone()`), WhatsApp link oluşturma (`whatsappLink()`), admin ve kullanıcı mesajları oluşturma, doğrulama kodu üretme (`generateVerificationCode()`), kullanıcı/admin oturum kontrolü (`isUserVerified()`, `isAdminLoggedIn()`), JSON yanıt gönderme (`jsonResponse()`), CSRF token oluşturma/doğrulama ve input temizleme (`sanitizeInput()`) gibi çeşitli yardımcı fonksiyonları barındırır. Ayrıca, veritabanından kullanıcı ve arayış verilerini çeken fonksiyonlar da buradadır.
*   **`api/` Dizini**: AJAX isteklerini karşılayan PHP dosyalarını içerir. Örnek olarak `arayis-ekle.php`, `dogrulama-talebi.php`, `kod-dogrula.php` gibi dosyalar bulunmaktadır.
*   **`admin/` Dizini**: Yönetim paneli işlevselliğini barındırır. Kullanıcı ve arayış yönetimi gibi temel admin görevleri için dosyalar mevcuttur.

### 3.3. Kimlik Doğrulama ve Doğrulama Akışı

Platform, emlak danışmanları için iki tür giriş mekanizması sunmaktadır: OTP (Tek Kullanımlık Şifre) ile giriş ve Şifre ile giriş. Ancak, ana akış OTP tabanlı görünmektedir.

**OTP Akışı:**
1.  Kullanıcı `giris.php` sayfasında ad soyad, telefon numarası, ofis adı ve Instagram bilgileriyle bir doğrulama talebi gönderir.
2.  `api/dogrulama-talebi.php` bu bilgileri `verification_requests` tablosuna `pending` durumuyla kaydeder.
3.  Sistem, `adminVerificationMessage()` fonksiyonu ile admin için bir WhatsApp mesajı oluşturur ve kullanıcıya bu mesajı içeren bir WhatsApp linki döndürür. Bu, adminin manuel olarak doğrulamayı yapması ve kodu kullanıcıya iletmesi gerektiği anlamına gelir.
4.  Kullanıcı, admin tarafından iletilen kodu `giris.php` sayfasındaki ilgili alana girer.
5.  `api/kod-dogrula.php` bu kodu ve telefon numarasını `verification_requests` tablosunda kontrol eder. Eğer eşleşen bir `pending` talep bulunursa, talebin durumunu `approved` olarak günceller.
6.  Kullanıcı `users` tablosunda yoksa yeni bir kayıt oluşturulur, varsa bilgileri güncellenir ve `is_verified` alanı `1` yapılır.
7.  Kullanıcı için PHP session'ı başlatılır ve `remember_me` çerezi ayarlanır.

**Gözlemler:**
*   OTP akışı yarı otomatiktir. Adminin manuel müdahalesi gerektirmesi, platformun ölçeklenebilirliği açısından ciddi bir darboğazdır.
*   `dogrulama-talebi.php` dosyasında `verification_code` üretilip `verification_requests` tablosuna kaydedilmemektedir. Bu, `kod-dogrula.php` dosyasının nasıl çalıştığı konusunda bir tutarsızlık yaratmaktadır. Muhtemelen `verification_code` admin tarafından manuel olarak belirlenmekte veya sistemde eksik bir parça bulunmaktadır.
*   `users` tablosundaki `password_hash` alanı, şifre ile giriş seçeneği sunulmasına rağmen bazı kullanıcılarda `NULL` olabilir. Bu, şifre ile giriş yapacak kullanıcıların şifre belirleme mekanizmasının eksik veya isteğe bağlı olduğunu gösterir.

### 3.4. Güvenlik Önlemleri

*   **SQL Injection Koruması**: `PDO` kullanılarak prepared statement'lar (`$pdo->prepare()->execute()`) ile sorgular çalıştırılmaktadır. Bu, SQL Injection saldırılarına karşı temel bir koruma sağlar.
*   **XSS Koruması**: `e()` fonksiyonu (`htmlspecialchars`) ve `sanitizeInput()` fonksiyonu (`strip_tags`, `trim`) ile çıktı ve girdi temizliği yapılmaktadır. Bu, XSS saldırılarına karşı bir miktar koruma sağlar.
*   **Şifre Güvenliği**: Admin şifreleri için `password_hash` kullanıldığı görülmektedir. `users` tablosunda da `password_hash` alanı mevcuttur, bu da kullanıcı şifrelerinin güvenli bir şekilde saklanabileceğini gösterir.
*   **CSRF Koruması**: `generateCSRFToken()` ve `validateCSRFToken()` fonksiyonları tanımlanmıştır, ancak API uç noktalarında (örn. `arayis-ekle.php`, `dogrulama-talebi.php`) bu token'ların kontrol edildiğine dair açık bir kullanım görülmemiştir.
*   **Session Yönetimi**: `session_set_cookie_params` ile `httponly` ve `secure` bayrakları ayarlanmıştır, bu da session güvenliğini artırır.
*   **Hata Raporlama**: `config.php` içinde `error_reporting(E_ALL);` ve `ini_set('display_errors', 1);` ayarları üretim ortamı için risk teşkil etmektedir. Hata mesajlarının doğrudan kullanıcıya gösterilmesi güvenlik zafiyetlerine yol açabilir.

### 3.5. Performans ve Ölçeklenebilirlik

*   **Veritabanı Sorguları**: `functions.php` içindeki `getActiveSearches()` gibi fonksiyonlar, `JOIN` ve `WHERE` koşulları ile filtreleme yapmaktadır. Ancak, `searches` ve `users` tablolarında uygun indekslerin olup olmadığı SQL dosyasında belirtilmemiştir. Büyük veri setlerinde bu sorgular yavaşlayabilir.
*   **Düz PHP Yapısı**: Prosedürel kod yapısı, uygulamanın büyümesiyle birlikte yönetimi ve bakımı zorlaştırabilir. Kod tekrarı ve bağımlılıkların artması performans ve ölçeklenebilirlik sorunlarına yol açabilir.
*   **Manuel Doğrulama**: Adminin manuel doğrulama süreci, kullanıcı sayısının artmasıyla birlikte ciddi bir darboğaz oluşturacaktır.

## 4. Tespit Edilen Sorunlar ve Eksiklikler

### 4.1. Fonksiyonel Eksiklikler (Web Sitesi İncelemesine Göre)

*   **Çalışmayan Navigasyon**: Ana sayfadaki menü linkleri (`/arayislar`, `/sss`, `/yardim`) ve footer'daki linkler 404 hatası vermektedir. Bu, platformun henüz tamamlanmadığı izlenimini yaratmaktadır.
*   **Talep Giriş Formu**: `talep-gir.php` dosyası mevcut olmasına rağmen, ana sayfadaki 
 "Hemen Talep Gir" butonu henüz işlevsel değildir veya bir form sayfasına yönlendirme yapmamaktadır (ilk incelemede). Kod incelendiğinde `talep-gir.php` sayfasına yönlendirme olduğu görülmüştür, ancak bu sayfanın içeriği ve işlevselliği test edilememiştir.
*   **Arayış Listeleme ve Detay Sayfaları**: `arayislar.php` dosyası mevcut ancak ilk incelemede 404 hatası vermiştir. Bu, arayışların listelendiği ve detaylarının görüntülendiği sayfaların henüz tam olarak çalışmadığını göstermektedir.
*   **Kullanıcı Profili Yönetimi**: `profil.php` dosyası mevcut ancak kullanıcıların kendi profillerini düzenleyebileceği, geçmiş arayışlarını görebileceği bir arayüzün işlevselliği test edilememiştir.

### 4.2. Güvenlik Açıkları ve Zafiyetler

*   **OTP Otomasyon Eksikliği ve Manuel Doğrulama**: `api/dogrulama-talebi.php` dosyasında `verification_code` üretilip `verification_requests` tablosuna kaydedilmemektedir. Bu, `api/kod-dogrula.php` dosyasının çalışması için gerekli olan kodun eksik olduğu anlamına gelir. Mevcut durumda, adminin manuel olarak bir kod üretip kullanıcıya iletmesi gerekmektedir ki bu, güvenlik zafiyetlerine (insan hatası, kodun tahmin edilebilirliği) ve ölçeklenebilirlik sorunlarına yol açar.
*   **CSRF Korumasının Eksik Kullanımı**: `generateCSRFToken()` ve `validateCSRFToken()` fonksiyonları tanımlanmış olmasına rağmen, kritik API uç noktalarında (örn. `api/arayis-ekle.php`, `api/dogrulama-talebi.php`, `api/kod-dogrula.php`) bu token'ların kontrolü yapılmamaktadır. Bu durum, Cross-Site Request Forgery (CSRF) saldırılarına karşı platformu savunmasız bırakır.
*   **Hata Mesajlarının Gösterimi**: `config.php` dosyasında `display_errors` ayarının açık olması, üretim ortamında hassas sistem bilgilerinin (veritabanı hataları, dosya yolları vb.) saldırganlara ifşa edilmesine neden olabilir. Bu, güvenlik açısından ciddi bir risktir.
*   **Telefon Numarası Doğrulama Zafiyeti**: `api/dogrulama-talebi.php` içinde aynı telefon numarasıyla mükerrer doğrulama talebi gönderilmesi durumunda zayıf bir kontrol mekanizması bulunmaktadır. Bu, potansiyel olarak spam veya hizmet reddi (DoS) saldırılarına yol açabilir.
*   **Admin Paneli Güvenliği**: Admin paneli girişinde kullanılan şifre hashlenmiş olsa da, admin paneline erişim kontrolü ve yetkilendirme mekanizmalarının detayları incelenmelidir. Ayrıca, admin paneli URL'sinin tahmin edilebilir olması (örn. `/admin`) bir zafiyet olabilir.

### 4.3. Performans ve Ölçeklenebilirlik Sorunları

*   **Veritabanı İndeksleme Eksikliği**: `searches` ve `users` tablolarında `WHERE` koşullarında sıkça kullanılan alanlar (örn. `phone`, `user_id`, `city`, `district`, `status`, `expires_at`) için uygun indekslerin tanımlanmamış olması, veritabanı sorgularının büyük veri setlerinde yavaşlamasına neden olabilir.
*   **Düz PHP Mimarisi**: Prosedürel PHP kodu, uygulamanın büyümesiyle birlikte kod tekrarına, bakımı zorlaştırmaya ve yeni özellik eklemeyi karmaşık hale getirmeye meyillidir. Bu durum, uygulamanın performansını ve ölçeklenebilirliğini olumsuz etkileyebilir.
*   **Manuel Doğrulama Süreci**: Kullanıcı doğrulama sürecinin adminin manuel müdahalesine dayanması, platformun kullanıcı sayısının artması durumunda ciddi bir darboğaz oluşturacaktır. Bu, hem kullanıcı deneyimini olumsuz etkiler hem de operasyonel maliyetleri artırır.
*   **Coğrafi Veri Yönetimi**: Şehir, ilçe, mahalle gibi coğrafi verilerin string olarak tutulması ve sorgulanması, performans açısından verimsiz olabilir. Daha yapılandırılmış bir coğrafi veri modeli (örn. ayrı tablolar ve ilişkiler) daha iyi performans sağlayabilir.

## 5. İyileştirme Önerileri

Platformun uzun vadeli başarısı ve sürdürülebilirliği için aşağıdaki teknik iyileştirme önerileri sunulmaktadır:

### 5.1. Güvenlik İyileştirmeleri

*   **Otomatik OTP Entegrasyonu**: Adminin manuel müdahalesini ortadan kaldırmak için bir SMS API sağlayıcısı (örn. Twilio, Netgsm, İleti Merkezi) ile entegrasyon yapılmalıdır. `api/dogrulama-talebi.php` dosyasında `generateVerificationCode()` fonksiyonu çağrılmalı, üretilen kod `verification_requests` tablosuna kaydedilmeli ve SMS ile kullanıcıya gönderilmelidir. Bu, hem güvenliği artırır hem de kullanıcı deneyimini hızlandırır.
*   **Kapsamlı CSRF Koruması**: Tüm POST istekleri için CSRF token kontrolü zorunlu hale getirilmelidir. Formlarda gizli bir input alanı olarak CSRF token eklenmeli ve her API çağrısında bu token sunucu tarafında doğrulanmalıdır.
*   **Hata Yönetimi ve Loglama**: Üretim ortamında `display_errors` kapatılmalı ve hatalar güvenli bir şekilde loglanmalıdır. Hata mesajları kullanıcıya genel ve bilgilendirici bir şekilde sunulmalıdır (örn. "Bir hata oluştu, lütfen daha sonra tekrar deneyin.").
*   **Rate Limiting Uygulaması**: Özellikle doğrulama kodu talepleri ve giriş denemeleri için IP bazlı veya kullanıcı bazlı istek sınırlamaları (rate limiting) uygulanmalıdır. Bu, DoS ve brute-force saldırılarına karşı koruma sağlar.
*   **Admin Paneli Güvenliği**: Admin paneli için daha güçlü kimlik doğrulama mekanizmaları (örn. iki faktörlü kimlik doğrulama) ve yetkilendirme kontrolleri eklenmelidir. Admin paneli URL'si varsayılan `/admin` yerine daha karmaşık bir yola taşınabilir.
*   **Input Doğrulama ve Temizleme**: Mevcut `sanitizeInput()` fonksiyonu iyi bir başlangıç olsa da, her input için veri tipine özel ve daha katı doğrulama kuralları uygulanmalıdır. Örneğin, telefon numaraları için regex tabanlı doğrulama, fiyatlar için sayısal doğrulama gibi.

### 5.2. Performans ve Ölçeklenebilirlik İyileştirmeleri

*   **Veritabanı İndeksleme**: `users` tablosunda `phone`, `is_active`, `remember_token` alanlarına, `searches` tablosunda ise `user_id`, `transaction_type`, `property_type`, `city`, `district`, `status`, `expires_at` alanlarına indeksler eklenmelidir. Bu, sorgu performansını önemli ölçüde artıracaktır.
*   **Modern PHP Framework Kullanımı**: Projenin uzun vadeli sürdürülebilirliği ve ölçeklenebilirliği için Laravel, Symfony veya CodeIgniter gibi modern bir PHP framework'üne geçiş yapılması şiddetle tavsiye edilir. Bu framework'ler, MVC (Model-View-Controller) mimarisi, ORM (Object-Relational Mapping), routing, middleware, caching ve daha birçok gelişmiş özellik sunarak geliştirme sürecini hızlandırır ve kod kalitesini artırır.
*   **API Mimarisi**: Mevcut API uç noktaları, RESTful prensiplere uygun hale getirilmeli ve daha tutarlı bir yapıya kavuşturulmalıdır. Her API isteği için kimlik doğrulama ve yetkilendirme kontrolleri uygulanmalıdır.
*   **Coğrafi Veri Optimizasyonu**: Türkiye'deki şehir, ilçe ve mahalle verileri için ayrı tablolar oluşturularak ilişkisel bir yapı kurulabilir. Bu, coğrafi sorguların daha verimli çalışmasını sağlar ve veri tutarlılığını artırır.
*   **Caching Mekanizmaları**: Sık erişilen ancak nadiren değişen veriler (örn. şehir listeleri, mülk tipleri) için caching mekanizmaları (örn. Redis, Memcached) kullanılabilir. Bu, veritabanı yükünü azaltır ve sayfa yükleme sürelerini iyileştirir.

### 5.3. Kod Kalitesi ve Bakım İyileştirmeleri

*   **Modüler Yapı**: Kod, daha küçük, yönetilebilir modüllere veya sınıflara ayrılmalıdır. Bu, kod tekrarını azaltır ve her bir bileşenin bağımsız olarak test edilmesini sağlar.
*   **Otomatik Testler**: Birim testleri ve entegrasyon testleri yazılması, yeni özellik eklerken veya mevcut kodu değiştirirken hataların erken tespit edilmesine yardımcı olur.
*   **Dokümantasyon**: Kod içi yorumlar ve harici dokümantasyon (API dokümantasyonu, kurulum kılavuzu) geliştirme sürecini kolaylaştırır.
*   **Versiyon Kontrolü**: Git gibi bir versiyon kontrol sistemi kullanılarak kod değişiklikleri takip edilmeli ve ekip çalışması kolaylaştırılmalıdır.

## 6. Sonuç

Emlak Arayış platformu, emlak sektöründe yenilikçi bir yaklaşıma sahip değerli bir fikri temsil etmektedir. Mevcut haliyle bir tanıtım sayfasından öteye geçemese de, sağlanan kaynak kodları ve veritabanı şeması, platformun temel işlevselliklerinin geliştirilmeye başlandığını göstermektedir. Yukarıda detaylandırılan teknik iyileştirme önerileri, platformun güvenlik, performans ve ölçeklenebilirlik açısından sağlam bir temele oturmasını sağlayacak ve uzun vadede başarılı bir ürün haline gelmesine katkıda bulunacaktır. Özellikle otomatik OTP entegrasyonu ve modern bir PHP framework'üne geçiş, projenin geleceği için kritik adımlardır.
