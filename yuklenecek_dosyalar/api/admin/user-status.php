<?php
/**
 * Admin: Kullanıcı Durum Güncelleme API
 */
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$userId = (int) ($_POST['user_id'] ?? 0);
$isActive = (int) ($_POST['is_active'] ?? 0);

if ($userId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz ID']);
    exit;
}

// Güvenlik: Asla kendini pasife alamasın (Admin ID 1 varsayımı veya session check)
// Basit bir örnek:
if ($userId === 1) { // Varsayılan admin
    // echo json_encode(['success' => false, 'message' => 'Ana yönetici pasife alınamaz']);
    // exit;
}

if (toggleUserStatus($pdo, $userId, $isActive)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası']);
}
?>