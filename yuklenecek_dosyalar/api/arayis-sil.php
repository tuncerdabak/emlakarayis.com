<?php
/**
 * Emlak Arayış - API: Arayış Sil
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

try {
    // Arayışın bu kullanıcıya ait olup olmadığını kontrol et
    $stmt = $pdo->prepare("SELECT id FROM searches WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);

    if (!$stmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Arayış bulunamadı veya size ait değil'], 404);
    }

    // Sil (aslında status='deleted' yapıyoruz, soft delete)
    $deleteStmt = $pdo->prepare("UPDATE searches SET status = 'deleted' WHERE id = ?");
    $deleteStmt->execute([$id]);

    jsonResponse(['success' => true]);

} catch (PDOException $e) {
    jsonResponse(['success' => false, 'message' => 'Veritabanı hatası'], 500);
}
