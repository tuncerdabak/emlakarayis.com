<?php
/**
 * Emlak Arayış - Admin API: Talep Onayla
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if (!isAdminLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Yetkisiz erişim'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Geçersiz istek'], 405);
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(['success' => false, 'message' => 'Geçersiz ID'], 400);
}

try {
    // 1. Talebi bul
    $stmt = $pdo->prepare("SELECT * FROM verification_requests WHERE id = ?");
    $stmt->execute([$id]);
    $request = $stmt->fetch();

    if (!$request) {
        jsonResponse(['success' => false, 'message' => 'Talep bulunamadı'], 404);
    }

    // 2. Kod oluştur
    $code = generateVerificationCode();

    // 3. Kodu kaydet
    $updateStmt = $pdo->prepare("UPDATE verification_requests SET verification_code = ? WHERE id = ?");
    $updateStmt->execute([$code, $id]);

    // 4. WhatsApp mesajı linki
    $message = verificationCodeMessage($code);
    $whatsappUrl = whatsappLink($request['phone'], $message);

    jsonResponse([
        'success' => true,
        'code' => $code,
        'whatsappUrl' => $whatsappUrl
    ]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Veritabanı hatası'], 500);
}
