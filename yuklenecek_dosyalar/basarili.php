<?php
/**
 * Emlak Arayış - Başarılı İşlem
 */
require_once 'config.php';
require_once 'includes/functions.php';

// Eğer arayış ID parametresi yoksa ana sayfaya yönlendir
$searchId = $_GET['id'] ?? null;
if (!$searchId) {
    header('Location: index.php');
    exit;
}
// Arayış bilgilerini çek
$stmt = $pdo->prepare("SELECT * FROM searches WHERE id = ?");
$stmt->execute([$searchId]);
$search = $stmt->fetch();

if (!$search) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Arayış Yayında';
require_once 'includes/header.php';
?>

<div class="min-h-[calc(100vh-64px)] bg-gray-50 flex items-center justify-center p-4">
    <div
        class="bg-white w-full max-w-lg rounded-3xl shadow-xl border border-gray-100 p-8 text-center relative overflow-hidden animate-fade-in-up">

        <!-- Background Confetti (CSS only representation) -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
            <div class="absolute top-10 left-10 w-2 h-2 bg-red-400 rounded-full opacity-50"></div>
            <div class="absolute top-20 right-20 w-3 h-3 bg-blue-400 rounded-full opacity-50"></div>
            <div class="absolute bottom-10 left-1/3 w-2 h-2 bg-yellow-400 rounded-full opacity-50"></div>
        </div>

        <div
            class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 relative z-10 animate-bounce-slow">
            <span class="material-symbols-outlined !text-6xl text-green-500">check_circle</span>
        </div>

        <h1 class="text-3xl font-black text-gray-900 mb-2 relative z-10">Harika!</h1>
        <p class="text-gray-500 text-lg mb-8 relative z-10">Arayışınız başarıyla oluşturuldu ve meslektaşlarınızın
            ekranına düştü.</p>

        <!-- Summary Card -->
        <div class="bg-gray-50 rounded-xl p-4 mb-8 text-left border border-gray-200 relative z-10">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Özet</h3>
            <div class="flex items-center gap-3 mb-2">
                <span class="material-symbols-outlined text-primary">location_on</span>
                <span class="font-bold text-gray-800"><?= e($search['district']) ?>, <?= e($search['city']) ?></span>
            </div>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">currency_lira</span>
                <span class="font-bold text-gray-800"><?= formatPrice($search['max_price']) ?></span>
            </div>
        </div>

        <!-- Share Section -->
        <div class="mb-6 text-left relative z-10">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Meslektaşlarınızla Paylaşın</h3>

            <!-- WhatsApp Button -->
            <a href="<?= getWhatsAppShareLink($search) ?>" target="_blank"
                class="flex items-center justify-between w-full bg-[#25D366] hover:bg-[#128C7E] text-white p-5 rounded-2xl transition-all shadow-xl shadow-green-500/20 group mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined !text-3xl text-white">share</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-black leading-tight tracking-tight">WhatsApp Gruplarına Gönder</span>
                        <span class="text-[11px] opacity-90 font-medium uppercase tracking-widest mt-0.5">Dikkat Çekici
                            Mesaj Hazır!</span>
                    </div>
                </div>
                <span
                    class="material-symbols-outlined transition-transform group-hover:translate-x-1">arrow_forward_ios</span>
            </a>

            <!-- Story Generation Button -->
            <button id="generateStoryBtn" type="button"
                class="flex items-center justify-between w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white p-5 rounded-2xl transition-all shadow-xl shadow-purple-500/20 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined !text-3xl text-white">filter_vintage</span>
                    </div>
                    <div class="flex flex-col text-left"> <!-- text-left added to ensure alignment -->
                        <span class="text-lg font-black leading-tight tracking-tight">Görsel Olarak Paylaş</span>
                        <span class="text-[11px] opacity-90 font-medium uppercase tracking-widest mt-0.5">Instagram
                            Story & WP Durum</span>
                    </div>
                </div>
                <span class="material-symbols-outlined transition-transform group-hover:translate-x-1">image</span>
            </button>
        </div>

        <div class="flex flex-col gap-3 relative z-10 w-full pt-4 border-t border-gray-100">
            <a href="arayislar.php"
                class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-xl font-bold transition-all shadow-lg shadow-blue-900/20">
                Arayışları İncele
            </a>
            <a href="talep-gir.php"
                class="w-full bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 py-4 rounded-xl font-bold transition-all">
                Yeni Talep Gir
            </a>
        </div>

    </div>
</div>

</div>
</div>

<!-- Hidden Story Template (Off-screen) -->
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
                    <?php
                    $typeColor = $search['transaction_type'] == 'satilik' ? 'bg-emerald-500' : 'bg-orange-500';
                    $typeText = $search['transaction_type'] == 'satilik' ? 'SATILIK ARAYIŞI' : 'KİRALIK ARAYIŞI';
                    $icon = $search['transaction_type'] == 'satilik' ? 'sell' : 'key';
                    ?>
                    <div
                        class="<?= $typeColor ?> px-8 py-4 rounded-2xl flex items-center gap-4 shadow-xl shadow-black/20">
                        <span class="material-symbols-outlined text-4xl"><?= $icon ?></span>
                        <span class="text-4xl font-black tracking-wide"><?= $typeText ?></span>
                    </div>
                </div>

                <!-- Main Title -->
                <h1 class="text-7xl font-black leading-tight drop-shadow-md">
                    Müşterim İçin<br>
                    <span style="color: #c084fc;">
                        <?= $PROPERTY_TYPES[$search['property_type']]['label'] ?? 'Gayrimenkul' ?>
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
                                <div class="text-4xl font-bold"><?= e($search['district']) ?>, <?= e($search['city']) ?>
                                </div>
                                <?php if (!empty($search['neighborhood'])): ?>
                                    <div class="text-2xl text-gray-400 mt-1"><?= e($search['neighborhood']) ?></div>
                                <?php endif; ?>
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
                                <div class="text-5xl font-black text-green-400"><?= formatPrice($search['max_price']) ?>
                                </div>
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
                                <div class="text-3xl font-medium leading-normal">
                                    "<?= e($search['features']) ?>"
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

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="assets/js/story-generator.js"></script>

<?php require_once 'includes/footer.php'; ?>