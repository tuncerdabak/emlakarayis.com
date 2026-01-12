<?php
/**
 * Emlak Arayış - Gizlilik Politikası
 */
require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-12 min-h-screen">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl p-8 md:p-12">
        <h1 class="text-3xl font-bold text-primary mb-8 text-center">Gizlilik Politikası</h1>

        <div class="prose prose-lg max-w-none text-gray-700 space-y-6">
            <p><strong>Son Güncelleme:</strong>
                <?= date('d.m.Y') ?>
            </p>

            <h3 class="text-xl font-semibold text-primary">1. Giriş</h3>
            <p>Emlak Arayış ("Biz", "Şirket"), kullanıcılarımızın ("Siz") gizliliğine önem vermektedir. Bu Gizlilik
                Politikası, mobil uygulamamızı ve web sitemizi kullanırken kişisel verilerinizin nasıl toplandığını,
                kullanıldığını ve korunduğunu açıklar.</p>

            <h3 class="text-xl font-semibold text-primary">2. Toplanan Bilgiler</h3>
            <p>Hizmetlerimizi kullanırken aşağıdaki bilgileri toplayabiliriz:</p>
            <ul class="list-disc pl-5">
                <li><strong>Kişisel Bilgiler:</strong> Ad, soyad, e-posta adresi, telefon numarası (Kayıt ve doğrulama
                    için).</li>
                <li><strong>Cihaz Bilgileri:</strong> IP adresi, cihaz modeli, işletim sistemi sürümü.</li>
                <li><strong>Kullanım Verileri:</strong> Uygulama içindeki etkileşimleriniz, arama geçmişi.</li>
                <li><strong>Konum Bilgileri:</strong> (İsteğe bağlı) Size en yakın ilanları göstermek için konum
                    verileri işlenebilir.</li>
            </ul>

            <h3 class="text-xl font-semibold text-primary">3. Bilgilerin Kullanımı</h3>
            <p>Topladığımız verileri şu amaçlarla kullanırız:</p>
            <ul class="list-disc pl-5">
                <li>Hesabınızı oluşturmak ve yönetmek.</li>
                <li>Size uygun emlak arayışlarını ve eşleşmeleri sunmak.</li>
                <li>Müşteri hizmetleri desteği sağlamak.</li>
                <li>Uygulama güvenliğini sağlamak ve dolandırıcılığı önlemek.</li>
            </ul>

            <h3 class="text-xl font-semibold text-primary">4. Üçüncü Taraf Paylaşımı</h3>
            <p>Kişisel verileriniz, yasal zorunluluklar dışında veya hizmet sağlayıcılarımızla (örn. sunucu hizmetleri)
                işbirliği gerektirmedikçe üçüncü taraflarla paylaşılmaz. Verileriniz satılmaz.</p>

            <h3 class="text-xl font-semibold text-primary">5. Veri Güvenliği</h3>
            <p>Verilerinizi yetkisiz erişime karşı korumak için endüstri standardı güvenlik önlemleri uyguluyoruz.
                Ancak, internet üzerinden yapılan hiçbir iletimin %100 güvenli olmadığını unutmayınız.</p>

            <h3 class="text-xl font-semibold text-primary">6. Hesabın Silinmesi</h3>
            <p>Hesabınızı ve verilerinizi silmek isterseniz, uygulama içindeki "Hesabımı Sil" seçeneğini kullanabilir
                veya bizimle iletişime geçebilirsiniz.</p>

            <h3 class="text-xl font-semibold text-primary">7. İletişim</h3>
            <p>Bu politika hakkında sorularınız varsa, lütfen bizimle iletişime geçin:<br>
                <strong>E-posta:</strong> info@emlakarayis.com
            </p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>