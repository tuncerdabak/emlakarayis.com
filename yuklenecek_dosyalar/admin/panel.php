<?php
/**
 * Emlak Arayış - Admin Panel - Doğrulama Talepleri
 */
$pageTitle = 'Doğrulama Talepleri';
require_once __DIR__ . '/includes/header.php';

// Bekleyen talepleri getir
$requests = getPendingVerifications($pdo);
$token = generateCSRFToken();
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Doğrulama Talepleri</h1>
        <p class="text-gray-500 mt-1">Bekleyen kullanıcı doğrulama taleplerini yönetin</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="px-3 py-1.5 bg-amber-100 text-amber-800 rounded-full text-sm font-bold">
            <?= count($requests) ?> Bekleyen
        </span>
    </div>
</div>

<?php if (empty($requests)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-green-500 text-4xl">check_circle</span>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Harika!</h3>
        <p class="text-gray-500">Bekleyen doğrulama talebi bulunmuyor.</p>
    </div>
<?php else: ?>
    <div class="grid gap-4">
        <?php foreach ($requests as $req):
            $phone = formatPhone($req['phone']);
            ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4 justify-between">
                    <!-- User Info -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center shrink-0">
                            <span
                                class="text-primary font-bold text-lg"><?= mb_strtoupper(mb_substr($req['agent_name'], 0, 1)) ?></span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg"><?= e($req['agent_name']) ?></h3>
                            <div class="flex flex-wrap items-center gap-3 mt-1 text-sm text-gray-500">
                                <a href="tel:<?= $phone ?>" class="flex items-center gap-1 text-blue-600 hover:underline">
                                    <span class="material-symbols-outlined text-base">phone</span>
                                    <?= e($req['phone']) ?>
                                </a>
                                <?php if (!empty($req['agency_name'])): ?>
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-base text-gray-400">business</span>
                                        <?= e($req['agency_name']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($req['instagram'])): ?>
                                    <a href="https://instagram.com/<?= e(ltrim($req['instagram'], '@')) ?>" target="_blank"
                                        class="flex items-center gap-1 text-pink-600 hover:underline">
                                        <span class="material-symbols-outlined text-base">photo_camera</span>
                                        <?= e($req['instagram']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">
                                <span class="material-symbols-outlined text-xs align-middle">schedule</span>
                                <?= date('d.m.Y H:i', strtotime($req['created_at'])) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Status & Actions -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:shrink-0">
                        <div id="status-<?= $req['id'] ?>">
                            <?php if ($req['verification_code']): ?>
                                <div class="flex items-center gap-2 px-4 py-2 bg-amber-50 rounded-lg border border-amber-200">
                                    <span class="material-symbols-outlined text-amber-600">lock</span>
                                    <div>
                                        <p class="text-xs text-amber-600 font-medium">Kod Oluşturuldu</p>
                                        <p class="text-lg font-black text-amber-700"><?= e($req['verification_code']) ?></p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">
                                    Onay Bekliyor
                                </span>
                            <?php endif; ?>
                        </div>

                        <button onclick="generateCode(<?= $req['id'] ?>)"
                            class="flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-lg">check</span>
                            Onayla & Kod Üret
                        </button>

                        <a id="wa-link-<?= $req['id'] ?>" href="#" target="_blank"
                            class="hidden items-center justify-center gap-2 px-5 py-2.5 bg-[#25D366] hover:bg-[#128C7E] text-white rounded-lg font-bold transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-lg">send</span>
                            WhatsApp Gönder
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
    async function generateCode(id) {
        try {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('csrf_token', '<?= $token ?>');

            const response = await fetch('../api/admin/talep-onayla.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Update Status Badge
                const statusEl = document.getElementById(`status-${id}`);
                if (statusEl) {
                    statusEl.innerHTML = `
                        <div class="flex items-center gap-2 px-4 py-2 bg-green-50 rounded-lg border border-green-200">
                            <span class="material-symbols-outlined text-green-600">verified</span>
                            <div>
                                <p class="text-xs text-green-600 font-medium">Kod Hazır</p>
                                <p class="text-lg font-black text-green-700">${result.code}</p>
                            </div>
                        </div>
                    `;
                }

                // Show WhatsApp Button
                const waBtn = document.getElementById(`wa-link-${id}`);
                if (waBtn) {
                    waBtn.href = result.whatsappUrl;
                    waBtn.classList.remove('hidden');
                    waBtn.classList.add('flex');
                }
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error(error);
            alert('İşlem başarısız');
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>