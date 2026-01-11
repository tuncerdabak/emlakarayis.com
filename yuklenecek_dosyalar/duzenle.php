<?php
/**
 * Emlak Arayış - Arayış Düzenle
 */
require_once 'config.php';
require_once 'includes/functions.php';

// Kullanıcı girişi kontrolü
if (!isUserVerified()) {
    header('Location: dogrulama.php');
    exit;
}

$id = (int) ($_GET['id'] ?? 0);
$userId = $_SESSION['user_id'];

// Arayış bilgilerini çek ve yetki kontrolü yap
$stmt = $pdo->prepare("SELECT * FROM searches WHERE id = ? AND user_id = ? AND status != 'deleted'");
$stmt->execute([$id, $userId]);
$search = $stmt->fetch();

if (!$search) {
    header('Location: taleplerim.php');
    exit;
}

$pageTitle = 'Arayışı Düzenle';
require_once 'includes/header.php';
?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                <a href="taleplerim.php" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                Arayışı Düzenle
            </h1>
            <p class="text-gray-500 text-sm ml-9">Mevcut arayış bilgilerinizi güncelleyebilirsiniz.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <form id="editForm" class="space-y-6">
                <input type="hidden" name="id" value="<?= $search['id'] ?>">

                <!-- İşlem Türü -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">İşlem Türü</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="transaction_type" value="satilik" class="peer sr-only"
                                <?= $search['transaction_type'] == 'satilik' ? 'checked' : '' ?>>
                            <div
                                class="px-4 py-3 rounded-xl border border-gray-200 text-center peer-checked:bg-emerald-50 peer-checked:text-satilik peer-checked:border-satilik hover:bg-gray-50 font-semibold transition-all">
                                Satılık
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="transaction_type" value="kiralik" class="peer sr-only"
                                <?= $search['transaction_type'] == 'kiralik' ? 'checked' : '' ?>>
                            <div
                                class="px-4 py-3 rounded-xl border border-gray-200 text-center peer-checked:bg-orange-50 peer-checked:text-kiralik peer-checked:border-kiralik hover:bg-gray-50 font-semibold transition-all">
                                Kiralık
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Gayrimenkul Türü -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Gayrimenkul Türü</label>
                    <select name="property_type"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary">
                        <?php foreach ($PROPERTY_TYPES as $key => $type): ?>
                            <option value="<?= $key ?>" <?= $search['property_type'] == $key ? 'selected' : '' ?>>
                                <?= $type['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Konum -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">İl</label>
                        <select name="city"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary">
                            <?php foreach ($CITIES as $city): ?>
                                <option value="<?= $city ?>" <?= $search['city'] == $city ? 'selected' : '' ?>><?= $city ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">İlçe</label>
                        <input type="text" name="district" value="<?= e($search['district']) ?>" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mahalle (Opsiyonel)</label>
                    <input type="text" name="neighborhood" value="<?= e($search['neighborhood']) ?>"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary">
                </div>

                <!-- Bütçe -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Maksimum Bütçe (TL)</label>
                    <input type="text" id="priceDisplay" value="<?= number_format($search['max_price'], 0, '', '.') ?>"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary font-bold text-lg">
                    <input type="hidden" name="max_price" id="maxPriceInput" value="<?= $search['max_price'] ?>">
                </div>

                <!-- Detaylar -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Özellikler</label>
                    <textarea name="features" rows="4" maxlength="200" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary resize-none"><?= e($search['features']) ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Özel Not</label>
                    <input type="text" name="special_note" value="<?= e($search['special_note']) ?>" maxlength="100"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary">
                </div>

                <button type="submit" id="saveBtn"
                    class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-xl font-bold shadow-lg transition-all flex items-center justify-center gap-2">
                    Değişiklikleri Kaydet
                </button>
            </form>
        </div>
    </div>
</div>

<script>
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

    document.getElementById('editForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('saveBtn');
        const formData = new FormData(e.target);

        try {
            btn.disabled = true;
            btn.innerHTML = 'Kaydediliyor...';

            const response = await fetch('api/arayis-guncelle.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert('Arayış güncellendi.');
                window.location.href = 'taleplerim.php';
            } else {
                alert('Hata: ' + result.message);
                btn.disabled = false;
                btn.innerHTML = 'Değişiklikleri Kaydet';
            }
        } catch (error) {
            console.error(error);
            alert('Bir hata oluştu.');
            btn.disabled = false;
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>