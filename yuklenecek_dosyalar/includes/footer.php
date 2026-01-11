</main>

<!-- Footer -->
<footer class="bg-primary text-white/70 border-t border-white/5 py-12 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Logo & Description -->
            <div class="col-span-1 md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <img src="assets/img/logo.svg" alt="Emlak Arayış Logo" class="h-10 w-auto brightness-0 invert">
                </div>
                <p class="text-sm leading-relaxed">
                    Emlak danışmanları için geliştirilmiş, hızlı ve güvenli arayış paylaşım platformu.
                </p>
            </div>

            <!-- Platform -->
            <div class="col-span-1">
                <h4 class="font-bold text-white mb-4">Platform</h4>
                <ul class="space-y-2 text-sm">
                    <li><a class="hover:text-white transition-colors" href="#nasil-calisir">Nasıl Çalışır?</a></li>
                    <li><a class="hover:text-white transition-colors" href="arayislar.php">Arayışlar</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">SSS</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div class="col-span-1">
                <h4 class="font-bold text-white mb-4">Yasal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a class="hover:text-white transition-colors" href="#">Kullanım Şartları</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Gizlilik Politikası</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">KVKK</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-span-1">
                <h4 class="font-bold text-white mb-4">İletişim</h4>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a class="hover:text-white transition-colors flex items-center gap-2"
                            href="mailto:info@emlakarayis.com">
                            <span class="material-symbols-outlined text-base">mail</span>
                            info@emlakarayis.com
                        </a>
                    </li>
                    <li>
                        <a class="hover:text-white transition-colors flex items-center gap-2"
                            href="tel:+<?= ADMIN_WHATSAPP ?>">
                            <span class="material-symbols-outlined text-base">call</span>
                            +<?= substr(ADMIN_WHATSAPP, 0, 2) ?> (<?= substr(ADMIN_WHATSAPP, 2, 3) ?>)
                            <?= substr(ADMIN_WHATSAPP, 5, 3) ?> <?= substr(ADMIN_WHATSAPP, 8, 2) ?>
                            <?= substr(ADMIN_WHATSAPP, 10, 2) ?>
                        </a>
                    </li>
                    <li class="pt-2">
                        <a href="<?= whatsappLink(ADMIN_WHATSAPP, 'Merhaba, bilgi almak istiyorum.') ?>" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-[#25D366] hover:bg-[#128C7E] text-white rounded-lg font-bold text-sm transition-all shadow-lg shadow-green-900/20">
                            <i class="fab fa-whatsapp text-lg"></i>
                            WhatsApp'tan Yazın
                        </a>
                    </li>
                    <li class="flex gap-3 mt-4">
                        <button
                            class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center hover:bg-white/10 hover:text-white transition-all text-white/50"
                            title="LinkedIn">
                            <span class="text-xs font-bold">in</span>
                        </button>
                        <button
                            class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center hover:bg-white/10 hover:text-white transition-all text-white/50"
                            title="Twitter">
                            <span class="text-xs font-bold">X</span>
                        </button>
                        <button
                            class="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center hover:bg-white/10 hover:text-white transition-all text-white/50"
                            title="Instagram">
                            <span class="text-xs font-bold">ig</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-white/5 text-center">
            <p class="text-sm text-white/40">© <?= date('Y') ?> Emlak Arayış. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>

<!-- Sticky Bottom Bar (Mobile) -->
<div
    class="fixed bottom-0 left-0 w-full bg-white/95 backdrop-blur-lg border-t border-gray-200 p-4 z-50 shadow-[0_-4px_12px_rgba(0,0,0,0.08)] md:hidden">
    <div class="max-w-lg mx-auto flex items-center justify-center gap-4">
        <a href="#nasil-calisir"
            class="flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-secondary transition-colors">
            <span class="material-symbols-outlined text-lg">help</span>
        </a>
        <a href="talep-gir.php"
            class="flex-1 h-12 bg-secondary hover:bg-primary text-white rounded-xl font-bold text-base shadow-lg shadow-secondary/30 transition-all flex items-center justify-center gap-2 hover:-translate-y-0.5 active:translate-y-0">
            <span class="material-symbols-outlined">add_circle</span>
            Hemen Talep Gir
        </a>
    </div>
</div>

<!-- Spacer for sticky bottom bar on mobile -->
<div class="h-20 md:hidden"></div>

<!-- Custom JavaScript -->
<script src="assets/js/app.js"></script>
</body>

</html>