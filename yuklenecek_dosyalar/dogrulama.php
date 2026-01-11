<?php
/**
 * Emlak Arayış - Doğrulama Ekranı
 */
require_once 'config.php';
require_once 'includes/functions.php'; // Needed for checks

// Eğer kullanıcı zaten doğrulanmışsa yönlendir
if (isUserVerified()) {
    header('Location: talep-gir.php');
    exit;
}

$pageTitle = 'Doğrulama';
require_once 'includes/header.php';
?>

<div class="min-h-[calc(100vh-64px)] bg-gray-50 flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-gray-200 overflow-hidden relative"
        id="auth-container">

        <!-- Decoration -->
        <div class="h-2 bg-gradient-to-r from-secondary to-primary w-full absolute top-0 left-0"></div>

        <!-- Step 1: Verification Request Form -->
        <div id="step-request" class="p-8">
            <div class="text-center mb-8">
                <div
                    class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 text-secondary">
                    <span class="material-symbols-outlined !text-4xl">security</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Meslektaş Doğrulaması</h1>
                <p class="text-gray-500 text-sm mt-2">Güvenli bir ağ için sadece doğrulanan emlak danışmanları arayış
                    paylaşabilir.</p>
            </div>

            <form id="verifyForm" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ad Soyad</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">person</span>
                        <input type="text" name="name" required
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary transition-all"
                            placeholder="Adınız Soyadınız">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Telefon Numarası</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">phone</span>
                        <input type="tel" name="phone" required
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary transition-all"
                            placeholder="5XX XXX XX XX">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ofis Adı (Opsiyonel)</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">business</span>
                        <input type="text" name="agency"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary transition-all"
                            placeholder="Kurumsal Emlak">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Instagram (Opsiyonel)</label>
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">camera_alt</span>
                        <input type="text" name="instagram"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary transition-all"
                            placeholder="@kullaniciadi">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Hızlı doğrulama için önerilir.</p>
                </div>

                <button type="submit"
                    class="w-full bg-[#25D366] hover:bg-[#128C7E] text-white py-4 rounded-xl font-bold shadow-lg shadow-green-200 transition-all flex items-center justify-center gap-2 mt-4 transform active:scale-95">
                    <i class="fab fa-whatsapp text-xl"></i>
                    <!-- FontAwesome loaded via header? No, need to handle icon -->
                    <span class="material-symbols-outlined">chat</span>
                    WhatsApp ile Doğrula
                </button>

                <p class="text-xs text-center text-gray-400 mt-4 leading-relaxed">
                    Butona tıkladığınızda WhatsApp üzerinden yöneticimize otomatik bir doğrulama mesajı gönderilecektir.
                    Ardından size bir kod iletilecektir.
                </p>

                <div class="text-center pt-4 border-t border-gray-100 mt-4">
                    <button type="button" onclick="showStep('step-code')"
                        class="text-secondary text-sm font-semibold hover:underline">
                        Zaten kodum var
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 2: Code Entry -->
        <div id="step-code" class="p-8 hidden">
            <div class="text-center mb-8">
                <button onclick="showStep('step-request')"
                    class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>

                <div
                    class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 text-green-600">
                    <span class="material-symbols-outlined !text-4xl">lock</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Kodu Girin</h1>
                <p class="text-gray-500 text-sm mt-2">WhatsApp üzerinden size gönderilen 6 haneli kodu girin.</p>
            </div>

            <form id="codeForm" class="space-y-6">
                <!-- Phone input needed for verification match -->
                <input type="hidden" name="phone" id="codePhone">

                <div class="flex justify-center gap-2" id="otp-inputs">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <input type="text" maxlength="1"
                            class="w-12 h-14 border-2 border-gray-200 rounded-lg text-center text-2xl font-bold text-primary focus:border-secondary focus:ring-0 transition-colors outline-none otp-input"
                            required inputmode="numeric">
                    <?php endfor; ?>
                    <input type="hidden" name="code" id="fullCode">
                </div>

                <button type="submit"
                    class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-xl font-bold shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                    Doğrula ve Giriş Yap
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    // Simple View Logic
    function showStep(stepId) {
        document.getElementById('step-request').classList.add('hidden');
        document.getElementById('step-code').classList.add('hidden');
        document.getElementById(stepId).classList.remove('hidden');

        if (stepId === 'step-code') {
            document.querySelector('.otp-input').focus();
        }
    }

    // OTP Input Logic
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateHiddenCode();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    function updateHiddenCode() {
        let code = '';
        inputs.forEach(input => code += input.value);
        document.getElementById('fullCode').value = code;
    }

    // Form Submissions will be handled in app.js
</script>

<?php require_once 'includes/footer.php'; ?>