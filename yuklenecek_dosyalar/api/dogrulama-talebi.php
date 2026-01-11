<?php
/**
 * Emlak Arayış - API: Doğrulama Talebi
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Geçersiz istek'], 405);
}

// CSRF Kontrolü
if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
    jsonResponse(['success' => false, 'message' => 'Güvenlik doğrulaması başarısız (CSRF)'], 403);
}

// Verileri al
$name = sanitizeInput($_POST['name'] ?? '');
$phone = formatPhone($_POST['phone'] ?? ''); // sadece rakamlar formata uygun
$agency = sanitizeInput($_POST['agency'] ?? '');
$instagram = sanitizeInput($_POST['instagram'] ?? '');

if (empty($name) || empty($phone)) {
    jsonResponse(['success' => false, 'message' => 'Ad Soyad ve Telefon zorunludur'], 400);
}

try {
    // Önce kullanıcı users tablosunda var mı bak (daha önce doğrulanmış mı?)
    $stmt = $pdo->prepare("SELECT id, is_verified FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $existingUser = $stmt->fetch();

    if ($existingUser && $existingUser['is_verified']) {
        // Zaten doğrulanmış, direkt oturum açılabilir ama burada akış gereği kullanıcıyı uyarmak yerine
        // belki direkt kod göndermek mantıklı olabilir. 
        // Şimdilik yeni talep oluşturmayalım, admin panelinde karışıklık olmasın.
        // Kullanıcıya "Zaten kayıtlısınız, giriş ekranından devam edin" diyebilirdik ama burada talep oluşturuyoruz.
    }

    // Doğrulama kodu üret
    $verificationCode = generateVerificationCode();

    // Talebi kaydet
    $sql = "INSERT INTO verification_requests (phone, agent_name, agency_name, instagram, verification_code) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$phone, $name, $agency, $instagram, $verificationCode]);

    // Admin için WhatsApp mesajı oluştur
    $message = adminVerificationMessage($name, $phone, $agency, $instagram, $verificationCode);

    // WhatsApp linki döndür
    $whatsappUrl = whatsappLink(ADMIN_WHATSAPP, $message);

    jsonResponse(['success' => true, 'whatsappUrl' => $whatsappUrl]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Veritabanı hatası'], 500);
}
