<?php
/**
 * Emlak Arayış - Giriş Sayfası
 * OTP veya Şifre ile giriş
 */
require_once 'config.php';
require_once 'includes/functions.php';

// Eğer kullanıcı zaten doğrulanmışsa yönlendir
if (isUserVerified()) {
    header('Location: talep-gir.php');
    exit;
}

$pageTitle = 'Giriş Yap';
require_once 'includes/header.php';
?>

<div class="min-h-[calc(100vh-64px)] bg-gray-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-gray-200 overflow-hidden relative">

        <!-- Decoration -->
        <div class="h-2 bg-gradient-to-r from-secondary to-primary w-full absolute top-0 left-0"></div>

        <!-- Tab Header -->
        <div class="flex border-b border-gray-200 mt-2">
            <button type="button" id="tab-otp"
                class="flex-1 py-4 text-center font-semibold text-primary border-b-2 border-primary transition-all"
                onclick="switchTab('otp')">
                <span class="material-symbols-outlined text-sm align-middle mr-1">sms</span>
                OTP ile Giriş
            </button>
            <button type="button" id="tab-password"
                class="flex-1 py-4 text-center font-semibold text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition-all"
                onclick="switchTab('password')">
                <span class="material-symbols-outlined text-sm align-middle mr-1">lock</span>
                Şifre ile Giriş
            </button>
        </div>

        <!-- OTP Tab Content -->
        <div id="content-otp" class="p-8">
            <div class="text-center mb-8">
                <div
                    class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-secondary">
                    <span class="material-symbols-outlined text-3xl">verified_user</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Doğrulama Talebi</h1>
                <p class="text-gray-500 text-sm mt-2">Güvenli bir ağ için sadece doğrulanan emlak danışmanları arayış
                    paylaşabilir.</p>
            </div>

            <form id="verifyForm" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ad Soyad</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">person</span>
                        <input type="text" name="name" required placeholder="Adınız Soyadınız"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Telefon Numarası</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">phone</span>
                        <input type="tel" name="phone" id="otpPhone" required
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary transition-all"
                            placeholder="5XX XXX XX XX">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ofis Adı (Opsiyonel)</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">business</span>
                        <input type="text" name="agency"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary transition-all"
                            placeholder="Emlak Ofisi Adı">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Instagram (Opsiyonel)</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">photo_camera</span>
                        <input type="text" name="instagram"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary transition-all"
                            placeholder="@kullaniciadi">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Hızlı doğrulama için önerilir.</p>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold shadow-lg shadow-primary/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">send</span>
                    Doğrulama Kodu Talep Et
                </button>

                <div class="text-center pt-2">
                    <button type="button" onclick="showCodeEntry()"
                        class="text-secondary text-sm font-semibold hover:underline">
                        Zaten kodum var
                    </button>
                </div>
            </form>

            <!-- Code Entry (Hidden by default) -->
            <div id="codeEntrySection" class="hidden">
                <div class="text-center mb-6">
                    <button onclick="hideCodeEntry()"
                        class="text-gray-400 hover:text-gray-600 transition-colors text-sm flex items-center gap-1 mx-auto">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                        Geri Dön
                    </button>
                </div>
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Kodu Girin</h2>
                    <p class="text-gray-500 text-sm mt-2">WhatsApp üzerinden size gönderilen 6 haneli kodu girin.</p>
                </div>

                <form id="codeForm" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="phone" id="codePhone">

                    <div class="flex justify-center gap-2" id="otp-inputs">
                        <input type="text" maxlength="1"
                            class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-200 rounded-xl focus:border-primary outline-none transition-colors"
                            oninput="handleOtpInput(this)">
                        <input type="text" maxlength="1"
                            class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-200 rounded-xl focus:border-primary outline-none transition-colors"
                            oninput="handleOtpInput(this)">
                        <input type="text" maxlength="1"
                            class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-200 rounded-xl focus:border-primary outline-none transition-colors"
                            oninput="handleOtpInput(this)">
                        <input type="text" maxlength="1"
                            class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-200 rounded-xl focus:border-primary outline-none transition-colors"
                            oninput="handleOtpInput(this)">
                        <input type="text" maxlength="1"
                            class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-200 rounded-xl focus:border-primary outline-none transition-colors"
                            oninput="handleOtpInput(this)">
                        <input type="text" maxlength="1"
                            class="w-12 h-14 text-center text-2xl font-bold border-2 border-gray-200 rounded-xl focus:border-primary outline-none transition-colors"
                            oninput="handleOtpInput(this)">
                    </div>
                    <input type="hidden" name="code" id="fullCode">

                    <button type="submit"
                        class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold shadow-lg shadow-green-600/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">login</span>
                        Doğrula ve Giriş Yap
                    </button>
                </form>
            </div>
        </div>

        <!-- Password Tab Content -->
        <div id="content-password" class="p-8 hidden">
            <div class="text-center mb-8">
                <div
                    class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 text-emerald-600">
                    <span class="material-symbols-outlined text-3xl">lock_open</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Şifre ile Giriş</h1>
                <p class="text-gray-500 text-sm mt-2">Daha önce belirlediğiniz şifre ile giriş yapın.</p>
            </div>

            <form id="passwordForm" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Telefon Numarası</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">phone</span>
                        <input type="tel" name="phone" id="passwordPhone" required
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary transition-all"
                            placeholder="5XX XXX XX XX">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Şifre</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">lock</span>
                        <input type="password" name="password" id="passwordInput" required
                            class="w-full pl-10 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary transition-all"
                            placeholder="••••••">
                        <button type="button" onclick="togglePasswordVisibility('passwordInput', this)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </button>
                    </div>
                </div>

                <div id="passwordError" class="hidden bg-red-50 text-red-700 p-3 rounded-lg text-sm"></div>

                <button type="submit" id="passwordSubmitBtn"
                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg shadow-emerald-600/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">login</span>
                    Giriş Yap
                </button>

                <div class="text-center pt-2">
                    <p class="text-gray-500 text-sm">
                        Şifreniz yok mu?
                        <button type="button" onclick="switchTab('otp')"
                            class="text-secondary font-semibold hover:underline">
                            OTP ile giriş yapın
                        </button>
                    </p>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    // Tab Switching
    function switchTab(tab) {
        const otpTab = document.getElementById('tab-otp');
        const passwordTab = document.getElementById('tab-password');
        const otpContent = document.getElementById('content-otp');
        const passwordContent = document.getElementById('content-password');

        if (tab === 'otp') {
            otpTab.classList.add('text-primary', 'border-primary');
            otpTab.classList.remove('text-gray-400', 'border-transparent');
            passwordTab.classList.remove('text-primary', 'border-primary');
            passwordTab.classList.add('text-gray-400', 'border-transparent');
            otpContent.classList.remove('hidden');
            passwordContent.classList.add('hidden');
        } else {
            passwordTab.classList.add('text-primary', 'border-primary');
            passwordTab.classList.remove('text-gray-400', 'border-transparent');
            otpTab.classList.remove('text-primary', 'border-primary');
            otpTab.classList.add('text-gray-400', 'border-transparent');
            passwordContent.classList.remove('hidden');
            otpContent.classList.add('hidden');
        }
    }

    // Show/Hide Code Entry
    function showCodeEntry() {
        document.getElementById('verifyForm').classList.add('hidden');
        document.getElementById('codeEntrySection').classList.remove('hidden');
    }

    function hideCodeEntry() {
        document.getElementById('verifyForm').classList.remove('hidden');
        document.getElementById('codeEntrySection').classList.add('hidden');
    }

    // OTP Input Handler
    function handleOtpInput(input) {
        if (input.value.length === 1) {
            const next = input.nextElementSibling;
            if (next && next.tagName === 'INPUT') {
                next.focus();
            }
        }
        updateFullCode();
    }

    function updateFullCode() {
        const inputs = document.querySelectorAll('#otp-inputs input');
        let code = '';
        inputs.forEach(input => code += input.value);
        document.getElementById('fullCode').value = code;
    }

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

    // OTP Verification Form
    document.getElementById('verifyForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const btn = e.target.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Gönderiliyor...';

        try {
            const response = await fetch('api/dogrulama-talebi.php', {
                method: 'POST',
                body: formData
            });
            const response = await fetch('api/dogrulama-talebi.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                // Yeni akış: Direkt yönlendirme
                if (result.redirect) {
                    window.location.href = result.redirect;
                } else {
                    window.location.href = 'talep-gir.php';
                }
            } else {
                alert('Hata: ' + result.message);
            }
        } catch (error) {
            alert('Bir hata oluştu');
        }

        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined">send</span> Doğrulama Kodu Talep Et';
    });

    // Code Verification Form
    document.getElementById('codeForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const btn = e.target.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Doğrulanıyor...';

        try {
            const response = await fetch('api/kod-dogrula.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                window.location.href = 'talep-gir.php';
            } else {
                alert('Hata: ' + result.message);
            }
        } catch (error) {
            alert('Bir hata oluştu');
        }

        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined">login</span> Doğrula ve Giriş Yap';
    });

    // Password Login Form
    document.getElementById('passwordForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const btn = document.getElementById('passwordSubmitBtn');
        const errorDiv = document.getElementById('passwordError');

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Giriş Yapılıyor...';
        errorDiv.classList.add('hidden');

        try {
            const response = await fetch('api/sifre-ile-giris.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                window.location.href = 'talep-gir.php';
            } else {
                errorDiv.textContent = result.message;
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            errorDiv.textContent = 'Bir hata oluştu. Lütfen tekrar deneyin.';
            errorDiv.classList.remove('hidden');
        }

        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined">login</span> Giriş Yap';
    });
</script>

<?php require_once 'includes/footer.php'; ?>