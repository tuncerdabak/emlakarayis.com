<?php
/**
 * Emlak Arayış - Profil Yönetimi
 */
require_once 'config.php';
require_once 'includes/functions.php';

// Oturum kontrolü
if (!isUserVerified()) {
    header('Location: giris.php');
    exit;
}

$success = '';
$error = '';

// Form Gönderimi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check (optional but good practice, skipping for MVP speed unless critical)

    // Profil Güncelleme
    if (isset($_POST['update_profile'])) {
        $data = [
            'agent_name' => $_POST['agent_name'] ?? '',
            'agency_name' => $_POST['agency_name'] ?? '',
            'city' => $_POST['city'] ?? '',
            'district' => $_POST['district'] ?? ''
        ];

        if (updateUser($pdo, $_SESSION['user_id'], $data)) {
            $success = 'Profil bilgileriniz başarıyla güncellendi.';
        } else {
            $error = 'Güncelleme sırasında bir hata oluştu.';
        }
    }

    // Hesap Silme
    if (isset($_POST['delete_account'])) {
        if (deleteUser($pdo, $_SESSION['user_id'])) {
            // Oturumu kapat ve yönlendir
            session_destroy();
            header('Location: index.php');
            exit;
        } else {
            $error = 'Hesap silinirken bir hata oluştu.';
        }
    }
}

// Güncel verileri çek
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: giris.php');
    exit;
}

$pageTitle = 'Profilim';
require_once 'includes/header.php';
?>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-xl mx-auto px-4">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Header -->
            <div class="bg-primary/5 p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Profil Yönetimi</h1>
                    <p class="text-sm text-gray-500">Üyelik bilgilerinizi düzenleyebilirsiniz.</p>
                </div>
                <div
                    class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold text-xl">
                    <?= mb_substr($user['agent_name'], 0, 1) ?>
                </div>
            </div>

            <!-- Messages -->
            <?php if ($success): ?>
                <div
                    class="bg-green-50 text-green-700 p-4 text-sm font-medium border-b border-green-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    <?= $success ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-700 p-4 text-sm font-medium border-b border-red-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">error</span>
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" class="p-6 space-y-5">

                <!-- Ad Soyad -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ad Soyad</label>
                    <input type="text" name="agent_name" value="<?= e($user['agent_name']) ?>" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary transition-all">
                </div>

                <!-- Firma Adı -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Firma / Ofis Adı</label>
                    <input type="text" name="agency_name" value="<?= e($user['agency_name']) ?>" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary transition-all">
                </div>

                <!-- Telefon (Disabled) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Telefon (Değiştirilemez)</label>
                    <input type="text" value="<?= formatPhone($user['phone']) ?>" disabled
                        class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed">
                    <p class="text-[10px] text-gray-400 mt-1 pl-1">Telefon numarası değişikliği için yönetici ile
                        iletişime geçiniz.</p>
                </div>

                <!-- Lokasyon Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- İl -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">İl</label>
                        <select name="city" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary appearance-none cursor-pointer">
                            <?php foreach ($CITIES as $city): ?>
                                <option value="<?= $city ?>" <?= $user['city'] === $city ? 'selected' : '' ?>>
                                    <?= $city ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- İlçe -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">İlçe</label>
                        <input type="text" name="district" value="<?= e($user['district']) ?>" required
                            placeholder="Örn: Kadıköy"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary transition-all">
                    </div>
                </div>

                <!-- Actions -->
                <div class="pt-4 flex items-center justify-between border-t border-gray-100 mt-4">
                    <button type="submit" name="update_profile"
                        class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-lg font-bold shadow-md shadow-primary/20 transition-all active:scale-95 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        Kaydet
                    </button>

                    <a href="cikis.php"
                        class="text-gray-500 hover:text-gray-700 font-medium text-sm px-4 py-2 hover:bg-gray-50 rounded-lg transition-colors">
                        Çıkış Yap
                    </a>
                </div>
            </form>
        </div>

        <!-- Şifre Yönetimi -->
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-emerald-50 p-4 border-b border-emerald-100 flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                    <span class="material-symbols-outlined">lock</span>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Şifre Yönetimi</h3>
                    <p class="text-xs text-gray-500">Şifre belirleyerek farklı cihazlardan kolayca giriş yapabilirsiniz.
                    </p>
                </div>
            </div>

            <div class="p-6">
                <?php $hasPassword = userHasPassword($pdo, $_SESSION['user_id']); ?>

                <?php if ($hasPassword): ?>
                    <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">check_circle</span>
                        Şifreniz belirlenmiş. Telefon + şifre ile giriş yapabilirsiniz.
                    </div>
                <?php else: ?>
                    <div class="bg-amber-50 text-amber-700 p-3 rounded-lg text-sm mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">info</span>
                        Henüz şifre belirlemediniz. Şifre belirleyerek OTP olmadan giriş yapabilirsiniz.
                    </div>
                <?php endif; ?>

                <form id="passwordForm" class="space-y-4">
                    <?php if ($hasPassword): ?>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mevcut Şifre</label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">lock</span>
                                <input type="password" name="current_password" id="currentPassword" required
                                    class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                    placeholder="••••••">
                                <button type="button" onclick="togglePasswordVisibility('currentPassword', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">
                            <?= $hasPassword ? 'Yeni Şifre' : 'Şifre Belirle' ?>
                        </label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">lock</span>
                            <input type="password" name="new_password" id="newPassword" required minlength="6"
                                class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                placeholder="En az 6 karakter">
                            <button type="button" onclick="togglePasswordVisibility('newPassword', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Şifre Tekrar</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">lock</span>
                            <input type="password" name="confirm_password" id="confirmPassword" required minlength="6"
                                class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                placeholder="••••••">
                            <button type="button" onclick="togglePasswordVisibility('confirmPassword', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div id="passwordMessage" class="hidden p-3 rounded-lg text-sm"></div>

                    <button type="submit" id="passwordSubmitBtn"
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-md shadow-emerald-600/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">save</span>
                        <?= $hasPassword ? 'Şifreyi Güncelle' : 'Şifre Belirle' ?>
                    </button>
                </form>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="mt-8 border-t border-gray-200 pt-8">
            <h3 class="text-sm font-bold text-red-600 mb-2">Hesap İşlemleri</h3>
            <div class="bg-red-50 border border-red-100 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <strong class="block text-sm text-red-800">Hesabımı Sil</strong>
                    <p class="text-xs text-red-600 mt-0.5">Hesabınız ve tüm arayışlarınız kalıcı olarak devre dışı
                        bırakılacaktır.</p>
                </div>
                <form method="POST"
                    onsubmit="return confirm('Hesabınızı silmek istediğinize emin misiniz? Bu işlem geri alınamaz!');">
                    <button type="submit" name="delete_account"
                        class="px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-bold hover:bg-red-600 hover:text-white transition-colors">
                        Hesabı Sil
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    // Toggle Password Visibility
    function togglePasswordVisibility(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('.material-symbols-outlined');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    // Password Form Handler
    document.getElementById('passwordForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const btn = document.getElementById('passwordSubmitBtn');
        const messageDiv = document.getElementById('passwordMessage');

        // Client-side validation
        const newPassword = formData.get('new_password');
        const confirmPassword = formData.get('confirm_password');

        if (newPassword.length < 6) {
            messageDiv.textContent = 'Şifre en az 6 karakter olmalıdır.';
            messageDiv.className = 'bg-red-50 text-red-700 p-3 rounded-lg text-sm';
            messageDiv.classList.remove('hidden');
            return;
        }

        if (newPassword !== confirmPassword) {
            messageDiv.textContent = 'Şifreler eşleşmiyor.';
            messageDiv.className = 'bg-red-50 text-red-700 p-3 rounded-lg text-sm';
            messageDiv.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Kaydediliyor...';
        messageDiv.classList.add('hidden');

        try {
            const response = await fetch('api/sifre-guncelle.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                messageDiv.textContent = result.message;
                messageDiv.className = 'bg-green-50 text-green-700 p-3 rounded-lg text-sm';
                messageDiv.classList.remove('hidden');

                // Clear form
                e.target.reset();

                // Reload page after 2 seconds to show updated state
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                messageDiv.textContent = result.message;
                messageDiv.className = 'bg-red-50 text-red-700 p-3 rounded-lg text-sm';
                messageDiv.classList.remove('hidden');
            }
        } catch (error) {
            messageDiv.textContent = 'Bir hata oluştu. Lütfen tekrar deneyin.';
            messageDiv.className = 'bg-red-50 text-red-700 p-3 rounded-lg text-sm';
            messageDiv.classList.remove('hidden');
        }

        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-lg">save</span> <?= $hasPassword ? 'Şifreyi Güncelle' : 'Şifre Belirle' ?>';
    });
</script>

<?php require_once 'includes/footer.php'; ?>