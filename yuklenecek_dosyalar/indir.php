<?php
/**
 * Emlak Arayış - Android İndirme Sayfası
 */
require_once 'includes/header.php';
?>

<div
    class="min-h-screen bg-gradient-to-b from-[#1A365D] to-[#0F2744] text-white flex flex-col items-center justify-center -mt-24 pt-32 pb-12 px-4 relative overflow-hidden">

    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-96 h-96 bg-secondary/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -left-1/4 w-80 h-80 bg-accent/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-md w-full text-center space-y-8">

        <!-- App Icon -->
        <div class="flex justify-center mb-8 animate-bounce-slow">
            <div class="w-32 h-32 bg-white/10 backdrop-blur-md rounded-3xl p-6 shadow-2xl border border-white/20">
                <img src="assets/img/android_simge.png" alt="Emlak Arayış"
                    class="w-full h-full object-contain filter drop-shadow-lg">
            </div>
        </div>

        <!-- Title & Desc -->
        <div class="space-y-4">
            <h1 class="text-4xl font-black tracking-tight">
                Emlak Arayış<br>
                <span class="text-secondary">Android'de!</span>
            </h1>
            <p class="text-white/80 text-lg leading-relaxed">
                Meslektaşlarınızla en hızlı portföy paylaşım ağı şimdi cebinizde.
            </p>
        </div>

        <!-- Download Button -->
        <div class="pt-4">
            <a href="android_uygulama/emlakarayis_v1.apk"
                class="group relative inline-flex items-center justify-center gap-3 w-full bg-white text-[#1A365D] px-8 py-5 rounded-2xl font-bold text-xl shadow-[0_0_40px_rgba(255,255,255,0.3)] hover:shadow-[0_0_60px_rgba(255,255,255,0.5)] hover:scale-105 transition-all duration-300">
                <span class="material-symbols-outlined text-3xl group-hover:animate-bounce">android</span>
                <span>Hemen İndir</span>
                <span class="absolute top-0 right-0 -mt-3 -mr-3 flex h-6 w-6">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-6 w-6 bg-secondary"></span>
                </span>
                <div class="absolute inset-0 rounded-2xl ring-4 ring-white/20 group-hover:ring-white/40 transition-all">
                </div>
            </a>
            <p class="mt-4 text-xs text-white/40 font-mono">Sürüm: 1.0.0 | Boyut: ~30MB</p>
        </div>

        <!-- Installation Guide -->
        <div class="mt-12 bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 text-left">
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary">info</span>
                Nasıl Yüklenir?
            </h3>
            <ol class="space-y-4 text-sm text-white/70 list-decimal pl-4 marker:text-secondary marker:font-bold">
                <li>Yukarıdaki <strong>"Hemen İndir"</strong> butonuna basın.</li>
                <li>İndirme tamamlanınca dosyayı açın.</li>
                <li>Eğer sorulursa <strong>"Bilinmeyen Kaynaklara İzin Ver"</strong> seçeneğini aktif edin.</li>
                <li><strong>"Yükle"</strong> diyerek kurulumu tamamlayın.</li>
            </ol>
        </div>

        <!-- QR Code (Optional/Mock) -->
        <!-- 
        <div class="mt-8 pt-8 border-t border-white/10 hidden md:block">
            <p class="text-sm text-white/50 mb-4">Masaüstünde misiniz? Telefonunuzla okutun:</p>
            <div class="bg-white p-2 rounded-xl inline-block">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://emlakarayis.com/indir.php" alt="QR Kod">
            </div>
        </div>
         -->
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>