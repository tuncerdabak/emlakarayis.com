require_once 'config.php';
require_once 'includes/functions.php';

// Eğer kullanıcı zaten doğrulanmışsa Keşfet sayfasına yönlendir
if (isUserVerified()) {
header('Location: arayislar.php');
exit;
}

$pageTitle = 'Müşteriniz Hazır ise, Portföyü Meslektaşınızdan Bulun';
require_once 'includes/header.php';

// Son eklenen arayışları getir (6 adet)
$latestSearches = getActiveSearches($pdo, 6);
?>

<!-- Hero Section -->
<section class="relative pt-12 pb-20 lg:pt-24 lg:pb-32 overflow-hidden bg-white">
    <!-- App Download Banner (Mobile Only) -->
    <div class="md:hidden mb-8 px-4">
        <div
            class="bg-gradient-to-r from-[#1A365D] to-[#2C5282] rounded-2xl p-4 flex items-center justify-between shadow-lg shadow-blue-900/20 text-white relative overflow-hidden">
            <!-- Deco -->
            <div class="absolute -right-6 top-1/2 -translate-y-1/2 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>

            <div class="flex items-center gap-4 relative z-10">
                <div
                    class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm shrink-0">
                    <span class="material-symbols-outlined text-2xl">android</span>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Mobil Uygulama Yayında!</h3>
                    <p class="text-xs text-blue-200">Daha hızlı ve pratik kullanım.</p>
                </div>
            </div>
            <a href="indir.php"
                class="bg-white text-[#1A365D] px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-50 transition-colors shrink-0 shadow-sm relative z-10">
                İndir
            </a>
        </div>
    </div>
    <!-- Background Blobs -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div
            class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-100/50 rounded-full blur-[100px] mix-blend-multiply">
        </div>
        <div
            class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-indigo-100/50 rounded-full blur-[100px] mix-blend-multiply">
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <!-- Badge -->
        <div
            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-primary text-xs font-semibold uppercase tracking-wide mb-8 animate-fade-in-up">
            <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
            Sadece Emlak Danışmanları İçin
        </div>

        <!-- Headline -->
        <h1
            class="text-4xl sm:text-5xl lg:text-7xl font-black text-primary leading-[1.1] tracking-tight mb-8 max-w-5xl mx-auto animate-fade-in-up [animation-delay:100ms]">
            Müşteriniz Hazır ise,<br />
            <span class="text-secondary relative inline-block">
                Portföyü Meslektaşınızdan Bulun.
                <svg class="absolute w-full h-3 -bottom-1 left-0 text-secondary/20 -z-10" preserveAspectRatio="none"
                    viewBox="0 0 100 10">
                    <path d="M0 5 Q 50 10 100 5" fill="none" stroke="currentColor" stroke-width="8"></path>
                </svg>
            </span>
        </h1>

        <!-- Subheadline -->
        <p
            class="text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto mb-12 leading-relaxed animate-fade-in-up [animation-delay:200ms]">
            Müşteriniz için aradığınız mülkü yazın.
            Uygun mülkü olan meslektaşlarınız sizinle iletişime geçsin.
            Zaman kaybetmeyin, doğru eşleşmeyi bulun.
        </p>

        <!-- Features Grid -->
        <div
            class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto mb-16 animate-fade-in-up [animation-delay:300ms]">
            <div
                class="flex flex-col items-center p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                <div
                    class="w-12 h-12 rounded-full bg-red-50 text-accent flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined filled">no_accounts</span>
                </div>
                <h3 class="font-bold text-gray-900">Üyelik Yok</h3>
                <p class="text-sm text-gray-500">Form doldurma derdi yok</p>
            </div>

            <div
                class="flex flex-col items-center p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                <div
                    class="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined filled">filter_list_off</span>
                </div>
                <h3 class="font-bold text-gray-900">İlan Kirliliği Yok</h3>
                <p class="text-sm text-gray-500">Sadece güncel müşteri arayışları</p>
            </div>

            <div
                class="flex flex-col items-center p-6 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                <div
                    class="w-12 h-12 rounded-full bg-green-50 text-satilik flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined filled">verified_user</span>
                </div>
                <h3 class="font-bold text-gray-900">Tek Seferlik Doğrulama</h3>
                <p class="text-sm text-gray-500">Güvenli meslektaş ağı</p>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div
            class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up [animation-delay:400ms]">
            <a href="talep-gir.php"
                class="w-full sm:w-auto h-14 px-10 bg-secondary hover:bg-primary text-white rounded-xl font-bold text-lg shadow-xl shadow-secondary/30 transition-all flex items-center justify-center gap-2 hover:-translate-y-1">
                <span class="material-symbols-outlined">add_circle</span>
                Hemen Talep Gir
            </a>
            <a href="arayislar.php"
                class="w-full sm:w-auto h-14 px-10 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-xl font-bold text-lg transition-all flex items-center justify-center gap-2 hover:-translate-y-1">
                <span class="material-symbols-outlined">search</span>
                Arayışları İncele
            </a>
        </div>
    </div>
</section>

<!-- Latest Searches Section -->
<section class="py-16 bg-background border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-secondary text-3xl">rss_feed</span>
                <h2 class="text-2xl font-bold text-primary">Son Paylaşılan Arayışlar</h2>
            </div>
            <a href="arayislar.php"
                class="text-secondary font-semibold hover:text-primary transition-colors flex items-center gap-1 group">
                Tümünü Gör
                <span
                    class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>

        <!-- Horizontal Scroll Container -->
        <?php if (empty($latestSearches)): ?>
            <div class="text-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-gray-400 text-3xl">inbox</span>
                </div>
                <p class="text-gray-500 font-medium">Henüz aktif arayış bulunmuyor.</p>
                <a href="talep-gir.php" class="text-secondary font-bold mt-2 inline-block hover:underline">İlk talebi siz
                    paylaşın!</a>
            </div>
        <?php else: ?>
            <div class="flex gap-4 overflow-x-auto pb-8 -mx-4 px-4 sm:mx-0 sm:px-0 snap-x no-scrollbar mask-fade">
                <?php foreach ($latestSearches as $search):
                    $propInfo = getPropertyTypeInfo($search['property_type']);
                    $transInfo = getTransactionTypeInfo($search['transaction_type']);
                    $isRental = $search['transaction_type'] === 'kiralik';

                    // Kart rengi - İşlem tipine göre
                    $cardBorder = $isRental ? 'border-orange-200 hover:border-orange-400' : 'border-emerald-200 hover:border-emerald-400';
                    $badgeBg = $isRental ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700';
                    $priceColor = $isRental ? 'text-orange-600' : 'text-emerald-600';
                    ?>
                    <div
                        class="flex-none w-[300px] snap-start bg-white p-5 rounded-2xl shadow-sm border <?= $cardBorder ?> transition-all hover:shadow-md group cursor-pointer relative overflow-hidden">
                        <!-- Top Bar -->
                        <div class="flex justify-between items-start mb-4">
                            <span
                                class="px-2.5 py-1 rounded-md text-[11px] font-black uppercase tracking-wider <?= $badgeBg ?>">
                                <?= $transInfo['label'] ?>
                            </span>
                            <span class="text-[11px] font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded">
                                <?= timeAgo($search['created_at']) ?>
                            </span>
                        </div>

                        <!-- Property Info -->
                        <div class="flex items-center gap-2 mb-2 text-gray-500">
                            <span
                                class="material-symbols-outlined text-[20px] <?= $propInfo['text'] ?>"><?= $propInfo['icon'] ?></span>
                            <span class="text-sm font-semibold text-gray-700"><?= $propInfo['label'] ?></span>
                        </div>

                        <!-- Location -->
                        <div class="font-bold text-gray-900 mb-1 truncate text-lg">
                            <?= e($search['city']) ?>, <?= e($search['district']) ?>
                        </div>
                        <div class="text-xs text-gray-500 mb-4 truncate font-medium">
                            <?= e($search['neighborhood'] ?? '') ?>
                            <?php if (!empty($search['features'])): ?>
                                • <?= mb_substr(e($search['features']), 0, 20) ?>...
                            <?php endif; ?>
                        </div>

                        <!-- Price -->
                        <div
                            class="font-black text-xl <?= $priceColor ?> mb-4 group-hover:scale-105 transition-transform origin-left">
                            <?= formatPrice($search['max_price']) ?>
                        </div>

                        <!-- Footer -->
                        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-xs border border-gray-200">
                                    <?= mb_substr($search['agent_name'], 0, 1) ?>
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-bold text-gray-900"><?= mb_substr($search['agent_name'], 0, 15) ?></span>
                                    <span
                                        class="text-[10px] text-gray-400 uppercase font-semibold"><?= mb_substr($search['agency_name'], 0, 15) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Full Link Overlay -->
                        <a href="whatsapp://send?phone=<?= formatPhone($search['phone']) ?>&text=Merhaba, EmlakArayış'taki <?= e($search['city']) ?> <?= e($search['district']) ?> arayışınız için yazıyorum."
                            class="absolute inset-0 z-10"></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- How it Works -->
<section class="py-20 bg-white" id="nasil-calisir">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <!-- Image Side -->
            <div class="w-full lg:w-1/2">
                <div
                    class="relative rounded-3xl overflow-hidden shadow-2xl border border-gray-100 bg-gray-50 aspect-[4/3] group">
                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                        alt="Emlak danışmanı"
                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/80 to-transparent"></div>

                    <!-- Floating Card -->
                    <div
                        class="absolute bottom-6 left-6 right-6 bg-white/95 backdrop-blur-sm p-5 rounded-2xl shadow-lg border border-white/50 animate-bounce-slow">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0">
                                <span class="material-symbols-outlined text-2xl">check_circle</span>
                            </div>
                            <div>
                                <p class="text-base font-bold text-gray-900">Eşleşme Bulundu!</p>
                                <p class="text-xs text-gray-500 mt-1">Kadıköy 3+1 arayışınız için 2 meslektaşınızın
                                    portföyü uygun.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Side -->
            <div class="w-full lg:w-1/2">
                <h2 class="text-3xl font-bold text-primary mb-6">Sistem Nasıl İşler?</h2>
                <p class="text-gray-500 text-lg leading-relaxed mb-10">
                    Emlak Arayış, geleneksel ilan sitelerinin aksine arzı değil, talebi merkeze alır.
                    Müşterinizin ne istediğini sisteme girersiniz, elinde o mülk olan danışman size ulaşır.
                </p>

                <div class="space-y-8">
                    <!-- Step 1 -->
                    <div class="flex gap-5 group">
                        <div
                            class="flex-shrink-0 w-12 h-12 rounded-2xl bg-primary/5 group-hover:bg-primary group-hover:text-white text-primary font-bold text-xl flex items-center justify-center transition-colors">
                            1</div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg mb-2">Talep Gir</h4>
                            <p class="text-sm text-gray-500 leading-relaxed">Müşterinizin bütçesini, istediği konumu ve
                                özellikleri detaylıca belirtin.</p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex gap-5 group">
                        <div
                            class="flex-shrink-0 w-12 h-12 rounded-2xl bg-primary/5 group-hover:bg-primary group-hover:text-white text-primary font-bold text-xl flex items-center justify-center transition-colors">
                            2</div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg mb-2">Meslektaşlarına Ulaşsın</h4>
                            <p class="text-sm text-gray-500 leading-relaxed">Arayışınız anında sisteme düşer ve
                                bölgedeki tüm danışmanlar tarafından görülür.</p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex gap-5 group">
                        <div
                            class="flex-shrink-0 w-12 h-12 rounded-2xl bg-primary/5 group-hover:bg-primary group-hover:text-white text-primary font-bold text-xl flex items-center justify-center transition-colors">
                            3</div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg mb-2">Eşleş ve Kazan</h4>
                            <p class="text-sm text-gray-500 leading-relaxed">Portföyü uygun olan meslektaşınız sizinle
                                iletişime geçer, işlemi birlikte bitirirsiniz.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12">
                    <a href="talep-gir.php"
                        class="inline-flex items-center gap-2 text-secondary font-bold hover:text-primary transition-colors text-lg group">
                        Hemen Başlayın
                        <span
                            class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-20 px-4">
    <div
        class="max-w-4xl mx-auto bg-primary rounded-3xl p-8 sm:p-16 text-center text-white relative overflow-hidden shadow-2xl shadow-primary/30">
        <!-- Background Icon -->
        <div class="absolute top-0 right-0 p-12 opacity-5 pointer-events-none">
            <span class="material-symbols-outlined !text-[300px]">handshake</span>
        </div>

        <div class="relative z-10 flex flex-col items-center gap-8">
            <h2 class="text-3xl sm:text-4xl font-bold tracking-tight max-w-2xl">Müşterinize uygun mülkü hemen bulun</h2>
            <p class="text-blue-100 text-lg max-w-2xl mx-auto leading-relaxed">
                Binlerce emlak danışmanı arayışlarını burada paylaşıyor.
                Siz de ağa katılın, pasif portföylerinizi değerlendirin.
            </p>
            <a href="talep-gir.php"
                class="min-w-[200px] h-14 bg-white text-primary hover:bg-gray-50 text-base font-bold rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2 hover:-translate-y-1">
                <span class="material-symbols-outlined">add_circle</span>
                Ücretsiz Talep Gir
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>