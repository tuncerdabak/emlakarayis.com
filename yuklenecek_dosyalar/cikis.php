<?php
/**
 * Emlak Arayış - Çıkış Yap
 */
session_start();
session_destroy();
header('Location: index.php');
exit;
