<?php
/**
 * Emlak Arayış - API: Arayış Güncelle
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!isUserVerified()) {
    jsonResponse(['success' => false, 'message' => 'Yetkisiz erişim'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Geçersiz istek'], 405);
}

$id = (int) $_POST['id'];
$userId = $_SESSION['user_id'];

// Yetki kontrolü
$stmt = $pdo->prepare("SELECT id FROM searches WHERE id = ? AND user_id = ? AND status != 'deleted'");
$stmt->execute([$id, $userId]);
if (!$stmt->fetch()) {
    jsonResponse(['success' => false, 'message' => 'Arayış bulunamadı'], 404);
}

// Verileri al
$transactionType = sanitizeInput($_POST['transaction_type'] ?? '');
$propertyType = sanitizeInput($_POST['property_type'] ?? '');
$city = sanitizeInput($_POST['city'] ?? '');
$district = sanitizeInput($_POST['district'] ?? '');
$neighborhood = sanitizeInput($_POST['neighborhood'] ?? '');
$maxPrice = (float) str_replace('.', '', $_POST['max_price'] ?? '0');
$features = sanitizeInput($_POST['features'] ?? '');
$specialNote = sanitizeInput($_POST['special_note'] ?? '');

// Güncelle
try {
    $sql = "UPDATE searches SET 
            transaction_type = ?, 
            property_type = ?, 
            city = ?, 
            district = ?, 
            neighborhood = ?, 
            max_price = ?, 
            features = ?, 
            special_note = ?
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $transactionType,
        $propertyType,
        $city,
        $district,
        $neighborhood,
        $maxPrice,
        $features,
        $specialNote,
        $id
    ]);

    jsonResponse(['success' => true]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()], 500);
}
