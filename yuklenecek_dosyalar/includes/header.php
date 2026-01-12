<?php
/**
 * Emlak Arayış - Header
 */

// Config dahil et (eğer zaten dahil edilmemişse)
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config.php';
}
require_once __DIR__ . '/functions.php';

// Geçerli sayfayı belirle (eğer önceden tanımlanmamışsa)
$current_page = $current_page ?? basename($_SERVER['PHP_SELF']);

// Sayfa başlığı ve SEO Ayarları
if ($current_page === 'arayislar.php' && isset($_GET['id'])) {
    // Tekil arayış sayfası için dinamik SEO
    try {
        $stmt = $pdo->prepare("SELECT transaction_type, property_type, city, district FROM searches WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $seoData = $stmt->fetch();
        if ($seoData) {
            $tType = $seoData['transaction_type'] === 'satilik' ? 'Satılık' : 'Kiralık';
            $pType = $seoData['property_type'];
            $pageTitle = "{$tType} {$pType} Arıyorum - {$seoData['city']}, {$seoData['district']}";
            $pageDescription = "{$seoData['city']} {$seoData['district']} bölgesinde {$tType} {$pType} arayışım bulunmaktadır. Portföyü olan meslektaşlarım iletişime geçebilir.";
        }
    } catch (PDOException $e) {
    }
}

$pageTitle = (isset($pageTitle) && !empty($pageTitle)) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME;
$pageDescription = $pageDescription ?? SITE_DESCRIPTION;
?>
<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">

<head>
    <!-- Google Tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-G1ZQY62L3R"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-G1ZQY62L3R');
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="keywords" content="emlak, emlakçı, gayrimenkul, satılık, kiralık, daire, arsa, villa, emlak arayış">
    <meta name="author" content="Emlak Arayış">

    <!-- Open Graph / WhatsApp Preview -->
    <meta property="og:title" content="<?= e($ogTitle ?? $pageTitle) ?>">
    <meta property="og:description" content="<?= e($ogDescription ?? $pageDescription) ?>">
    <meta property="og:url"
        content="<?= e($ogUrl ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    <?php if (isset($ogImage)): ?>
        <meta property="og:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>


    <title><?= e($pageTitle) ?></title>

    <!-- Canonical URL -->
    <link rel="canonical"
        href="<?= e((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . explode('?', $_SERVER['REQUEST_URI'])[0]) ?>">

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "<?= SITE_NAME ?>",
      "url": "<?= SITE_URL ?>",
      "description": "<?= e(SITE_DESCRIPTION) ?>",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "<?= SITE_URL ?>/arayislar.php?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "<?= SITE_NAME ?>",
      "operatingSystem": "All",
      "applicationCategory": "BusinessApplication",
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "TRY"
      }
    }
    </script>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#1A365D',
                        'primary-dark': '#0F2744',
                        'secondary': '#3182CE',
                        'background': '#F7FAFC',
                        'accent': '#E53E3E',
                        'satilik': '#10B981',
                        'kiralik': '#F59E0B',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Material Symbols -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .material-symbols-outlined.filled {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        /* Scrollbar gizle */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-background text-gray-900 antialiased min-h-screen flex flex-col">

    <!-- Modern Dock Header -->
    <header class="fixed top-4 left-0 right-0 z-50 flex justify-center px-4 pointer-events-none">
        <nav
            class="pointer-events-auto bg-[#1A365D]/95 backdrop-blur-md px-6 py-2 rounded-full shadow-2xl flex items-center gap-4 sm:gap-8 border border-white/10 relative">

            <!-- Home -->
            <a href="index.php" class="flex flex-col items-center group min-w-[50px]">
                <span
                    class="material-symbols-outlined text-white/70 group-hover:text-white transition-colors text-2xl">home</span>
                <span class="text-[10px] font-medium text-white/70 group-hover:text-white mt-0.5">Ana Sayfa</span>
            </a>

            <!-- Discover (Search) -->
            <a href="arayislar.php" class="flex flex-col items-center group min-w-[50px]">
                <span
                    class="material-symbols-outlined text-white/70 group-hover:text-white transition-colors text-2xl">search</span>
                <span class="text-[10px] font-medium text-white/70 group-hover:text-white mt-0.5">Keşfet</span>
            </a>

            <!-- ADD BUTTON (Central FAB) -->
            <div class="relative -top-5 mx-2 group">
                <a href="talep-gir.php"
                    class="flex items-center justify-center w-14 h-14 bg-[#F59E0B] rounded-full shadow-lg shadow-orange-500/30 border-4 border-[#F7FAFC] transform transition-transform group-hover:scale-110 group-hover:-translate-y-1">
                    <span class="material-symbols-outlined text-white text-3xl font-bold">add</span>
                </a>
            </div>

            <!-- Notifications / Requests -->
            <?php if (isUserVerified()): ?>
                <a href="taleplerim.php" class="flex flex-col items-center group min-w-[50px]">
                    <div class="relative">
                        <span
                            class="material-symbols-outlined text-white/70 group-hover:text-white transition-colors text-2xl">notifications</span>
                        <!-- Badge (Optional logic) -->
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full border border-[#1A365D]"></span>
                    </div>
                    <span class="text-[10px] font-medium text-white/70 group-hover:text-white mt-0.5">Taleplerim</span>
                </a>
            <?php else: ?>
                <a href="index.php#nasil-calisir" class="flex flex-col items-center group min-w-[50px]">
                    <span
                        class="material-symbols-outlined text-white/70 group-hover:text-white transition-colors text-2xl">help</span>
                    <span class="text-[10px] font-medium text-white/70 group-hover:text-white mt-0.5">Yardım</span>
                </a>
            <?php endif; ?>

            <!-- Profile -->
            <a href="<?= isUserVerified() ? 'profil.php' : 'giris.php' ?>"
                class="flex flex-col items-center group min-w-[50px]">
                <span
                    class="material-symbols-outlined text-white/70 group-hover:text-white transition-colors text-2xl">person</span>
                <span class="text-[10px] font-medium text-white/70 group-hover:text-white mt-0.5">Profil</span>
            </a>

        </nav>
    </header>

    <!-- Spacer to prevent content from being hidden behind the fixed header if we wanted it top, 
         but since it's floating, we might want the content to start a bit lower or just let it flow. 
         Adding a generous padding-top to main, or a spacer div. 
         Since the header is 'fixed top-4', it takes ~80px space visually including margin.
    -->
    <div class="h-24"></div>



    <!-- Main Content -->
    <main class="flex-grow">