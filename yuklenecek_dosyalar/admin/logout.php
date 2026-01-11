<?php
/**
 * Emlak Arayış - Admin Çıkış
 */
session_start();
session_destroy();
header('Location: index.php');
exit;
