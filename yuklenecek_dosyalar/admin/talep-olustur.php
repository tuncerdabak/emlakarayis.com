<?php
/**
 * Emlak Arayış - Admin: Talep Oluştur (Multi-step Form)
 */
$pageTitle = 'Yönetici - Yeni Talep Oluştur';
require_once __DIR__ . '/includes/header.php';

// Tüm kullanıcıları getir (Dropdown için)
$users = getAllUsers($pdo);

$token = generateCSRFToken();
?>

<div class="max-w-2xl mx-auto">

    <!-- Header & Progress -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            Yönetici: Kullanıcı Adına Talep Oluştur
        </h1>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
            <div id="progressBar" class="bg-secondary h-2 rounded-full transition-all duration-300" style="width: 14%">
            </div>
        </div>
        <div class="flex justify-between text-xs font-semibold text-gray-400 uppercase tracking-wider">
            <span id="stepLabel">Adım 1/7</span>
            <span id="stepTitle">Kullanıcı Seçimi</span>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative min-h-[400px]">
        <form id="searchForm" class="p-6 md:p-8">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">

            <!-- STEP 1: Kullanıcı Seçimi -->
            <div class="form-step" data-step="1">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Hangi kullanıcı adına ekliyorsunuz?</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kullanıcı Seçin</label>
                        <select name="user_id" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary appearance-none cursor-pointer">
                            <option value="">Kullanıcı Seçiniz</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>">
                                    <?= e($u['agent_name']) ?> (<?= e($u['agency_name']) ?>) -
                                    <?= formatPhone($u['phone']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">
                            Listede kullanıcı yoksa önce <a href="kullanici-olustur.php"
                                class="text-blue-600 underline">Kullanıcı Oluştur</a> sayfasından ekleyin.
                        </p>
                    </div>
                </div>
            </div>

            <!-- STEP 2: İşlem Türü -->
            <div class="form-step hidden" data-step="2">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Ne arıyor?</h3>
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

            <!-- STEP 3: Gayrimenkul Türü -->
            <div class="form-step hidden" data-step="3">
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

            <!-- STEP 4: Konum -->
            <div class="form-step hidden" data-step="4">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Nerede arıyor?</h3>
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

            <!-- STEP 5: Bütçe -->
            <div class="form-step hidden" data-step="5">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Maksimum bütçesi nedir?</h3>
                <div class="relative max-w-sm mx-auto mt-8">
                    <input type="text" id="priceDisplay" placeholder="0"
                        class="w-full text-center text-4xl font-black text-gray-900 bg-transparent border-b-2 border-gray-200 focus:border-secondary outline-none py-2 placeholder-gray-200 transition-colors"
                        inputmode="numeric">
                    <input type="hidden" name="max_price" id="maxPriceInput" required>
                    <span
                        class="absolute right-0 bottom-4 text-2xl font-bold text-gray-400 pointer-events-none">₺</span>
                </div>
            </div>

            <!-- STEP 6: Detaylar -->
            <div class="form-step hidden" data-step="6">
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

            <!-- STEP 7: Süre -->
            <div class="form-step hidden" data-step="7">
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
                    Kullanıcı Adına Paylaş <span class="material-symbols-outlined text-sm">check</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Price formatter
    const priceInput = document.getElementById('priceDisplay');
    const hiddenPrice = document.getElementById('maxPriceInput');

    priceInput.addEventListener('input', function (e) {
        let value = this.value.replace(/\D/g, '');
        hiddenPrice.value = value;
        if (value) {
            this.value = parseInt(value).toLocaleString('tr-TR');
        } else {
            this.value = '';
        }
    });

    // Step Management
    let currentStep = 1;
    const totalSteps = 7; // Increased by 1
    const form = document.getElementById('searchForm');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const progressBar = document.getElementById('progressBar');
    const stepLabel = document.getElementById('stepLabel');
    const stepTitle = document.getElementById('stepTitle');

    // Step Titles Map
    const stepTitles = {
        1: 'Kullanıcı Seçimi',
        2: 'İşlem Türü',
        3: 'Gayrimenkul Türü',
        4: 'Konum',
        5: 'Bütçe',
        6: 'Detaylar',
        7: 'Yayın Süresi'
    };

    function updateStep(step) {
        document.querySelectorAll('.form-step').forEach(el => el.classList.add('hidden'));

        const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        currentStepEl.classList.remove('hidden');
        currentStepEl.classList.add('animate-fade-in-up');

        const progress = ((step) / totalSteps) * 100;
        progressBar.style.width = `${progress}%`;
        stepLabel.innerText = `Adım ${step}/${totalSteps}`;
        stepTitle.innerText = stepTitles[step];

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

    function validateStep(step) {
        const stepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        const inputs = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value) {
                isValid = false;
                input.classList.add('border-accent', 'ring-1', 'ring-accent');
                input.addEventListener('input', () => {
                    input.classList.remove('border-accent', 'ring-1', 'ring-accent');
                }, { once: true });
            }
        });
        return isValid;
    }

    nextBtn.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            currentStep++;
            updateStep(currentStep);
            window.scrollTo(0, 0);
        } else {
            alert('Lütfen zorunlu alanları doldurun.');
        }
    });

    prevBtn.addEventListener('click', () => {
        currentStep--;
        updateStep(currentStep);
        window.scrollTo(0, 0);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">progress_activity</span> Paylaşılıyor...';

            const formData = new FormData(form);
            const response = await fetch('../api/admin/talep-olustur.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert('Talep başarıyla oluşturuldu!');
                window.location.href = 'panel.php';
            } else {
                alert('Hata: ' + result.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Kullanıcı Adına Paylaş <span class="material-symbols-outlined text-sm">check</span>';
            }
        } catch (error) {
            console.error(error);
            alert('Bir hata oluştu.');
            submitBtn.disabled = false;
        }
    });

    // Start
    updateStep(1);
</script>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>