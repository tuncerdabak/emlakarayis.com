<?php
/**
 * Emlak Arayış - API: Şifre Güncelleme
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Geçersiz istek'], 405);
}

// Oturum kontrolü
if (!isUserVerified()) {
    jsonResponse(['success' => false, 'message' => 'Oturum açmanız gerekiyor'], 401);
}

$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$currentPassword = $_POST['current_password'] ?? '';

// Şifre validasyonu
if (empty($newPassword) || empty($confirmPassword)) {
    jsonResponse(['success' => false, 'message' => 'Şifre alanları boş bırakılamaz'], 400);
}

if (strlen($newPassword) < 6) {
    jsonResponse(['success' => false, 'message' => 'Şifre en az 6 karakter olmalıdır'], 400);
}

if ($newPassword !== $confirmPassword) {
    jsonResponse(['success' => false, 'message' => 'Şifreler eşleşmiyor'], 400);
}

try {
    $userId = $_SESSION['user_id'];

    // Eğer kullanıcının mevcut şifresi varsa, eski şifreyi doğrula
    if (userHasPassword($pdo, $userId)) {
        if (empty($currentPassword)) {
            jsonResponse(['success' => false, 'message' => 'Mevcut şifrenizi girmeniz gerekiyor'], 400);
        }

        // Mevcut şifreyi kontrol et
        $stmt = $pdo->prepare("SELECT password_hash, phone FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!password_verify($currentPassword, $user['password_hash'])) {
            jsonResponse(['success' => false, 'message' => 'Mevcut şifreniz hatalı'], 400);
        }
    }

    // Şifreyi güncelle
    if (updateUserPassword($pdo, $userId, $newPassword)) {
        jsonResponse(['success' => true, 'message' => 'Şifreniz başarıyla güncellendi']);
    } else {
        jsonResponse(['success' => false, 'message' => 'Şifre güncellenirken bir hata oluştu'], 500);
    }

} catch (PDOException $e) {
    error_log("Password Update Error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Sistem hatası'], 500);
}
