<?php
/**
 * Emlak Arayış - Konfigürasyon Dosyası
 * emlakarayis.com
 */

// Hata raporlama (Production'da kapatıldı)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Karakter seti
mb_internal_encoding('UTF-8');

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400 * 30, // 30 gün
        'path' => '/',
        'domain' => '', // Current domain
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true
    ]);
    session_start();
}

// Veritabanı Ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'tuncerda_emlak_arayis');
define('DB_USER', 'tuncerda_arayis');
define('DB_PASS', 'Td3492549*');
define('DB_CHARSET', 'utf8mb4');

// Site Ayarları
define('SITE_URL', 'https://emlakarayis.com');
define('SITE_NAME', 'Emlak Arayış');
define('SITE_SLOGAN', 'Müşteriniz Hazır ise, Portföyü Meslektaşınızdan Bulun.');
define('SITE_DESCRIPTION', 'Emlak danışmanları için üyeliksiz, hızlı müşteri arayışı paylaşım platformu.');

// WhatsApp Admin Numarası (Başında 90 olmadan)
define('ADMIN_WHATSAPP', '905423408943');

// Renk Paleti
define('COLOR_PRIMARY', '#1A365D');
define('COLOR_SECONDARY', '#3182CE');
define('COLOR_BACKGROUND', '#F7FAFC');
define('COLOR_ACCENT', '#E53E3E');
define('COLOR_SATILIK', '#10B981');
define('COLOR_KIRALIK', '#F59E0B');

// Mülk Tipleri
$PROPERTY_TYPES = [
    'daire' => ['label' => 'Daire', 'icon' => 'apartment', 'bg' => 'bg-blue-50', 'border' => 'border-blue-300', 'text' => 'text-blue-800', 'badge' => 'bg-blue-600'],
    'villa' => ['label' => 'Villa', 'icon' => 'villa', 'bg' => 'bg-purple-50', 'border' => 'border-purple-300', 'text' => 'text-purple-800', 'badge' => 'bg-purple-600'],
    'arsa' => ['label' => 'Arsa', 'icon' => 'landscape', 'bg' => 'bg-amber-50', 'border' => 'border-amber-300', 'text' => 'text-amber-800', 'badge' => 'bg-amber-600'],
    'ticari' => ['label' => 'Ticari', 'icon' => 'store', 'bg' => 'bg-red-50', 'border' => 'border-red-300', 'text' => 'text-red-800', 'badge' => 'bg-red-600'],
    'rezidans' => ['label' => 'Rezidans', 'icon' => 'domain', 'bg' => 'bg-cyan-50', 'border' => 'border-cyan-300', 'text' => 'text-cyan-800', 'badge' => 'bg-cyan-600'],
    'mustakil' => ['label' => 'Müstakil', 'icon' => 'house', 'bg' => 'bg-teal-50', 'border' => 'border-teal-300', 'text' => 'text-teal-800', 'badge' => 'bg-teal-600'],
    'ofis' => ['label' => 'Ofis', 'icon' => 'corporate_fare', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-300', 'text' => 'text-indigo-800', 'badge' => 'bg-indigo-600'],
    'dukkan' => ['label' => 'Dükkan', 'icon' => 'storefront', 'bg' => 'bg-pink-50', 'border' => 'border-pink-300', 'text' => 'text-pink-800', 'badge' => 'bg-pink-600'],
    'depo' => ['label' => 'Depo', 'icon' => 'warehouse', 'bg' => 'bg-gray-100', 'border' => 'border-gray-300', 'text' => 'text-gray-800', 'badge' => 'bg-gray-600']
];

// İşlem Tipleri
$TRANSACTION_TYPES = [
    'satilik' => ['label' => 'Satılık', 'bg' => 'bg-emerald-500', 'border' => 'border-emerald-500', 'text' => 'text-emerald-700', 'light_bg' => 'bg-emerald-50'],
    'kiralik' => ['label' => 'Kiralık', 'bg' => 'bg-orange-500', 'border' => 'border-orange-500', 'text' => 'text-orange-700', 'light_bg' => 'bg-orange-50']
];

// Türkiye İlleri
$CITIES = [
    'Adana',
    'Adıyaman',
    'Afyonkarahisar',
    'Ağrı',
    'Aksaray',
    'Amasya',
    'Ankara',
    'Antalya',
    'Ardahan',
    'Artvin',
    'Aydın',
    'Balıkesir',
    'Bartın',
    'Batman',
    'Bayburt',
    'Bilecik',
    'Bingöl',
    'Bitlis',
    'Bolu',
    'Burdur',
    'Bursa',
    'Çanakkale',
    'Çankırı',
    'Çorum',
    'Denizli',
    'Diyarbakır',
    'Düzce',
    'Edirne',
    'Elazığ',
    'Erzincan',
    'Erzurum',
    'Eskişehir',
    'Gaziantep',
    'Giresun',
    'Gümüşhane',
    'Hakkari',
    'Hatay',
    'Iğdır',
    'Isparta',
    'İstanbul',
    'İzmir',
    'Kahramanmaraş',
    'Karabük',
    'Karaman',
    'Kars',
    'Kastamonu',
    'Kayseri',
    'Kırıkkale',
    'Kırklareli',
    'Kırşehir',
    'Kilis',
    'Kocaeli',
    'Konya',
    'Kütahya',
    'Malatya',
    'Manisa',
    'Mardin',
    'Mersin',
    'Muğla',
    'Muş',
    'Nevşehir',
    'Niğde',
    'Ordu',
    'Osmaniye',
    'Rize',
    'Sakarya',
    'Samsun',
    'Siirt',
    'Sinop',
    'Sivas',
    'Şanlıurfa',
    'Şırnak',
    'Tekirdağ',
    'Tokat',
    'Trabzon',
    'Tunceli',
    'Uşak',
    'Van',
    'Yalova',
    'Yozgat',
    'Zonguldak'
];

// Yayın Süreleri
$DURATION_OPTIONS = [
    3 => '3 Gün',
    7 => '7 Gün (Önerilen)',
    14 => '14 Gün',
    30 => '30 Gün'
];

// Veritabanı Bağlantısı
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
