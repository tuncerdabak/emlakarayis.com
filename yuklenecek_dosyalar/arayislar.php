<?php
/**
 * Emlak Arayış - Tüm Arayışlar
 */
// Config ve Fonksiyonlar
require_once 'config.php';
require_once 'includes/functions.php';

// Filtre parametrelerini al
$filters = [
    'city' => sanitizeInput($_GET['city'] ?? ''),
    'transaction_type' => sanitizeInput($_GET['action'] ?? ''), // 'type' yerine 'action' (URL conflict)
    'property_type' => sanitizeInput($_GET['property'] ?? ''),
    'search' => sanitizeInput($_GET['q'] ?? ''),
    'id' => sanitizeInput($_GET['id'] ?? '')
];

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = 12; // Sayfa başı kayıt
$offset = ($page - 1) * $limit;

// Arayışları getir
$searches = getActiveSearches($pdo, $limit, $offset, $filters);
$totalSearches = getTotalActiveSearches($pdo, $filters);
$totalPages = ceil($totalSearches / $limit);

// Şehir bazlı sayıları getir (Harita için)
$cityCounts = getRequestsCountByCity($pdo);

// Tekil Arayış SEO & Başlık Ayarları
if (!empty($filters['id']) && count($searches) === 1) {
    $s = $searches[0];
    $propInfo = getPropertyTypeInfo($s['property_type']);
    $transInfo = getTransactionTypeInfo($s['transaction_type']);

    // Sayfa Başlığı: Satılık Daire - İstanbul / Kadıköy
    $pageTitle = "{$transInfo['label']} {$propInfo['label']} - {$s['city']} / {$s['district']} | Emlak Arayış";

    // OG Title
    $ogTitle = $pageTitle;

    // OG Description: Özellikler ve Fiyat
    $features = mb_substr($s['features'], 0, 150) . (mb_strlen($s['features']) > 150 ? '...' : '');
    $price = formatPrice($s['max_price']);
    $ogDescription = "Bütçe: {$price} | {$features}";

    // OG Url
    $ogUrl = SITE_URL . "/arayislar.php?id=" . $s['id'];
} else {
    $pageTitle = 'Güncel Arayışlar';
}

require_once 'includes/header.php';
?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Turkey Map Section -->
        <div
            class="bg-white rounded-3xl shadow-xl border border-gray-100 p-3 md:p-6 mb-8 md:max-w-5xl mx-auto overflow-hidden relative group">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">map</span>
                        Talep Haritası
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">Türkiye genelindeki güncel müşteri talepleri</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-gray-200"></span>
                        <span class="text-xs font-medium text-gray-500">Talep Yok</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-secondary shadow-[0_0_8px_rgba(49,130,206,0.5)]"></span>
                        <span class="text-xs font-medium text-gray-500">Aktif Talep</span>
                    </div>
                </div>
            </div>

            <div
                class="relative w-full aspect-[2/1] bg-sky-50/30 rounded-2xl border border-sky-100 p-1 md:p-4 transition-all group-hover:shadow-inner overflow-hidden">
                <div id="map-loading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
                    <div class="w-8 h-8 border-4 border-secondary border-t-transparent rounded-full animate-spin"></div>
                </div>
                <?php
                $svgContent = file_get_contents(__DIR__ . '/turkiye.svg');
                // Remove potential script tags from SVG for security
                $svgContent = preg_replace('/<script[\s\S]*?>[\s\S]*?<\/script>/i', '', $svgContent);
                echo $svgContent;
                ?>

                <!-- Tooltip -->
                <div id="city-tooltip"
                    class="absolute hidden pointer-events-none z-20 bg-primary/95 backdrop-blur-md text-white px-3 py-2 rounded-xl text-xs font-bold shadow-2xl transition-all border border-white/20 whitespace-nowrap">
                    <div class="flex flex-col gap-0.5">
                        <span id="tooltip-city" class="text-secondary tracking-wide uppercase">Şehir</span>
                        <span id="tooltip-count" class="text-lg">0 Talep</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-8">
            <form action="" method="GET" class="flex flex-col gap-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div class="col-span-1 md:col-span-2 relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input type="text" name="q" value="<?= e($filters['search']) ?>"
                            placeholder="İl, ilçe veya mahalle ara..."
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary transition-all">
                    </div>

                    <!-- City Select -->
                    <div>
                        <select name="city"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-secondary cursor-pointer appearance-none">
                            <option value="">Tüm İller</option>
                            <?php foreach ($CITIES as $city): ?>
                                <option value="<?= $city ?>" <?= $filters['city'] === $city ? 'selected' : '' ?>><?= $city ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filter Button -->
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">filter_list</span>
                        Filtrele
                    </button>
                </div>

                <!-- Interactive Filtering System -->
                <div class="flex flex-col gap-4">
                    <!-- Top Level Tabs: Hepsi | Satılık | Kiralık -->
                    <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar">
                        <button type="button" onclick="setFilterType('')" id="btn-all"
                            class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all border-2">
                            Hepsi
                        </button>
                        <button type="button" onclick="setFilterType('satilik')" id="btn-satilik"
                            class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all border-2">
                            Satılık
                        </button>
                        <button type="button" onclick="setFilterType('kiralik')" id="btn-kiralik"
                            class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all border-2">
                            Kiralık
                        </button>
                    </div>

                    <!-- Hidden Inputs for Form Submission -->
                    <input type="hidden" name="action" id="input-action" value="<?= e($filters['transaction_type']) ?>">
                    <input type="hidden" name="property" id="input-property"
                        value="<?= e($filters['property_type']) ?>">

                    <!-- Sub Level Tabs: Property Types (Expandable) -->
                    <div id="property-types-container" class="hidden animate-fade-in-down">
                        <div class="flex flex-wrap gap-2 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <?php foreach ($PROPERTY_TYPES as $key => $type): ?>
                                <button type="button" onclick="setFilterProperty('<?= $key ?>')"
                                    class="prop-type-btn px-4 py-2 rounded-xl text-sm font-semibold border transition-all flex items-center gap-2 bg-white text-gray-600 border-gray-200 hover:border-gray-300"
                                    data-value="<?= $key ?>">
                                    <span class="material-symbols-outlined text-lg"><?= $type['icon'] ?></span>
                                    <?= $type['label'] ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script>
            // State
            let currentAction = '<?= e($filters['transaction_type']) ?>';
            let currentProperty = '<?= e($filters['property_type']) ?>';

            // DOM Elements
            const form = document.querySelector('form');
            const btnAll = document.getElementById('btn-all');
            const btnSatilik = document.getElementById('btn-satilik');
            const btnKiralik = document.getElementById('btn-kiralik');
            const propContainer = document.getElementById('property-types-container');
            const inputAction = document.getElementById('input-action');
            const inputProperty = document.getElementById('input-property');
            const propButtons = document.querySelectorAll('.prop-type-btn');

            function initFilters() {
                updateTypeButtonsUI();
                updatePropertyButtonsUI();

                if (currentAction === 'satilik' || currentAction === 'kiralik') {
                    propContainer.classList.remove('hidden');
                } else {
                    propContainer.classList.add('hidden');
                }
            }

            function setFilterType(type) {
                // If clicking the same active type, do nothing (or reset if you prefer)
                if (currentAction === type && type !== '') return;

                currentAction = type;
                inputAction.value = type;

                // Reset property type when switching main categories
                currentProperty = '';
                inputProperty.value = '';

                // UI Updates
                if (type === '') {
                    propContainer.classList.add('hidden');
                    // "Hepsi" clicked, submit immediately
                    form.submit();
                } else {
                    propContainer.classList.remove('hidden');
                    updateTypeButtonsUI();
                    updatePropertyButtonsUI(); // Clear property selection visual
                    // Don't submit yet, wait for property selection
                }
            }

            function setFilterProperty(prop) {
                currentProperty = prop;
                inputProperty.value = prop;
                updatePropertyButtonsUI();
                form.submit();
            }

            function updateTypeButtonsUI() {
                // Reset All
                const standardClass = 'bg-white text-gray-500 border-gray-200 hover:border-gray-400';
                btnAll.className = `px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all border-2 ${standardClass}`;
                btnSatilik.className = `px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all border-2 ${standardClass}`;
                btnKiralik.className = `px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all border-2 ${standardClass}`;

                // Set Active
                if (currentAction === 'satilik') {
                    btnSatilik.className = 'px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all border-2 bg-satilik text-white border-satilik shadow-lg shadow-green-200';
                } else if (currentAction === 'kiralik') {
                    btnKiralik.className = 'px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all border-2 bg-kiralik text-white border-kiralik shadow-lg shadow-orange-200';
                } else {
                    btnAll.className = 'px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all border-2 bg-gray-800 text-white border-gray-800';
                }
            }

            function updatePropertyButtonsUI() {
                let activeColorClass = '';
                let activeBorderClass = '';

                if (currentAction === 'satilik') {
                    activeColorClass = 'text-satilik bg-emerald-50';
                    activeBorderClass = 'border-satilik';
                } else if (currentAction === 'kiralik') {
                    activeColorClass = 'text-kiralik bg-orange-50';
                    activeBorderClass = 'border-kiralik';
                }

                propButtons.forEach(btn => {
                    const value = btn.getAttribute('data-value');
                    if (value === currentProperty && currentProperty !== '') {
                        btn.className = `prop-type-btn px-4 py-2 rounded-xl text-sm font-bold border-2 transition-all flex items-center gap-2 ${activeColorClass} ${activeBorderClass}`;
                    } else {
                        btn.className = 'prop-type-btn px-4 py-2 rounded-xl text-sm font-semibold border transition-all flex items-center gap-2 bg-white text-gray-600 border-gray-200 hover:border-gray-300 hover:bg-gray-50';
                    }
                });
            }

            // Init on load
            document.addEventListener('DOMContentLoaded', initFilters);

        </script>

        <!-- Connection Error / Empty State -->
        <?php if ($totalSearches == 0): ?>
            <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-gray-300 text-4xl">search_off</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Eşleşen arayış bulunamadı</h3>
                <p class="text-gray-500 mb-6">Filtreleri değiştirerek tekrar deneyebilirsin.</p>
                <a href="arayislar.php" class="text-secondary font-bold hover:underline">Filtreleri Temizle</a>
            </div>
        <?php else: ?>

            <div class="mb-4 text-sm text-gray-500 font-medium">
                Toplam <strong><?= $totalSearches ?></strong> aktif arayış listeleniyor
            </div>

            <!-- List Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($searches as $search):
                    $propInfo = getPropertyTypeInfo($search['property_type']);
                    $transInfo = getTransactionTypeInfo($search['transaction_type']);
                    $isRental = $search['transaction_type'] === 'kiralik';

                    $cardBorder = $isRental
                        ? 'border-orange-200 border-l-4 border-l-orange-500' // Kiralık: Turuncu sol çizgi
                        : 'border-emerald-200 border-l-4 border-l-emerald-500'; // Satılık: Yeşil sol çizgi
            
                    $badgeBg = $isRental ? 'bg-orange-100 text-orange-800' : 'bg-emerald-100 text-emerald-800';
                    $btnClass = $isRental ? 'bg-orange-50 text-orange-600 hover:bg-orange-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100';
                    ?>
                    <div
                        class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow border-t border-r border-b <?= $cardBorder ?> p-5 flex flex-col h-full relative group">

                        <!-- Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex gap-2">
                                <span
                                    class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider <?= $badgeBg ?>">
                                    <?= $transInfo['label'] ?>
                                </span>
                                <span
                                    class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider <?= $propInfo['bg'] ?> <?= $propInfo['text'] ?>">
                                    <?= $propInfo['label'] ?>
                                </span>
                            </div>
                            <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded">
                                <?= timeAgo($search['created_at']) ?>
                            </span>
                        </div>

                        <!-- Price -->
                        <div class="mb-2">
                            <span class="text-2xl font-black text-gray-900 tracking-tight">
                                <?= formatPrice($search['max_price']) ?>
                            </span>
                            <span class="text-xs text-gray-400 font-medium ml-1">bütçe</span>
                        </div>

                        <!-- Location -->
                        <div class="flex items-start gap-1 mb-3 text-gray-600">
                            <span class="material-symbols-outlined text-lg mt-0.5 text-gray-400">location_on</span>
                            <div class="text-sm font-semibold leading-relaxed">
                                <?= e($search['city']) ?> / <?= e($search['district']) ?>
                                <span
                                    class="block text-xs font-normal text-gray-400"><?= e($search['neighborhood'] ?? '') ?></span>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="bg-gray-50 rounded-lg p-3 mb-4 text-xs text-gray-600 font-medium italic min-h-[60px]">
                            "<?= mb_substr(e($search['features']), 0, 100) ?><?= strlen($search['features']) > 100 ? '...' : '' ?>"
                        </div>

                        <!-- Footer / Actions -->
                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <div
                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs shrink-0">
                                    <?= mb_substr($search['agent_name'], 0, 1) ?>
                                </div>
                                <div class="flex flex-col truncate">
                                    <span
                                        class="text-xs font-bold text-gray-900 truncate"><?= mb_substr($search['agent_name'], 0, 15) ?></span>
                                    <span
                                        class="text-[10px] text-gray-400 uppercase font-semibold truncate"><?= mb_substr($search['agency_name'], 0, 15) ?></span>
                                </div>
                            </div>

                            <div class="flex gap-2 shrink-0">
                                <a href="<?= getWhatsAppShareLink($search) ?>"
                                    target="_blank"
                                    class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200"
                                    title="WhatsApp'ta Paylaş">
                                    <span class="material-symbols-outlined text-lg">share</span>
                                </a>
                                <a href="tel:<?= formatPhone($search['phone']) ?>"
                                    class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200"
                                    title="Ara">
                                    <span class="material-symbols-outlined text-lg">call</span>
                                </a>
                                <a href="<?= whatsappLink($search['phone'], "Merhaba, EmlakArayış'taki #{$search['id']} nolu {$search['city']} arayışınız için yazıyorum. İlanı gör: " . SITE_URL . "/arayislar.php?id={$search['id']}") ?>"
                                    target="_blank"
                                    class="w-9 h-9 rounded-lg flex items-center justify-center transition-colors bg-green-50 text-green-600 hover:bg-green-100 border border-green-200"
                                    title="Sohbet Başlat">
                                    <span class="material-symbols-outlined text-lg">chat</span>
                                </a>
                            </div>
                        </div>

                        <!-- Expires Badge -->
                        <div class="absolute top-0 right-0 -mt-2 -mr-2">
                            <span
                                class="bg-white text-[10px] font-bold text-gray-400 border border-gray-200 px-2 py-1 rounded-md shadow-sm">
                                <?= remainingTime($search['expires_at']) ?>
                            </span>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="mt-12 flex justify-center">
                    <nav class="flex gap-2">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                                class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-bold transition-colors <?= $page === $i ? 'bg-primary text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </nav>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>

<script>
    /**
     * Harita Verileri ve Mantığı
     */
    (function () {
        const cityCounts = <?= json_encode($cityCounts) ?>;
        const tooltip = document.getElementById('city-tooltip');
        const tooltipCity = document.getElementById('tooltip-city');
        const tooltipCount = document.getElementById('tooltip-count');
        const mapLoading = document.getElementById('map-loading');
        const svg = document.getElementById('svg-turkey');

        if (!svg) {
            console.error("SVG map not found!");
            if (mapLoading) mapLoading.classList.add('hidden');
            return;
        }

        const cities = svg.querySelectorAll('g[data-city-name]');

        // Renk skalası
        function getCityColor(count) {
            if (count === 0) return '#f1f5f9'; // slate-100
            if (count < 5) return '#90cdf4';   // blue-200
            if (count < 10) return '#4299e1';  // blue-400
            if (count < 25) return '#3182ce';  // blue-600
            return '#1e4e8c';                  // blue-800 (koyu)
        }

        function initMap() {
            cities.forEach(cityGroup => {
                const cityName = cityGroup.getAttribute('data-city-name');
                const count = cityCounts[cityName] || 0;
                const paths = cityGroup.querySelectorAll('path');

                // Renklendir
                const color = getCityColor(count);
                paths.forEach(p => {
                    p.style.fill = color;
                    p.style.stroke = '#fff';
                    p.style.strokeWidth = '0.5';
                    p.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                });

                cityGroup.style.cursor = 'pointer';

                // Hover efektleri
                cityGroup.addEventListener('mouseenter', (e) => {
                    paths.forEach(p => {
                        p.style.fill = (count > 0 ? '#1a365d' : '#cbd5e0');
                        p.style.filter = 'drop-shadow(0 4px 6px rgba(0,0,0,0.1))';
                    });

                    tooltipCity.textContent = cityName;
                    tooltipCount.textContent = count + ' Talep';
                    tooltip.classList.remove('hidden');
                });

                cityGroup.addEventListener('mousemove', (e) => {
                    const rect = svg.getBoundingClientRect();
                    const containerRect = svg.parentElement.getBoundingClientRect();

                    // Tooltip konumlandırma (SVG içi koordinatlar)
                    const x = e.clientX - containerRect.left + 15;
                    const y = e.clientY - containerRect.top - 50;

                    tooltip.style.left = x + 'px';
                    tooltip.style.top = y + 'px';
                });

                cityGroup.addEventListener('mouseleave', () => {
                    paths.forEach(p => {
                        p.style.fill = color;
                        p.style.filter = 'none';
                    });
                    tooltip.classList.add('hidden');
                });

                // Tıklama ile filtreleme
                cityGroup.addEventListener('click', () => {
                    const select = document.querySelector('select[name="city"]');
                    if (select) {
                        select.value = cityName;
                        document.querySelector('form').submit();
                    }
                });

                // Sayı etiketleri (Sadece talebi olan iller için)
                if (count > 0 && count < 1000) { // Çok uçuk rakamlar için koruma
                    try {
                        const bbox = cityGroup.getBBox();
                        const text = document.createElementNS("http://www.w3.org/2000/svg", "text");

                        // Merkeze koyuyoruz
                        text.setAttribute("x", bbox.x + bbox.width / 2);
                        text.setAttribute("y", bbox.y + bbox.height / 2);
                        text.setAttribute("text-anchor", "middle");
                        text.setAttribute("alignment-baseline", "middle");
                        text.setAttribute("class", "pointer-events-none select-none");
                        text.style.fontSize = "11px";
                        text.style.fontWeight = "900";
                        text.style.fill = "white";
                        text.style.paintOrder = "stroke";
                        text.style.stroke = "rgba(0,0,0,0.3)";
                        text.style.strokeWidth = "2px";
                        text.textContent = count;

                        // Bazı çok küçük veya parçalı iller için manuel düzeltme (Gerekirse)
                        if (cityName === 'İstanbul') text.setAttribute("y", (bbox.y + bbox.height / 2) - 5);

                        svg.appendChild(text);
                    } catch (err) {
                        console.error("Label placement error for " + cityName, err);
                    }
                }
            });

            if (mapLoading) mapLoading.classList.add('hidden');
        }

        // Başlat
        // SVG inline basıldığı için DOMContentLoaded yeterli olmalı
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMap);
        } else {
            initMap();
        }
    })();
</script>
</div>
</div>

<?php require_once 'includes/footer.php'; ?>