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
    // Kullanıcı var mı bak
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if (!$user) {
        // Yeni kullanıcı oluştur (Direkt doğrulanmış olarak)
        $sql = "INSERT INTO users (phone, agent_name, agency_name, instagram, is_verified, is_active) VALUES (?, ?, ?, ?, 1, 1)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$phone, $name, $agency, $instagram]);

        $userId = $pdo->lastInsertId();

        // Kullanıcıyı tekrar çek (ID ve diğer bilgiler için)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
    } else {
        // Mevcut kullanıcıyı güncelle (belki adı değişmiştir) ve doğrula
        $sql = "UPDATE users SET agent_name = ?, agency_name = ?, instagram = ?, is_verified = 1, is_active = 1 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $agency, $instagram, $user['id']]);
    }

    // Oturum Aç (Login)
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['user_name'] = $name;
    $_SESSION['verified'] = true;

    // Yeni kayıt mı yoksa mevcut kullanıcı mı?
    // Eğer şifresi yoksa veya yeni açılmışsa profilini güncellemesi için profil sayfasına yönlendirelim
    $redirectUrl = empty($user['password']) || (isset($userId)) ? 'profil.php' : 'talep-gir.php';

    // Her ihtimale karşı doğrulama talebi de sisteme düşsün (admin izleyebilsin)
    $verificationCode = "AUTO"; // Otomatik doğrulandığını belirtir
    $sql = "INSERT INTO verification_requests (phone, agent_name, agency_name, instagram, verification_code, status) VALUES (?, ?, ?, ?, ?, 'approved')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$phone, $name, $agency, $instagram, $verificationCode]);

    jsonResponse([
        'success' => true,
        'message' => 'Kayıt başarılı, yönlendiriliyorsunuz...',
        'redirect' => $redirectUrl
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()], 500);
}
