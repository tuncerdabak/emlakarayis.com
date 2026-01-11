<?php
/**
 * Emlak Arayış - Arayış Gir (Multi-step Form)
 */
require_once 'config.php';
require_once 'includes/functions.php'; // Needed for session and checks

// Kullanıcı doğrulanmış mı kontrol et
if (!isUserVerified()) {
    header('Location: dogrulama.php');
    exit;
}

$pageTitle = 'Yeni Talep Oluştur';
require_once 'includes/header.php';

$currentUser = getCurrentUser();
?>


<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        <!-- Header & Progress -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <a href="index.php" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                Yeni Talep Oluştur
            </h1>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                <div id="progressBar" class="bg-secondary h-2 rounded-full transition-all duration-300"
                    style="width: 16%"></div>
            </div>
            <div class="flex justify-between text-xs font-semibold text-gray-400 uppercase tracking-wider">
                <span id="stepLabel">Adım 1/6</span>
                <span id="stepTitle">İşlem Türü</span>
            </div>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative min-h-[400px]">
            <form id="searchForm" class="p-6 md:p-8">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

                <!-- STEP 1: İşlem Türü -->
                <div class="form-step" data-step="1">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Ne arıyorsunuz?</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="cursor-pointer group">
                            <input type="radio" name="transaction_type" value="satilik" class="peer sr-only" required>
                            <div
                                class="border-2 border-gray-200 rounded-2xl p-6 text-center hover:border-satilik hover:bg-emerald-50 transition-all peer-checked:border-satilik peer-checked:bg-emerald-50 peer-checked:shadow-md">
                                <div
                                    class="w-16 h-16 rounded-full bg-emerald-100 text-satilik flex items-center justify-center mx-auto mb-4 text-3xl">
                                    <span class="material-symbols-outlined">sell</span>
                                </div>
                                <h4 class="font-bold text-lg text-gray-700 peer-checked:text-satilik">Satılık</h4>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="transaction_type" value="kiralik" class="peer sr-only" required>
                            <div
                                class="border-2 border-gray-200 rounded-2xl p-6 text-center hover:border-kiralik hover:bg-orange-50 transition-all peer-checked:border-kiralik peer-checked:bg-orange-50 peer-checked:shadow-md">
                                <div
                                    class="w-16 h-16 rounded-full bg-orange-100 text-kiralik flex items-center justify-center mx-auto mb-4 text-3xl">
                                    <span class="material-symbols-outlined">key</span>
                                </div>
                                <h4 class="font-bold text-lg text-gray-700 peer-checked:text-kiralik">Kiralık</h4>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- STEP 2: Gayrimenkul Türü -->
                <div class="form-step hidden" data-step="2">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Gayrimenkul türü nedir?</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <?php foreach ($PROPERTY_TYPES as $key => $type): ?>
                            <label class="cursor-pointer group">
                                <input type="radio" name="property_type" value="<?= $key ?>" class="peer sr-only" required>
                                <div
                                    class="border-2 border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center gap-2 hover:border-secondary hover:bg-blue-50 transition-all peer-checked:border-secondary peer-checked:bg-blue-50 peer-checked:shadow-sm aspect-square">
                                    <span
                                        class="material-symbols-outlined text-3xl text-gray-400 peer-checked:text-secondary group-hover:text-secondary"><?= $type['icon'] ?></span>
                                    <span
                                        class="font-semibold text-sm text-gray-600 peer-checked:text-secondary"><?= $type['label'] ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- STEP 3: Konum -->
                <div class="form-step hidden" data-step="3">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Nerede arıyorsunuz?</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">İl</label>
                            <select name="city" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary appearance-none cursor-pointer">
                                <option value="">İl Seçiniz</option>
                                <?php foreach ($CITIES as $city): ?>
                                    <option value="<?= $city ?>"><?= $city ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">İlçe</label>
                            <input type="text" name="district" required placeholder="Örn: Kadıköy"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mahalle/Semt
                                (Opsiyonel)</label>
                            <input type="text" name="neighborhood" placeholder="Örn: Moda, Fenerbahçe"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary transition-all">
                        </div>
                    </div>
                </div>

                <!-- STEP 4: Bütçe -->
                <div class="form-step hidden" data-step="4">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Maksimum bütçeniz nedir?</h3>
                    <div class="relative max-w-sm mx-auto mt-8">
                        <input type="text" id="priceDisplay" placeholder="0"
                            class="w-full text-center text-4xl font-black text-gray-900 bg-transparent border-b-2 border-gray-200 focus:border-secondary outline-none py-2 placeholder-gray-200 transition-colors"
                            inputmode="numeric">
                        <input type="hidden" name="max_price" id="maxPriceInput" required>
                        <span
                            class="absolute right-0 bottom-4 text-2xl font-bold text-gray-400 pointer-events-none">₺</span>
                    </div>
                    <p class="text-center text-gray-400 text-sm mt-4">Müşterinizin çıkabileceği maksimum rakamı girin.
                    </p>
                </div>

                <!-- STEP 5: Detaylar -->
                <div class="form-step hidden" data-step="5">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Talep detayları</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Aranan
                                Özellikler</label>
                            <textarea name="features" required rows="4" maxlength="200"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary resize-none transition-all"
                                placeholder="Oda sayısı, kat, cephe, metrekare vb. detayları yazın..."></textarea>
                            <div class="text-right text-xs text-gray-400 mt-1">Maksimum 200 karakter</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Özel Not
                                (Opsiyonel)</label>
                            <input type="text" name="special_note" maxlength="100"
                                placeholder="Örn: Krediye uygun, Acil, Takas olur"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary transition-all">
                        </div>
                    </div>
                </div>

                <!-- STEP 6: Süre -->
                <div class="form-step hidden" data-step="6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Bu talep ne kadar aktif kalsın?</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <?php foreach ($DURATION_OPTIONS as $days => $label): ?>
                            <label class="cursor-pointer group">
                                <input type="radio" name="duration" value="<?= $days ?>" class="peer sr-only" <?= $days === 7 ? 'checked' : '' ?>>
                                <div
                                    class="border-2 border-gray-200 rounded-xl p-4 flex items-center justify-between hover:border-secondary hover:bg-blue-50 transition-all peer-checked:border-secondary peer-checked:bg-blue-50 peer-checked:shadow-sm">
                                    <span class="font-bold text-gray-700 peer-checked:text-secondary"><?= $label ?></span>
                                    <div
                                        class="w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-secondary peer-checked:bg-secondary flex items-center justify-center">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-gray-400 mt-4 leading-relaxed">
                        Belirtilen süre sonunda talebiniz otomatik olarak yayından kaldırılacaktır.
                    </p>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between mt-10 pt-6 border-t border-gray-100">
                    <button type="button" id="prevBtn"
                        class="px-6 py-3 rounded-xl font-bold text-gray-500 hover:text-gray-900 transition-colors hidden">
                        Geri
                    </button>
                    <button type="button" id="nextBtn"
                        class="ml-auto px-8 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold shadow-lg shadow-primary/20 transition-all transform active:scale-95 flex items-center gap-2">
                        Devam Et <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </button>
                    <button type="submit" id="submitBtn"
                        class="ml-auto px-8 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold shadow-lg shadow-green-500/20 transition-all transform active:scale-95 items-center gap-2 hidden">
                        Talebi Paylaş <span class="material-symbols-outlined text-sm">check</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/app.js"></script>
<script>
    // Price formatter
    const priceInput = document.getElementById('priceDisplay');
    const hiddenPrice = document.getElementById('maxPriceInput');

    priceInput.addEventListener('input', function (e) {
        // Remove non-digits
        let value = this.value.replace(/\D/g, '');

        // Update hidden input
        hiddenPrice.value = value;

        // Format display
        if (value) {
            this.value = parseInt(value).toLocaleString('tr-TR');
        } else {
            this.value = '';
        }
    });

    // Step Management
    let currentStep = 1;
    const totalSteps = 6;
    const form = document.getElementById('searchForm');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const progressBar = document.getElementById('progressBar');
    const stepLabel = document.getElementById('stepLabel');
    const stepTitle = document.getElementById('stepTitle');

    // Step Titles Map
    const stepTitles = {
        1: 'İşlem Türü',
        2: 'Gayrimenkul Türü',
        3: 'Konum',
        4: 'Bütçe',
        5: 'Detaylar',
        6: 'Yayın Süresi'
    };

    function updateStep(step) {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(el => el.classList.add('hidden'));

        // Show current step with animation
        const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        currentStepEl.classList.remove('hidden');
        currentStepEl.classList.add('animate-fade-in-up');

        // Update Progress
        const progress = ((step) / totalSteps) * 100;
        progressBar.style.width = `${progress}%`;
        stepLabel.innerText = `Adım ${step}/${totalSteps}`;
        stepTitle.innerText = stepTitles[step];

        // Button Visibility
        if (step === 1) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }

        if (step === totalSteps) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }

    // Validation
    function validateStep(step) {
        const stepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value) {
                isValid = false;
                input.classList.add('border-accent', 'ring-1', 'ring-accent');

                // Remove error style on input
                input.addEventListener('input', () => {
                    input.classList.remove('border-accent', 'ring-1', 'ring-accent');
                }, { once: true });
            }
        });

        return isValid;
    }

    // Navigation Events
    nextBtn.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            currentStep++;
            updateStep(currentStep);
            window.scrollTo(0, 0);
        } else {
            // Optional: Shake animation or toast
            alert('Lütfen zorunlu alanları doldurun.');
        }
    });

    prevBtn.addEventListener('click', () => {
        currentStep--;
        updateStep(currentStep);
        window.scrollTo(0, 0);
    });

    // Form Submission
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Paylaşılıyor...';

            const response = await fetch('api/arayis-ekle.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = `basarili.php?id=${result.id}`;
            } else {
                alert('Hata: ' + result.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Talebi Paylaş <span class="material-symbols-outlined text-sm">check</span>';
            }
        } catch (error) {
            console.error(error);
            alert('Bir hata oluştu.');
            submitBtn.disabled = false;
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>