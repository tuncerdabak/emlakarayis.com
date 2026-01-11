<?php
/**
 * Emlak Arayış - Yardımcı Fonksiyonlar
 */

/**
 * Güvenli çıktı için HTML escape
 */
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Tarih formatlama (Türkçe, göreceli)
 */
function timeAgo($datetime)
{
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0)
        return $diff->y . ' yıl önce';
    if ($diff->m > 0)
        return $diff->m . ' ay önce';
    if ($diff->d > 0)
        return $diff->d . ' gün önce';
    if ($diff->h > 0)
        return $diff->h . ' saat önce';
    if ($diff->i > 0)
        return $diff->i . ' dk önce';
    return 'Az önce';
}

/**
 * Kalan süre hesaplama
 */
function remainingTime($expiresAt)
{
    $now = new DateTime();
    $expires = new DateTime($expiresAt);

    if ($expires < $now)
        return 'Süresi doldu';

    $diff = $now->diff($expires);

    if ($diff->d > 0)
        return $diff->d . ' Gün Kaldı';
    if ($diff->h > 0)
        return $diff->h . ' Saat Kaldı';
    return $diff->i . ' Dakika Kaldı';
}

/**
 * Fiyat formatlama (Türk Lirası)
 */
function formatPrice($price)
{
    return number_format($price, 0, ',', '.') . ' ₺';
}

/**
 * Telefon numarası formatlama
 */
function formatPhone($phone)
{
    // Sadece rakamları al
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // 0 ile başlıyorsa kaldır
    if (substr($phone, 0, 1) === '0') {
        $phone = substr($phone, 1);
    }

    // 90 ile başlamıyorsa ekle
    if (substr($phone, 0, 2) !== '90') {
        $phone = '90' . $phone;
    }

    return $phone;
}

/**
 * WhatsApp link oluşturucu
 */
function whatsappLink($phone, $message = '')
{
    $phone = formatPhone($phone);
    $url = "https://wa.me/{$phone}";

    if (!empty($message)) {
        $url .= "?text=" . urlencode($message);
    }

    return $url;
}

/**
 * Admin'e gönderilecek WhatsApp mesajı
 */
function adminVerificationMessage($name, $phone, $agency = '', $instagram = '', $code = '')
{
    $message = "🏠 *Emlak Arayış - Doğrulama Talebi*\n\n";
    $message .= "📋 *Bilgiler:*\n";
    $message .= "👤 İsim: {$name}\n";
    $message .= "📱 Telefon: {$phone}\n";

    if (!empty($agency)) {
        $message .= "🏢 Ofis: {$agency}\n";
    }

    if (!empty($instagram)) {
        $message .= "📸 Instagram: {$instagram}\n";
    }

    if (!empty($code)) {
        $message .= "\n🔑 *DOĞRULAMA KODU: {$code}*\n";
        $message .= "_(Kullanıcıya bu kodu iletin)_";
    }

    $message .= "\n\n✅ Onay Bekleniyor";

    return $message;
}

/**
 * Kullanıcıya gönderilecek doğrulama kodu mesajı
 */
function verificationCodeMessage($code)
{
    $message = "🏠 *Emlak Arayış*\n\n";
    $message .= "Doğrulama kodunuz: *{$code}*\n\n";
    $message .= "Bu kodu siteye girerek hesabınızı doğrulayabilirsiniz.";

    return $message;
}

/**
 * 6 haneli rastgele kod oluştur
 */
function generateVerificationCode()
{
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Mülk tipi bilgilerini al
 */
function getPropertyTypeInfo($type)
{
    global $PROPERTY_TYPES;
    return $PROPERTY_TYPES[$type] ?? $PROPERTY_TYPES['daire'];
}

/**
 * İşlem tipi bilgilerini al
 */
function getTransactionTypeInfo($type)
{
    global $TRANSACTION_TYPES;
    return $TRANSACTION_TYPES[$type] ?? $TRANSACTION_TYPES['satilik'];
}

/**
 * Kullanıcı doğrulanmış mı kontrol et
 */
function isUserVerified()
{
    if (isset($_SESSION['user_id']) && !empty($_SESSION['verified'])) {
        return true;
    }
    return checkRememberCookie();
}

/**
 * Beni Hatırla çerezi kontrolü
 */
function checkRememberCookie()
{
    global $pdo;

    if (!isset($_COOKIE['remember_me'])) {
        return false;
    }

    $parts = explode(':', $_COOKIE['remember_me']);
    if (count($parts) !== 2) {
        return false;
    }

    list($userId, $token) = $parts;

    if (!$userId || !$token) {
        return false;
    }

    try {
        // Kullanıcıyı ve token'ı getir
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user && $user['remember_token'] && hash_equals($user['remember_token'], hash('sha256', $token))) {
            // Oturum aç
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['user_name'] = $user['agent_name'];
            $_SESSION['verified'] = true;
            return true;
        }
    } catch (PDOException $e) {
        // DB hatası durumunda sessizce başarısız ol
        return false;
    }

    return false;
}

/**
 * Kullanıcı bilgilerini session'dan al
 */
function getCurrentUser()
{
    if (!isUserVerified())
        return null;

    return [
        'id' => $_SESSION['user_id'],
        'phone' => $_SESSION['user_phone'] ?? '',
        'name' => $_SESSION['user_name'] ?? ''
    ];
}

/**
 * Admin giriş kontrolü
 */
function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Admin yetkisi gerektir
 */
function requireAdmin()
{
    if (!isAdminLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * JSON response gönder
 */
function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * CSRF Token oluştur
 */
/**
 * CSRF Token oluştur
 */
function generateCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function getAllUsers($pdo)
{
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function toggleUserStatus($pdo, $userId, $status)
{
    $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
    return $stmt->execute([$status, $userId]);
}

function checkUserActive($pdo, $userId)
{
    $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    return $user && $user['is_active'] == 1;
}


/**
 * CSRF Token doğrula
 */
function validateCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Input temizleme
 */
function sanitizeInput($input)
{
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return trim(strip_tags($input));
}

/**
 * Şehir bazlı aktif arayış sayılarını getir
 */
function getRequestsCountByCity($pdo)
{
    $sql = "SELECT city, COUNT(*) as count 
            FROM searches 
            WHERE status = 'active' AND expires_at > NOW() 
            GROUP BY city";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
}

/**
 * Aktif arayışları getir
 */
function getActiveSearches($pdo, $limit = 10, $offset = 0, $filters = [])
{
    $sql = "SELECT s.*, u.agent_name, u.agency_name, u.phone 
            FROM searches s 
            JOIN users u ON s.user_id = u.id 
            WHERE s.status = 'active' AND s.expires_at > NOW()";

    $params = [];

    if (!empty($filters['transaction_type'])) {
        $sql .= " AND s.transaction_type = ?";
        $params[] = $filters['transaction_type'];
    }

    if (!empty($filters['property_type'])) {
        $sql .= " AND s.property_type = ?";
        $params[] = $filters['property_type'];
    }

    if (!empty($filters['city'])) {
        $sql .= " AND s.city LIKE ?";
        $params[] = '%' . $filters['city'] . '%';
    }

    if (!empty($filters['search'])) {
        $sql .= " AND (s.city LIKE ? OR s.district LIKE ? OR s.neighborhood LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    if (!empty($filters['id'])) {
        $sql .= " AND s.id = ?";
        $params[] = $filters['id'];
    }

    $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/**
 * Toplam aktif arayış sayısı
 */
function getTotalActiveSearches($pdo, $filters = [])
{
    $sql = "SELECT COUNT(*) FROM searches s WHERE s.status = 'active' AND s.expires_at > NOW()";

    $params = [];

    if (!empty($filters['transaction_type'])) {
        $sql .= " AND s.transaction_type = ?";
        $params[] = $filters['transaction_type'];
    }

    if (!empty($filters['property_type'])) {
        $sql .= " AND s.property_type = ?";
        $params[] = $filters['property_type'];
    }

    if (!empty($filters['city'])) {
        $sql .= " AND s.city LIKE ?";
        $params[] = '%' . $filters['city'] . '%';
    }

    if (!empty($filters['id'])) {
        $sql .= " AND s.id = ?";
        $params[] = $filters['id'];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}

/**
 * Bekleyen doğrulama taleplerini getir
 */
function getPendingVerifications($pdo)
{
    $stmt = $pdo->query("SELECT * FROM verification_requests WHERE status = 'pending' ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

/**
 * Kullanıcı Bilgilerini Güncelle
 */
function updateUser($pdo, $userId, $data)
{
    // Önce kullanıcıyı bul
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user)
        return false;

    // Güncellenecek alanlar
    $fields = [];
    $params = [];

    // İsim
    if (isset($data['agent_name'])) {
        $fields[] = "agent_name = ?";
        $params[] = sanitizeInput($data['agent_name']);
    }

    // Ofis Adı
    if (isset($data['agency_name'])) {
        $fields[] = "agency_name = ?";
        $params[] = sanitizeInput($data['agency_name']);
    }

    // İl
    if (isset($data['city'])) {
        $fields[] = "city = ?";
        $params[] = sanitizeInput($data['city']);
    }

    // İlçe
    if (isset($data['district'])) {
        $fields[] = "district = ?";
        $params[] = sanitizeInput($data['district']);
    }

    if (empty($fields))
        return true; // Değişiklik yok

    $params[] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($params);

    // Session'ı güncelle
    if ($result && isset($data['agent_name'])) {
        $_SESSION['user_name'] = $data['agent_name'];
    }

    return $result;
}

/**
 * Kullanıcı Hesabını Sil (Soft Delete)
 */
function deleteUser($pdo, $userId)
{
    // Arayışları da pasife çekebiliriz ama şimdilik sadece kullanıcıyı pasife çekelim.
    // Kullanıcıya ait tüm aktif veriler de mantıken pasif olmalı ama veri tutarlılığı için dokunmuyoruz.
    // Login olamayacağı için sorun yok.

    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
    return $stmt->execute([$userId]);
}

/**
 * Catchy WhatsApp paylaşım linki oluştur
 */
function getWhatsAppShareLink($search)
{
    global $PROPERTY_TYPES, $TRANSACTION_TYPES;

    $type = $TRANSACTION_TYPES[$search['transaction_type']]['label'] ?? 'Talep';
    $property = $PROPERTY_TYPES[$search['property_type']]['label'] ?? '';
    $price = formatPrice($search['max_price']);
    $loc = "{$search['city']} / {$search['district']}";
    $link = SITE_URL . "/arayislar.php?id=" . $search['id'];

    $msg = "🏠 *MÜŞTERİM HAZIR! PORTFÖYÜNÜZ VAR MI?* 🏠\n\n";
    $msg .= "📍 *Bölge:* {$loc}\n";
    $msg .= "🏢 *Tip:* {$type} {$property}\n";
    $msg .= "💰 *Bütçe:* {$price}\n";

    if (!empty($search['features'])) {
        $shortFeatures = mb_substr(strip_tags($search['features']), 0, 100);
        if (mb_strlen($search['features']) > 100)
            $shortFeatures .= "...";
        $msg .= "📝 *Özellikler:* {$shortFeatures}\n";
    }

    $msg .= "\n🤝 *Elinde uygun portföyü olan meslektaşlarım iletişime geçebilir.*\n\n";
    $msg .= "🔗 *Tüm detaylar için tıklayın:* {$link}\n\n";
    $msg .= "#emlakarayis #emlak #isbirligi";

    return "https://api.whatsapp.com/send?text=" . urlencode($msg);
}

/**
 * Kullanıcı şifresini güncelle
 */
function updateUserPassword($pdo, $userId, $newPassword)
{
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    return $stmt->execute([$hash, $userId]);
}

/**
 * Kullanıcının şifresi var mı kontrol et
 */
function userHasPassword($pdo, $userId)
{
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    return $user && !empty($user['password_hash']);
}

/**
 * Şifre ile giriş doğrulama
 */
function verifyUserPassword($pdo, $phone, $password)
{
    $phone = formatPhone($phone);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? AND is_active = 1 AND is_verified = 1");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user && $user['password_hash'] && password_verify($password, $user['password_hash'])) {
        return $user;
    }
    return false;
}


/**
 * Şifre sıfırlama token'ı oluştur
 */
function createPasswordResetToken($pdo, $userId)
{
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id = ?");
    $stmt->execute([$token, $expires, $userId]);

    return $token;
}

/**
 * Şifre sıfırlama token'ını doğrula
 */
function verifyPasswordResetToken($pdo, $token)
{
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires_at > NOW() LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    return $user ? $user['id'] : false;
}

/**
 * Şifre sıfırlama token'ını temizle
 */
function clearPasswordResetToken($pdo, $userId)
{
    $stmt = $pdo->prepare("UPDATE users SET reset_token = NULL, reset_expires_at = NULL WHERE id = ?");
    return $stmt->execute([$userId]);
}
