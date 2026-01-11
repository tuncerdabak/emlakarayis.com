<?php
/**
 * Emlak Arayış - Arayışlarım
 */
require_once 'config.php';
require_once 'includes/functions.php';

// Kullanıcı girişi zorunlu
if (!isUserVerified()) {
    header('Location: giris.php');
    exit;
}

$pageTitle = 'Taleplerim';
require_once 'includes/header.php';

$userId = $_SESSION['user_id'];

// Kullanıcının arayışlarını getir
$stmt = $pdo->prepare("SELECT * FROM searches WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$searches = $stmt->fetchAll();
?>

<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Taleplerim</h1>
            <a href="talep-gir.php"
                class="inline-flex items-center gap-2 px-4 py-2 bg-secondary text-white rounded-lg font-bold text-sm hover:bg-primary transition-colors">
                <span class="material-symbols-outlined text-lg">add</span>
                Yeni Ekle
            </a>
        </div>

        <?php if (empty($searches)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-gray-400 text-3xl">inbox</span>
                </div>
                <h3 class="text-gray-900 font-bold mb-2">Henüz talebiniz yok</h3>
                <p class="text-gray-500 text-sm mb-6">Müşterileriniz için talep girerek meslektaşlarınıza ulaşabilirsiniz.
                </p>
                <a href="talep-gir.php" class="text-secondary font-bold hover:underline">İlk talebinizi oluşturun</a>
            </div>
        <?php else: ?>
            <div class="grid gap-4">
                <?php foreach ($searches as $search):
                    $propInfo = getPropertyTypeInfo($search['property_type']);
                    $transInfo = getTransactionTypeInfo($search['transaction_type']);
                    $isActive = $search['status'] === 'active' && new DateTime($search['expires_at']) > new DateTime();
                    ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 group transition-all hover:shadow-md">
                        <div class="flex flex-col sm:flex-row gap-4 justify-between sm:items-center">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider <?= $transInfo['light_bg'] ?> <?= $transInfo['text'] ?>">
                                        <?= $transInfo['label'] ?>
                                    </span>
                                    <span class="text-xs text-gray-500 font-semibold">•</span>
                                    <span class="text-xs font-bold text-gray-700"><?= $propInfo['label'] ?></span>
                                    <?php if (!$isActive): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500">PASİF /
                                            SÜRESİ DOLDU</span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="font-bold text-gray-900 text-lg mb-1">
                                    <?= e($search['city']) ?> / <?= e($search['district']) ?>
                                    <span class="text-gray-400 font-normal text-sm"><?= e($search['neighborhood']) ?></span>
                                </h3>

                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="font-bold text-gray-900"><?= formatPrice($search['max_price']) ?></span>
                                    <span><?= remainingTime($search['expires_at']) ?></span>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-2 pt-4 sm:pt-0 border-t sm:border-0 border-gray-100 place-self-end sm:place-self-auto w-full sm:w-auto">
                                <button type="button" onclick="generateStory(<?= $search['id'] ?>)"
                                    class="w-10 h-10 flex items-center justify-center bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg transition-all shrink-0 shadow-sm"
                                    title="Görsel Oluştur">
                                    <span class="material-symbols-outlined text-lg">image</span>
                                </button>
                                <a href="<?= getWhatsAppShareLink($search) ?>" target="_blank"
                                    class="w-10 h-10 flex items-center justify-center bg-[#25D366] hover:bg-[#128C7E] text-white rounded-lg transition-all shrink-0 shadow-sm"
                                    title="WhatsApp'ta Paylaş">
                                    <span class="material-symbols-outlined text-lg font-bold">share</span>
                                </a>
                                <a href="duzenle.php?id=<?= $search['id'] ?>"
                                    class="flex-1 sm:flex-none px-4 py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg font-bold text-sm transition-colors text-center border border-gray-200">
                                    Düzenle
                                </a>
                                <button onclick="deleteSearch(<?= $search['id'] ?>)"
                                    class="flex-1 sm:flex-none px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-bold text-sm transition-colors border border-red-100">
                                    Sil
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Hidden Story Template (Off-screen) - will be dynamically updated -->
<div class="fixed left-[-9999px] top-0 pointer-events-none">
    <div id="storyCardTemplate" class="w-[1080px] h-[1920px] bg-white relative overflow-hidden font-sans">

        <!-- Background Image/Gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900"></div>

        <!-- Decorative Shapes -->
        <div class="absolute top-[-10%] left-[-20%] w-[800px] h-[800px] rounded-full bg-purple-600/30 blur-[100px]">
        </div>
        <div class="absolute bottom-[-10%] right-[-20%] w-[800px] h-[800px] rounded-full bg-blue-600/30 blur-[100px]">
        </div>

        <!-- Content Container -->
        <div class="relative z-10 h-full flex flex-col p-[80px] justify-between text-white">

            <!-- Top: Header -->
            <div class="flex items-center gap-6">
                <!-- Branding -->
                <div class="bg-white/10 backdrop-blur-lg px-8 py-4 rounded-full border border-white/20">
                    <span class="text-3xl font-bold tracking-wider">EMLAKARAYIS.COM</span>
                </div>
                <div class="h-px bg-white/30 flex-1"></div>
                <div class="text-3xl font-medium opacity-80"><?= date('d.m.Y') ?></div>
            </div>

            <!-- Middle: Main Call to Action -->
            <div class="flex flex-col gap-10 mt-20">

                <!-- Tag -->
                <div class="self-start">
                    <div id="storyTypeTag"
                        class="bg-emerald-500 px-8 py-4 rounded-2xl flex items-center gap-4 shadow-xl shadow-black/20">
                        <span class="material-symbols-outlined text-4xl" id="storyTypeIcon">sell</span>
                        <span class="text-4xl font-black tracking-wide" id="storyTypeText">SATILIK ARAYIŞI</span>
                    </div>
                </div>

                <!-- Main Title -->
                <h1 class="text-7xl font-black leading-tight drop-shadow-md">
                    Müşterim İçin<br>
                    <span style="color: #c084fc;" id="storyPropertyType">
                        Gayrimenkul
                    </span>
                    Arıyorum!
                </h1>

                <!-- Details Box -->
                <div class="bg-white/10 backdrop-blur-2xl rounded-[40px] p-10 border border-white/20 shadow-2xl mt-10">
                    <div class="space-y-8">
                        <!-- Location -->
                        <div class="flex items-start gap-6">
                            <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-4xl text-purple-300">location_on</span>
                            </div>
                            <div>
                                <div class="text-2xl text-gray-300 font-medium mb-1">Lokasyon</div>
                                <div class="text-4xl font-bold" id="storyLocation">İstanbul, Kadıköy</div>
                                <div class="text-2xl text-gray-400 mt-1" id="storyNeighborhood"></div>
                            </div>
                        </div>

                        <div class="h-px bg-white/10 w-full"></div>

                        <!-- Price -->
                        <div class="flex items-start gap-6">
                            <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-4xl text-green-300">payments</span>
                            </div>
                            <div>
                                <div class="text-2xl text-gray-300 font-medium mb-1">Bütçe</div>
                                <div class="text-5xl font-black text-green-400" id="storyPrice">1.000.000 ₺</div>
                            </div>
                        </div>

                        <div class="h-px bg-white/10 w-full"></div>

                        <!-- Features -->
                        <div class="flex items-start gap-6">
                            <div class="w-16 h-16 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-4xl text-blue-300">list</span>
                            </div>
                            <div>
                                <div class="text-2xl text-gray-300 font-medium mb-1">Aranan Özellikler</div>
                                <div class="text-3xl font-medium leading-normal" id="storyFeatures">
                                    "Özellikler burada görünecek"
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom: Footer -->
            <div class="mt-auto pt-20 text-center">
                <div class="inline-block bg-white text-slate-900 px-10 py-6 rounded-full font-bold text-3xl shadow-xl">
                    Detaylar İçin DM 📩
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal for Preview -->
<div id="storyPreviewModal"
    class="fixed inset-0 z-50 bg-black/90 hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div
        class="bg-white rounded-3xl w-full max-w-sm max-h-[90vh] overflow-hidden flex flex-col relative animate-fade-in-up">

        <!-- Modal Header -->
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Görsel Hazır!</h3>
            <button id="closeStoryModal"
                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
                <span class="material-symbols-outlined text-gray-600">close</span>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-4 flex-1 overflow-y-auto bg-gray-50 flex items-center justify-center min-h-[300px]">

            <!-- Loading State -->
            <div id="storyLoading" class="flex flex-col items-center gap-3 py-10">
                <span class="material-symbols-outlined animate-spin text-4xl text-primary">progress_activity</span>
                <span class="text-gray-500 font-medium">Görsel oluşturuluyor...</span>
            </div>

            <!-- Image Preview -->
            <div id="storyPreviewContent" class="hidden w-full h-full flex items-center justify-center">
                <img id="generatedStoryImage" src="" alt="Story Preview"
                    class="w-full h-auto rounded-xl shadow-lg border border-gray-200">
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-gray-100 bg-white">
            <a id="downloadStoryBtn" href="#" download="story.jpg"
                class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 transition-all shadow-lg shadow-purple-500/20">
                <span class="material-symbols-outlined">download</span> Görseli İndir
            </a>
            <p class="text-center text-xs text-gray-400 mt-2">İndirdikten sonra WhatsApp veya Instagram'da
                paylaşabilirsiniz.</p>
        </div>
    </div>
</div>

<!-- Store search data for JS -->
<script>
    const searchesData = <?= json_encode($searches, JSON_UNESCAPED_UNICODE) ?>;
    const propertyTypes = <?= json_encode($PROPERTY_TYPES, JSON_UNESCAPED_UNICODE) ?>;

    function formatPrice(price) {
        return new Intl.NumberFormat('tr-TR').format(price) + ' ₺';
    }

    function generateStory(searchId) {
        const search = searchesData.find(s => s.id == searchId);
        if (!search) return;

        // Update story template with search data
        const typeColor = search.transaction_type === 'satilik' ? 'bg-emerald-500' : 'bg-orange-500';
        const typeText = search.transaction_type === 'satilik' ? 'SATILIK ARAYIŞI' : 'KİRALIK ARAYIŞI';
        const typeIcon = search.transaction_type === 'satilik' ? 'sell' : 'key';

        const tag = document.getElementById('storyTypeTag');
        tag.className = `${typeColor} px-8 py-4 rounded-2xl flex items-center gap-4 shadow-xl shadow-black/20`;
        document.getElementById('storyTypeIcon').textContent = typeIcon;
        document.getElementById('storyTypeText').textContent = typeText;

        document.getElementById('storyPropertyType').textContent = propertyTypes[search.property_type]?.label || 'Gayrimenkul';
        document.getElementById('storyLocation').textContent = `${search.district}, ${search.city}`;
        document.getElementById('storyNeighborhood').textContent = search.neighborhood || '';
        document.getElementById('storyPrice').textContent = formatPrice(search.max_price);
        document.getElementById('storyFeatures').textContent = search.features ? `"${search.features}"` : '"Belirtilmemiş"';

        // Open Modal & Show Loading
        const modal = document.getElementById('storyPreviewModal');
        const loadingState = document.getElementById('storyLoading');
        const previewContent = document.getElementById('storyPreviewContent');

        modal.classList.remove('hidden');
        loadingState.classList.remove('hidden');
        previewContent.classList.add('hidden');

        // Generate image with a small delay to allow DOM update
        setTimeout(async () => {
            try {
                const storyCard = document.getElementById('storyCardTemplate');
                const canvas = await html2canvas(storyCard, {
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    backgroundColor: null,
                    allowTaint: true
                });

                const imgData = canvas.toDataURL("image/jpeg", 0.9);

                const previewImage = document.getElementById('generatedStoryImage');
                const downloadBtn = document.getElementById('downloadStoryBtn');

                previewImage.src = imgData;
                downloadBtn.href = imgData;
                downloadBtn.download = `emlak-arayis-${searchId}.jpg`;

                loadingState.classList.add('hidden');
                previewContent.classList.remove('hidden');

            } catch (err) {
                console.error('Görsel oluşturma hatası:', err);
                alert('Görsel oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.');
                modal.classList.add('hidden');
            }
        }, 100);
    }

    // Close Modal
    document.getElementById('closeStoryModal').addEventListener('click', () => {
        document.getElementById('storyPreviewModal').classList.add('hidden');
    });

    document.getElementById('storyPreviewModal').addEventListener('click', (e) => {
        if (e.target === document.getElementById('storyPreviewModal')) {
            document.getElementById('storyPreviewModal').classList.add('hidden');
        }
    });

    async function deleteSearch(id) {
        if (!confirm('Bu arayışı silmek istediğinize emin misiniz?')) return;

        try {
            const formData = new FormData();
            formData.append('id', id);

            const response = await fetch('api/arayis-sil.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                alert('Arayış silindi.');
                window.location.reload();
            } else {
                alert(result.message);
            }
        } catch (e) {
            console.error(e);
            alert('Bir hata oluştu.');
        }
    }
</script>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<?php require_once 'includes/footer.php'; ?>