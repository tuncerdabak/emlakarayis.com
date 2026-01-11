<?php
/**
 * Emlak Arayış - API: Admin Kullanıcı Oluştur
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

// Admin kontrolü
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Geçersiz istek'], 405);
}

// Verileri al
$agentName = sanitizeInput($_POST['agent_name'] ?? '');
$agencyName = sanitizeInput($_POST['agency_name'] ?? '');
$phone = formatPhone($_POST['phone'] ?? '');
$instagram = sanitizeInput($_POST['instagram'] ?? '');
$password = $_POST['password'] ?? '';

// Validasyon
if (empty($agentName) || empty($agencyName) || empty($phone)) {
    jsonResponse(['success' => false, 'message' => 'Lütfen zorunlu alanları doldurun'], 400);
}

// Şifre validasyonu (opsiyonel ama girilmişse min 6 karakter)
$passwordHash = null;
if (!empty($password)) {
    if (strlen($password) < 6) {
        jsonResponse(['success' => false, 'message' => 'Şifre en az 6 karakter olmalıdır'], 400);
    }
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
}

try {
    // Telefon kontrolü
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    if ($stmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Bu telefon numarası ile kayıtlı bir kullanıcı zaten var'], 400);
    }

    // Kullanıcıyı oluştur
    $sql = "INSERT INTO users (phone, agent_name, agency_name, instagram, password_hash, is_verified, is_active) VALUES (?, ?, ?, ?, ?, 1, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $phone,
        $agentName,
        $agencyName,
        $instagram,
        $passwordHash
    ]);

    $id = $pdo->lastInsertId();

    // Log (opsiyonel, gerekirse eklenebilir)

    jsonResponse(['success' => true, 'message' => 'Kullanıcı başarıyla oluşturuldu', 'id' => $id]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()], 500);
}

