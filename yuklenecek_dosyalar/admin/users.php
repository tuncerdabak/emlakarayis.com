<?php
/**
 * Emlak Arayış - Admin Kullanıcı Yönetimi
 */
$pageTitle = 'Kullanıcı Yönetimi';
require_once __DIR__ . '/includes/header.php';

$users = getAllUsers($pdo);
$token = generateCSRFToken();

// İstatistikler
$totalUsers = count($users);
$activeUsers = count(array_filter($users, fn($u) => $u['is_active']));
$verifiedUsers = count(array_filter($users, fn($u) => $u['is_verified']));
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Kullanıcı Yönetimi</h1>
        <p class="text-gray-500 mt-1">Sistemdeki tüm kullanıcıları görüntüleyin ve yönetin</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="kullanici-olustur.php"
            class="flex items-center gap-2 px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg font-bold transition-colors shadow-sm">
            <span class="material-symbols-outlined text-lg">person_add</span>
            Yeni Kullanıcı
        </a>
        <a href="export-users.php"
            class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition-colors shadow-sm">
            <span class="material-symbols-outlined text-lg">download</span>
            Excel
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-2xl">group</span>
            </div>
            <div>
                <p class="text-2xl font-black text-gray-900"><?= $totalUsers ?></p>
                <p class="text-sm text-gray-500">Toplam Kullanıcı</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
            </div>
            <div>
                <p class="text-2xl font-black text-gray-900"><?= $activeUsers ?></p>
                <p class="text-sm text-gray-500">Aktif Kullanıcı</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                <span class="material-symbols-outlined text-purple-600 text-2xl">verified</span>
            </div>
            <div>
                <p class="text-2xl font-black text-gray-900"><?= $verifiedUsers ?></p>
                <p class="text-sm text-gray-500">Doğrulanmış</p>
            </div>
        </div>
    </div>
</div>

<!-- Users Table (Desktop) -->
<div class="hidden lg:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kullanıcı
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">İletişim
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kayıt</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlem</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-400 font-mono">#<?= $user['id'] ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center shrink-0">
                                    <span
                                        class="text-primary font-bold"><?= mb_strtoupper(mb_substr($user['agent_name'], 0, 1)) ?></span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900"><?= e($user['agent_name']) ?></p>
                                    <p class="text-sm text-gray-500"><?= e($user['agency_name']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <a href="tel:<?= formatPhone($user['phone']) ?>"
                                    class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">phone</span>
                                    <?= formatPhone($user['phone']) ?>
                                </a>
                                <?php if (!empty($user['instagram'])): ?>
                                    <p class="text-xs text-pink-500 mt-1"><?= e($user['instagram']) ?></p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-600"><?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                            <p class="text-xs text-gray-400"><?= date('H:i', strtotime($user['created_at'])) ?></p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col gap-1">
                                <?php if ($user['is_active']): ?>
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                        Pasif
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($user['password_hash'])): ?>
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600">
                                        <span class="material-symbols-outlined text-xs">lock</span>
                                        Şifreli
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="user-edit.php?id=<?= $user['id'] ?>"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Düzenle">
                                    <span class="material-symbols-outlined">edit</span>
                                </a>
                                <button onclick="toggleStatus(<?= $user['id'] ?>, <?= $user['is_active'] ? 0 : 1 ?>)"
                                    class="<?= $user['is_active'] ? 'text-red-600 hover:text-red-800 hover:bg-red-50' : 'text-green-600 hover:text-green-800 hover:bg-green-50' ?> px-3 py-1.5 rounded-lg font-bold text-sm transition-colors">
                                    <?= $user['is_active'] ? 'Pasife Al' : 'Aktif Yap' ?>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Users Cards (Mobile) -->
<div class="grid grid-cols-1 gap-4 lg:hidden">
    <?php foreach ($users as $user): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center shrink-0">
                        <span
                            class="text-primary font-bold"><?= mb_strtoupper(mb_substr($user['agent_name'], 0, 1)) ?></span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900"><?= e($user['agent_name']) ?></p>
                        <p class="text-xs text-gray-500"><?= e($user['agency_name']) ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400 font-mono italic">#<?= $user['id'] ?></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">İletişim</p>
                    <a href="tel:<?= formatPhone($user['phone']) ?>"
                        class="text-blue-600 font-medium"><?= formatPhone($user['phone']) ?></a>
                    <?php if (!empty($user['instagram'])): ?>
                        <p class="text-xs text-pink-500"><?= e($user['instagram']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Durum</p>
                    <div class="flex flex-wrap gap-1">
                        <?php if ($user['is_active']): ?>
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                <span class="w-1 h-1 bg-green-500 rounded-full"></span> Aktif
                            </span>
                        <?php else: ?>
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                                <span class="w-1 h-1 bg-red-500 rounded-full"></span> Pasif
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400">
                    Kayıt: <?= date('d.m.Y', strtotime($user['created_at'])) ?>
                </span>
                <div class="flex items-center gap-2">
                    <a href="user-edit.php?id=<?= $user['id'] ?>"
                        class="flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg font-bold text-xs">
                        <span class="material-symbols-outlined text-sm">edit</span> Düzenle
                    </a>
                    <button onclick="toggleStatus(<?= $user['id'] ?>, <?= $user['is_active'] ? 0 : 1 ?>)"
                        class="px-3 py-1.5 <?= $user['is_active'] ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' ?> rounded-lg font-bold text-xs">
                        <?= $user['is_active'] ? 'Pasife Al' : 'Aktif Yap' ?>
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    async function toggleStatus(userId, newStatus) {
        if (!confirm(newStatus ? 'Kullanıcıyı aktif hale getirmek istiyor musunuz?' : 'Kullanıcıyı pasife almak istiyor musunuz? Bu işlem kullanıcının giriş yapmasını engelleyecektir.')) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('is_active', newStatus);
            formData.append('csrf_token', '<?= $token ?>');

            const response = await fetch('../api/admin/user-status.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                window.location.reload();
            } else {
                alert('Hata: ' + result.message);
            }
        } catch (error) {
            console.error(error);
            alert('Bir hata oluştu');
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>