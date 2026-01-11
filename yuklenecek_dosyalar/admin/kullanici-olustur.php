<?php
/**
 * Emlak Arayış - Admin: Kullanıcı Oluştur
 */
$pageTitle = 'Yeni Kullanıcı Oluştur';
require_once __DIR__ . '/includes/header.php';

$token = generateCSRFToken();
?>

<div class="max-w-xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Yeni Kullanıcı Oluştur</h2>

    <div class="bg-white shadow rounded-lg p-6">
        <form id="createUserForm" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700">Ad Soyad</label>
                <input type="text" name="agent_name" required
                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Firma Adı</label>
                <input type="text" name="agency_name" required
                    class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Telefon Numarası</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <input type="tel" name="phone" required placeholder="5XX..."
                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <p class="mt-1 text-xs text-gray-500">Başına 0 koymadan giriniz.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Instagram (Opsiyonel)</label>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span
                        class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                        @
                    </span>
                    <input type="text" name="instagram"
                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-700">Şifre (Opsiyonel)</label>
                <div class="mt-1 relative">
                    <input type="password" name="password" id="passwordField" minlength="6"
                        placeholder="En az 6 karakter"
                        class="block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <button type="button" onclick="togglePassword()"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined text-lg" id="passwordToggleIcon">visibility</span>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">Şifre belirlenmezse kullanıcı sadece OTP ile giriş yapabilir.
                </p>
            </div>

            <div class="pt-4">
                <button type="submit" id="submitBtn"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Kullanıcıyı Oluştur
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const form = document.getElementById('createUserForm');
    const submitBtn = document.getElementById('submitBtn');

    // Password toggle
    function togglePassword() {
        const field = document.getElementById('passwordField');
        const icon = document.getElementById('passwordToggleIcon');
        if (field.type === 'password') {
            field.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            field.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        submitBtn.disabled = true;
        submitBtn.innerHTML = 'İşleniyor...';

        try {
            const formData = new FormData(form);
            const response = await fetch('../api/admin/kullanici-olustur.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert('Kullanıcı başarıyla oluşturuldu!');
                window.location.href = 'users.php';
            } else {
                alert('Hata: ' + result.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Kullanıcıyı Oluştur';
            }
        } catch (error) {
            console.error(error);
            alert('Bir hata oluştu');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Kullanıcıyı Oluştur';
        }
    });
</script>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>