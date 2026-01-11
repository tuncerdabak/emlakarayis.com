<?php
/**
 * Emlak Arayış - API: Admin Talep Oluştur (Kullanıcı Adına)
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Geçersiz istek'], 405);
}

// Verileri al ve temizle
$userId = (int) ($_POST['user_id'] ?? 0);
$transactionType = sanitizeInput($_POST['transaction_type'] ?? '');
$propertyType = sanitizeInput($_POST['property_type'] ?? '');
$city = sanitizeInput($_POST['city'] ?? '');
$district = sanitizeInput($_POST['district'] ?? '');
$neighborhood = sanitizeInput($_POST['neighborhood'] ?? '');
$maxPrice = (float) str_replace('.', '', $_POST['max_price'] ?? '0');
$features = sanitizeInput($_POST['features'] ?? '');
$specialNote = sanitizeInput($_POST['special_note'] ?? '');
$durationDays = (int) ($_POST['duration'] ?? 7);

// Validasyon
if ($userId <= 0 || empty($transactionType) || empty($propertyType) || empty($city) || empty($district) || $maxPrice <= 0 || empty($features)) {
    jsonResponse(['success' => false, 'message' => 'Lütfen zorunlu alanları doldurun'], 400);
}

// Kullanıcı var mı kontrol et
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$userId]);
if (!$stmt->fetch()) {
    jsonResponse(['success' => false, 'message' => 'Geçersiz kullanıcı'], 400);
}

// Bitiş tarihi hesapla
$expiresAt = date('Y-m-d H:i:s', strtotime("+$durationDays days"));

// Veritabanına ekle
try {
    $sql = "INSERT INTO searches (user_id, transaction_type, property_type, city, district, neighborhood, max_price, features, special_note, duration_days, expires_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $userId,
        $transactionType,
        $propertyType,
        $city,
        $district,
        $neighborhood,
        $maxPrice,
        $features,
        $specialNote,
        $durationDays,
        $expiresAt
    ]);

    $id = $pdo->lastInsertId();

    jsonResponse(['success' => true, 'message' => 'Arayış başarıyla eklendi', 'id' => $id]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()], 500);
}
