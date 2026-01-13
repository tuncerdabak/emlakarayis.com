# Eski WhatsApp Doğrulama Akışı (Legacy Verification Flow)

Bu döküman, sistemin ilk aşamalarında kullanılan ve yönetici onayına dayalı WhatsApp doğrulama akışını yedeklemek amacıyla oluşturulmuştur.

## Akış Özeti
1. Kullanıcı `giris.php` sayfasında isim ve telefon numarasını girer.
2. Form gönderildiğinde `api/dogrulama-talebi.php` çalışır:
    - Veritabanında bir `verification_requests` kaydı oluşturulur.
    - 6 haneli rastgele bir `verification_code` üretilir.
    - Yöneticiye gönderilmek üzere WhatsApp linki oluşturulur.
3. Kullanıcı butona tıkladığında WhatsApp'a yönlendirilir ve kodu içeren mesajı yöneticiye iletir.
4. Yönetici kodu admin panelinden görür ve kullanıcıya iletir (veya manuel onaylar).
5. Kullanıcı aldığı kodu `giris.php` (veya `dogrulama.php`) ekranına girerek hesabını doğrular.

## Kritik Kod Parçaları

### 1. API - Doğrulama Talebi Oluşturma (`api/dogrulama-talebi.php`)
```php
// Doğrulama kodu üret
$verificationCode = generateVerificationCode();

// Talebi kaydet
$sql = "INSERT INTO verification_requests (phone, agent_name, agency_name, instagram, verification_code) VALUES (?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$phone, $name, $agency, $instagram, $verificationCode]);

// WhatsApp mesajı ve linki
$message = adminVerificationMessage($name, $phone, $agency, $instagram, $verificationCode);
$whatsappUrl = whatsappLink(ADMIN_WHATSAPP, $message);
```

### 2. Frontend - Form ve Yönlendirme (`giris.php`)
```javascript
// Kod giriş ekranına geçiş
function showCodeEntry() {
    document.getElementById('verifyForm').classList.add('hidden');
    document.getElementById('codeEntrySection').classList.remove('hidden');
}

// WhatsApp linkine yönlendirme (Eski akışta JS ile yapılıyordu)
if (result.success && result.whatsappUrl) {
    window.open(result.whatsappUrl, '_blank');
    showCodeEntry();
}
```

### 3. Veritabanı Şeması (`db.sql`)
```sql
CREATE TABLE `verification_requests` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `phone` VARCHAR(20) NOT NULL,
    `agent_name` VARCHAR(100) NOT NULL,
    `verification_code` VARCHAR(6) DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    -- ...
);
```

---
*Not: Bu yapı, kullanıcı deneyimini hızlandırmak amacıyla Ocak 2026'da basitleştirilmiş ("Yıldırmayan Kayıt") akışı ile değiştirilmiştir.*
