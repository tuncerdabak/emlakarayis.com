<?php
/**
 * Emlak Arayış - Dinamik Sitemap
 */
require_once 'config.php';
require_once 'includes/functions.php';

// Çıktı tamponunu temizle (önceden bir boşluk vs. sızmışsa engellemek için)
if (ob_get_length())
    ob_clean();

header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// 1. Statik Sayfalar
$staticPages = [
    '',
    '/arayislar.php',
    '/giris.php',
    '/talep-gir.php'
];

foreach ($staticPages as $page) {
    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . SITE_URL . $page . '</loc>' . PHP_EOL;
    echo '    <changefreq>daily</changefreq>' . PHP_EOL;
    echo '    <priority>' . ($page === '' ? '1.0' : '0.8') . '</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// 2. Dinamik Sayfalar (Aktif Arayışlar)
try {
    $stmt = $pdo->query("SELECT id, created_at FROM searches WHERE status = 'active' AND expires_at > NOW() ORDER BY created_at DESC");
    while ($row = $stmt->fetch()) {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . SITE_URL . '/arayislar.php?id=' . $row['id'] . '</loc>' . PHP_EOL;
        echo '    <lastmod>' . date('Y-m-d', strtotime($row['created_at'])) . '</lastmod>' . PHP_EOL;
        echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
        echo '    <priority>0.6</priority>' . PHP_EOL;
        echo '  </url>' . PHP_EOL;
    }
} catch (PDOException $e) {
    // Error silenty
}

echo '</urlset>';
