<?php
/**
 * Emlak Arayış - Admin: Tüm Talepler
 */
$pageTitle = 'Tüm Talepler';
require_once __DIR__ . '/includes/header.php';

// Filtreler
$filters = [
    'transaction_type' => sanitizeInput($_GET['type'] ?? ''),
    'property_type' => sanitizeInput($_GET['property'] ?? ''),
    'city' => sanitizeInput($_GET['city'] ?? ''),
    'search' => sanitizeInput($_GET['search'] ?? ''),
    'status' => sanitizeInput($_GET['status'] ?? '')
];

// Sayfalama
$page = (int) ($_GET['p'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Talepleri getir (Özel bir admin fonksiyonu yazacağız veya mevcut olanı modifiye edeceğiz)
// getAllRequestsForAdmin($pdo, $limit, $offset, $filters)
function getAllRequestsForAdmin($pdo, $limit, $offset, $filters = [])
{
    $sql = "SELECT s.*, u.agent_name, u.phone 
            FROM searches s 
            LEFT JOIN users u ON s.user_id = u.id 
            WHERE 1=1";
    $params = [];

    if (!empty($filters['transaction_type'])) {
        $sql .= " AND s.transaction_type = ?";
        $params[] = $filters['transaction_type'];
    }
    if (!empty($filters['property_type'])) {
        $sql .= " AND s.property_type = ?";
        $params[] = $filters['property_type'];
    }
    if (!empty($filters['city'])) {
        $sql .= " AND s.city = ?";
        $params[] = $filters['city'];
    }
    if (!empty($filters['status'])) {
        $sql .= " AND s.status = ?";
        $params[] = $filters['status'];
    }
    if (!empty($filters['search'])) {
        $sql .= " AND (s.district LIKE ? OR s.neighborhood LIKE ? OR s.features LIKE ?)";
        $search = "%{$filters['search']}%";
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }

    $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
    $params[] = (int) $limit;
    $params[] = (int) $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getTotalRequestsForAdmin($pdo, $filters = [])
{
    $sql = "SELECT COUNT(*) FROM searches s WHERE 1=1";
    $params = [];

    if (!empty($filters['transaction_type'])) {
        $sql .= " AND s.transaction_type = ?";
        $params[] = $filters['transaction_type'];
    }
    if (!empty($filters['status'])) {
        $sql .= " AND s.status = ?";
        $params[] = $filters['status'];
    }
    // ... diğer filtreler eklenebilir

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

$requests = getAllRequestsForAdmin($pdo, $limit, $offset, $filters);
$total = getTotalRequestsForAdmin($pdo, $filters);
$totalPages = ceil($total / $limit);
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tüm Talepler</h1>
        <p class="text-gray-500 mt-1">Sistemdeki tüm ilan arayışlarını yönetin</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-8">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <input type="text" name="search" value="<?= e($filters['search']) ?>"
            placeholder="İlçe, mahalle veya özellik..."
            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-primary text-sm">

        <select name="type"
            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-primary text-sm">
            <option value="">İşlem Türü</option>
            <option value="satilik" <?= $filters['transaction_type'] === 'satilik' ? 'selected' : '' ?>>Satılık</option>
            <option value="kiralik" <?= $filters['transaction_type'] === 'kiralik' ? 'selected' : '' ?>>Kiralık</option>
        </select>

        <select name="status"
            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-primary text-sm">
            <option value="">Durum</option>
            <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
            <option value="passive" <?= $filters['status'] === 'passive' ? 'selected' : '' ?>>Pasif</option>
            <option value="deleted" <?= $filters['status'] === 'deleted' ? 'selected' : '' ?>>Silinmiş</option>
        </select>

        <button type="submit"
            class="w-full px-4 py-2 bg-primary text-white rounded-lg font-bold text-sm hover:bg-primary-dark transition-colors">
            Filtrele
        </button>

        <a href="talepler.php"
            class="w-full px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-bold text-sm text-center hover:bg-gray-200 transition-colors">
            Sıfırla
        </a>
    </form>
</div>

<!-- Talepler Table (Desktop) -->
<div class="hidden lg:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Tarih</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Kullanıcı</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Tip / Konum</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Bütçe</th>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Durum</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">İşlem</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($requests as $req): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= date('d.m.Y', strtotime($req['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">
                            <?= e($req['agent_name']) ?>
                        </div>
                        <div class="text-xs text-gray-500">
                            <?= formatPhone($req['phone']) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">
                            <?= mb_convert_case($req['transaction_type'], MB_CASE_TITLE) ?>
                            <?= mb_convert_case($req['property_type'], MB_CASE_TITLE) ?>
                        </div>
                        <div class="text-xs text-gray-500">
                            <?= e($req['city']) ?> /
                            <?= e($req['district']) ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-gray-900">
                        <?= formatPrice($req['max_price']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if ($req['status'] === 'active'): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold">Aktif</span>
                        <?php elseif ($req['status'] === 'passive'): ?>
                            <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold">Pasif</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-bold">Silinmiş</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="talep-edit.php?id=<?= $req['id'] ?>"
                            class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Talepler Cards (Mobile) -->
<div class="grid grid-cols-1 gap-4 lg:hidden">
    <?php foreach ($requests as $req): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 space-y-3">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400">
                        <?= date('d.m.Y H:i', strtotime($req['created_at'])) ?>
                    </span>
                    <h3 class="font-bold text-gray-900">
                        <?= mb_convert_case($req['transaction_type'], MB_CASE_TITLE) ?>
                        <?= mb_convert_case($req['property_type'], MB_CASE_TITLE) ?>
                    </h3>
                    <p class="text-xs text-gray-500">
                        <?= e($req['city']) ?> /
                        <?= e($req['district']) ?>
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm font-black text-blue-600">
                        <?= formatPrice($req['max_price']) ?>
                    </div>
                    <?php if ($req['status'] === 'active'): ?>
                        <span class="text-[10px] font-bold text-green-600">● Aktif</span>
                    <?php else: ?>
                        <span class="text-[10px] font-bold text-red-600">● Pasif</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="pt-3 border-t border-gray-50 flex items-center justify-between">
                <div class="text-xs text-gray-600">
                    <span class="font-bold">
                        <?= e($req['agent_name']) ?>
                    </span>
                </div>
                <a href="talep-edit.php?id=<?= $req['id'] ?>"
                    class="flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg font-bold text-xs">
                    <span class="material-symbols-outlined text-sm">edit</span> Düzenle
                </a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <div class="mt-8 flex justify-center gap-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?p=<?= $i ?>&type=<?= urlencode((string)$filters['transaction_type']) ?>&status=<?= urlencode((string)$filters['status']) ?>&search=<?= urlencode((string)$filters['search']) ?>"
                class="w-10 h-10 flex items-center justify-center rounded-lg font-bold transition-all <?= $page === $i ? 'bg-primary text-white scale-110 shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>