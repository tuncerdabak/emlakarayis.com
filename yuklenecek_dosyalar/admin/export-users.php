<?php
/**
 * Admin: Kullanıcıları Excel Olarak İndir
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$filename = "kullanici_listesi_" . date('Y-m-d') . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

$users = getAllUsers($pdo);

// Excel BOM for UTF-8 (Türkçe karakter sorunu için)
echo "\xEF\xBB\xBF";

// Başlıklar
echo "ID\tKayıt Tarihi\tAd Soyad\tFirma\tTelefon\tInstagram\tDurum\n";

// Veriler
foreach ($users as $user) {
    echo $user['id'] . "\t";
    echo date('d.m.Y H:i', strtotime($user['created_at'])) . "\t";
    echo str_replace("\t", " ", $user['agent_name']) . "\t"; // Tab karakterlerini temizle
    echo str_replace("\t", " ", $user['agency_name']) . "\t";
    echo $user['phone'] . "\t";
    echo $user['instagram'] . "\t";
    echo ($user['is_active'] ? 'Aktif' : 'Pasif') . "\n";
}
exit;
?>