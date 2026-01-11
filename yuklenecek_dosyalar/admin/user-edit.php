<?php
/**
 * Emlak Arayış - Admin: Kullanıcı Düzenleme
 */
$pageTitle = 'Kullanıcıyı Düzenle';
require_once __DIR__ . '/includes/header.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "<div class='bg-red-50 text-red-600 p-4 rounded-xl'>Kullanıcı bulunamadı.</div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$token = generateCSRFToken();
$message = '';

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Güvenlik doğrulaması başarısız.'];
    } else {
        $action = $_POST['action'] ?? 'update';

        if ($action === 'update') {
            try {
                $stmt = $pdo->prepare("UPDATE users SET 
                    agent_name = ?, 
                    agency_name = ?, 
                    phone = ?, 
                    instagram = ?, 
                    is_active = ?,
                    is_verified = ?
                    WHERE id = ?");

                $stmt->execute([
                    sanitizeInput($_POST['agent_name']),
                    sanitizeInput($_POST['agency_name']),
                    formatPhone($_POST['phone']),
                    sanitizeInput($_POST['instagram']),
                    isset($_POST['is_active']) ? 1 : 0,
                    isset($_POST['is_verified']) ? 1 : 0,
                    $id
                ]);

                $message = ['type' => 'success', 'text' => 'Kullanıcı bilgileri başarıyla güncellendi.'];
                // Veriyi tazele
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $user = $stmt->fetch();
            } catch (PDOException $e) {
                $message = ['type' => 'error', 'text' => 'Veritabanı hatası: ' . $e->getMessage()];
            }
        } elseif ($action === 'reset_password') {
            $newPassword = sanitizeInput($_POST['new_password']);
            if (strlen($newPassword) < 6) {
                $message = ['type' => 'error', 'text' => 'Şifre en az 6 karakter olmalıdır.'];
            } else {
                updateUserPassword($pdo, $id, $newPassword);
                $message = ['type' => 'success', 'text' => 'Şifre başarıyla güncellendi.'];
            }
        } elseif ($action === 'generate_reset_link') {
            $token = createPasswordResetToken($pdo, $id);
            $resetUrl = SITE_URL . "/sifre-sifirla.php?token=" . $token;
            $message = ['type' => 'success', 'text' => 'Şifre sıfırlama linki oluşturuldu. Aşağıdaki WhatsApp butonu ile paylaşabilirsiniz.'];
            
            // Veriyi tazele (token kaydedildiği için)
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
        }
    }
}

// WhatsApp Şifre Sıfırlama Mesajı Linki
if (!empty($user['reset_token'])) {
    $resetUrl = SITE_URL . "/sifre-sifirla.php?token=" . $user['reset_token'];
    $resetMsg = "Merhaba {$user['agent_name']}, Emlak Arayış hesabınız için şifre sıfırlama talebi oluşturulmuştur.\n\nAşağıdaki linke tıklayarak yeni şifrenizi belirleyebilirsiniz:\n\n{$resetUrl}\n\nBu link 24 saat geçerlidir.";
} else {
    $resetMsg = "Merhaba {$user['agent_name']}, Emlak Arayış hesabınız için şifreniz güncellenmiştir.\n\nYeni Şifreniz: [BURAYA YAZIN]\n\nGiriş yapmak için: " . SITE_URL . "/login.php";
}
$whatsappResetUrl = whatsappLink($user['phone'], $resetMsg);
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="users.php" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Kullanıcıyı Düzenle <span
                class="text-gray-400 font-mono text-sm ml-2">#
                <?= $id ?>
            </span></h1>
    </div>

    <?php if ($message): ?>
        <div
            class="mb-6 p-4 rounded-xl <?= $message['type'] === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?>">
            <?= $message['text'] ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 gap-8">
        <!-- Temel Bilgiler -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                <h3 class="font-bold text-gray-800">Profil Bilgileri</h3>
            </div>
            <form method="POST" class="p-6 space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $token ?>">
                <input type="hidden" name="action" value="update">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Ad Soyad</label>
                        <input type="text" name="agent_name" value="<?= e($user['agent_name']) ?>" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Ajans / Firma</label>
                        <input type="text" name="agency_name" value="<?= e($user['agency_name']) ?>"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Telefon</label>
                        <input type="text" name="phone" value="<?= e($user['phone']) ?>" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Instagram</label>
                        <input type="text" name="instagram" value="<?= e($user['instagram']) ?>"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" <?= $user['is_active'] ? 'checked' : '' ?>
                        class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm font-medium text-gray-700">Hesap Aktif</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_verified" <?= $user['is_verified'] ? 'checked' : '' ?>
                        class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm font-medium text-gray-700">Doğrulanmış Hesap</span>
                    </label>
                </div>

                <button type="submit"
                  class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl transition-all">
                    Bilgileri Güncelle
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Manuel Şifre Atama -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Manuel Şifre Atama</h3>
                    <?php if (!empty($user['password_hash'])): ?>
                        <span class="text-[10px] font-bold uppercase bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Şifreli Hesap</span>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                        <input type="hidden" name="action" value="reset_password">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Yeni Şifre</label>
                            <div class="flex gap-2">
                                <input type="text" name="new_password" id="new_password" placeholder="Min. 6 karakter"
                                       class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                                <button type="button" onclick="generateRandomPass()" class="px-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200" title="Rastgele Oluştur">
                                    <span class="material-symbols-outlined">cached</span>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-gray-800 hover:bg-black text-white font-bold py-3 rounded-xl transition-all">
                            Şifreyi Güncelle
                        </button>
                    </form>
                </div>
            </div>

            <!-- Gelişmiş Şifre Sıfırlama -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Güvenli Sıfırlama (Önerilen)</h3>
                    <span class="text-[10px] font-bold uppercase bg-green-100 text-green-700 px-2 py-1 rounded-full">Yeni</span>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-6">Kullanıcıya özel bir link göndererek kendi şifresini belirlemesini sağlayın. Bu yöntem daha güvenlidir.</p>
                    
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                        <input type="hidden" name="action" value="generate_reset_link">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">link</span>
                            Sıfırlama Linki Oluştur
                        </button>
                    </form>

                    <?php if (!empty($user['reset_token'])): ?>
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-xl">
                            <p class="text-[10px] uppercase font-bold text-blue-500 mb-1 leading-none">Aktif Link Mevcut</p>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly value="<?= SITE_URL ?>/sifre-sifirla.php?token=<?= $user['reset_token'] ?>" 
                                       class="flex-1 bg-transparent text-xs text-blue-800 outline-none border-none p-0 focus:ring-0">
                                <button onclick="navigator.clipboard.writeText(this.previousElementSibling.value)" class="text-blue-500 hover:text-blue-700">
                                    <span class="material-symbols-outlined text-sm">content_copy</span>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- WhatsApp Bilgilendirme -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col items-center gap-4">
                    <p class="text-sm text-gray-500 text-center">Şifre veya sıfırlama linki oluşturduktan sonra kullanıcıyı WhatsApp ile bilgilendirin.</p>
                    <a href="<?= $whatsappResetUrl ?>" target="_blank" id="waBtn"
                       class="w-full sm:w-auto px-12 flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#128C7E] text-white font-bold py-4 rounded-2xl transition-all shadow-lg shadow-green-500/20">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp ile Bilgilendir
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function generateRandomPass() {
        const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        let pass = "";
        for (let i = 0; i < 8; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('new_password').value = pass;
        updateWaLink(pass);
    }

    function updateWaLink(pass) {
    const btn = document.getElementById('waBtn');
    const hasToken = <?= !empty($user['reset_token']) ? 'true' : 'false' ?>;
    
    if (hasToken && !pass) {
        // Reset link message logic is handled in PHP and already set.
        return;
    }

    const newMsg = `Merhaba <?= e($user['agent_name']) ?>, Emlak Arayış hesabınız için şifreniz güncellenmiştir.\n\nYeni Şifreniz: ${pass}\n\nGiriş yapmak için: <?= SITE_URL ?>/login.php`;
    const newUrl = "https://wa.me/<?= formatPhone($user['phone']) ?>?text=" + encodeURIComponent(newMsg);
    btn.href = newUrl;
}    }

    document.getElementById('new_password').addEventListener('input', function (e) {
        updateWaLink(e.target.value);
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>