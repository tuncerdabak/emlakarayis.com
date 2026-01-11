<?php
/**
 * Emlak Arayış - API: Kod Doğrulama & Giriş
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

$phone = formatPhone($_POST['phone'] ?? '');
$code = sanitizeInput($_POST['code'] ?? '');

if (empty($phone) || empty($code)) {
    jsonResponse(['success' => false, 'message' => 'Eksik bilgi'], 400);
}

try {
    // 1. Talebi bul (Verification Request)
    $stmt = $pdo->prepare("SELECT * FROM verification_requests WHERE phone = ? AND verification_code = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$phone, $code]);
    $request = $stmt->fetch();

    if (!$request) {
        // Belki kullanıcı daha önce doğrulanmıştı ve tekrar giriş yapıyor?
        // Bu versiyonda sadece talep tablosundan doğrulama yapıyoruz.
        jsonResponse(['success' => false, 'message' => 'Geçersiz kod veya telefon numarası'], 400);
    }

    // 2. Talebi onayla
    $updateStmt = $pdo->prepare("UPDATE verification_requests SET status = 'approved', processed_at = NOW() WHERE id = ?");
    $updateStmt->execute([$request['id']]);

    // 3. Kullanıcıyı users tablosuna ekle veya güncelle
    // Önce var mı kontrol et
    $userStmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $userStmt->execute([$phone]);
    $existingUser = $userStmt->fetch();

    if ($existingUser) {
        // Kullanıcı aktif mi kontrol et
        if ($existingUser['is_active'] == 0) {
            jsonResponse(['success' => false, 'message' => 'Hesabınız pasife alınmıştır. Lütfen yönetici ile iletişime geçin.'], 403);
        }

        $userId = $existingUser['id'];
        // Bilgileri güncelle
        $pdo->prepare("UPDATE users SET is_verified = 1, agent_name = ?, agency_name = ?, instagram = ? WHERE id = ?")
            ->execute([$request['agent_name'], $request['agency_name'], $request['instagram'], $userId]);
    } else {
        // Yeni kullanıcı oluştur
        $pdo->prepare("INSERT INTO users (phone, agent_name, agency_name, instagram, is_verified, is_active) VALUES (?, ?, ?, ?, 1, 1)")
            ->execute([$phone, $request['agent_name'], $request['agency_name'], $request['instagram']]);
        $userId = $pdo->lastInsertId();
    }

    // 4. Session Başlat
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_phone'] = $phone;
    $_SESSION['user_name'] = $request['agent_name'];
    $_SESSION['verified'] = true;

    // Beni Hatırla: Token oluştur ve kaydet
    try {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        // DB'ye kaydet
        $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?")->execute([$hashedToken, $userId]);

        // Cookie ayarla (30 gün)
        // Secure flag: HTTPS varsa true
        $secure = isset($_SERVER['HTTPS']);
        setcookie('remember_me', $userId . ':' . $token, time() + (86400 * 30), '/', '', $secure, true);
    } catch (Exception $e) {
        // Token hatası oturum açmayı engellememeli
        error_log("Remember Me Error: " . $e->getMessage());
    }

    session_write_close(); // Ensure session is saved before output
    jsonResponse(['success' => true]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Sistem hatası'], 500);
}
