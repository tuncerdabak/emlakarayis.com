<?php
/**
 * Emlak Arayış - API: Şifre ile Giriş
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Geçersiz istek'], 405);
}

$phone = formatPhone($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($phone) || empty($password)) {
    jsonResponse(['success' => false, 'message' => 'Telefon ve şifre gereklidir'], 400);
}

try {
    // Şifre ile doğrula
    $user = verifyUserPassword($pdo, $phone, $password);

    if (!$user) {
        jsonResponse(['success' => false, 'message' => 'Telefon veya şifre hatalı'], 401);
    }

    // Session Başlat
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['user_name'] = $user['agent_name'];
    $_SESSION['verified'] = true;

    // Beni Hatırla: Token oluştur ve kaydet
    try {
        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);

        // DB'ye kaydet
        $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?")->execute([$hashedToken, $user['id']]);

        // Cookie ayarla (30 gün)
        $secure = isset($_SERVER['HTTPS']);
        setcookie('remember_me', $user['id'] . ':' . $token, time() + (86400 * 30), '/', '', $secure, true);
    } catch (Exception $e) {
        error_log("Remember Me Error: " . $e->getMessage());
    }

    session_write_close();
    jsonResponse(['success' => true]);

} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    jsonResponse(['success' => false, 'message' => 'Sistem hatası'], 500);
}
