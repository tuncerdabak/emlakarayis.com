<?php
/**
 * Emlak Arayış - Admin: Talep Düzenleme
 */
$pageTitle = 'Talebi Düzenle';
require_once __DIR__ . '/includes/header.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT s.*, u.agent_name FROM searches s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
$stmt->execute([$id]);
$request = $stmt->fetch();

if (!$request) {
    echo "<div class='bg-red-50 text-red-600 p-4 rounded-xl'>Talep bulunamadı.</div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$token = generateCSRFToken();

// Güncelleme işlemi
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Güvenlik doğrulaması başarısız.'];
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE searches SET 
                transaction_type = ?, 
                property_type = ?, 
                city = ?, 
                district = ?, 
                neighborhood = ?, 
                max_price = ?, 
                features = ?, 
                special_note = ?,
                status = ?
                WHERE id = ?");

            $stmt->execute([
                sanitizeInput($_POST['transaction_type']),
                sanitizeInput($_POST['property_type']),
                sanitizeInput($_POST['city']),
                sanitizeInput($_POST['district']),
                sanitizeInput($_POST['neighborhood']),
                (int) $_POST['max_price'],
                sanitizeInput($_POST['features']),
                sanitizeInput($_POST['special_note']),
                sanitizeInput($_POST['status']),
                $id
            ]);

            $message = ['type' => 'success', 'text' => 'Talep başarıyla güncellendi.'];
            // Veriyi tazele
            $stmt = $pdo->prepare("SELECT s.*, u.agent_name FROM searches s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
            $stmt->execute([$id]);
            $request = $stmt->fetch();
        } catch (PDOException $e) {
            $message = ['type' => 'error', 'text' => 'Veritabanı hatası: ' . $e->getMessage()];
        }
    }
}
?>

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="talepler.php" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Talebi Düzenle <span class="text-gray-400 font-mono text-sm ml-2">#
                <?= $id ?>
            </span></h1>
    </div>

    <?php if ($message): ?>
        <div
            class="mb-6 p-4 rounded-xl <?= $message['type'] === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' ?>">
            <?= $message['text'] ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <form method="POST" class="p-6 space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">İşlem Türü</label>
                    <select name="transaction_type"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                        <option value="satilik" <?= $request['transaction_type'] === 'satilik' ? 'selected' : '' ?>>Satılık
                        </option>
                        <option value="kiralik" <?= $request['transaction_type'] === 'kiralik' ? 'selected' : '' ?>>Kiralık
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Gayrimenkul Türü</label>
                    <select name="property_type"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                        <?php foreach ($PROPERTY_TYPES as $key => $type): ?>
                            <option value="<?= $key ?>" <?= $request['property_type'] === $key ? 'selected' : '' ?>>
                                <?= $type['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">İl</label>
                    <select name="city"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                        <?php foreach ($CITIES as $city): ?>
                            <option value="<?= $city ?>" <?= $request['city'] === $city ? 'selected' : '' ?>>
                                <?= $city ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">İlçe</label>
                    <input type="text" name="district" value="<?= e($request['district']) ?>"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mahalle / Semt</label>
                <input type="text" name="neighborhood" value="<?= e($request['neighborhood']) ?>"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Maksimum Bütçe (₺)</label>
                <input type="number" name="max_price" value="<?= e($request['max_price']) ?>"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Özellikler</label>
                <textarea name="features" rows="4" maxlength="200"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary resize-none"><?= e($request['features']) ?></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Özel Not</label>
                <input type="text" name="special_note" value="<?= e($request['special_note']) ?>"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Durum</label>
                <select name="status"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-primary">
                    <option value="active" <?= $request['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                    <option value="passive" <?= $request['status'] === 'passive' ? 'selected' : '' ?>>Pasif</option>
                    <option value="deleted" <?= $request['status'] === 'deleted' ? 'selected' : '' ?>>Silinmiş</option>
                </select>
            </div>

            <div class="pt-4 flex items-center gap-4">
                <button type="submit"
                    class="flex-1 bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Değişiklikleri Kaydet
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 p-4 bg-gray-50 rounded-xl border border-gray-100">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-gray-400">person</span>
            <div class="text-sm">
                <span class="text-gray-500 uppercase font-bold text-[10px] block">İlan Sahibi</span>
                <span class="font-bold text-gray-900">
                    <?= e($request['agent_name']) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>