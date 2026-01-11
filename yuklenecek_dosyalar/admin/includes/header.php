<?php
/**
 * Emlak Arayış - Admin Header
 */
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../../config.php';
}
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

// Aktif sayfa kontrolü
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $pageTitle ?? 'Admin Panel' ?> | Emlak Arayış
    </title>
    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#1A365D',
                        'primary-dark': '#0F2744',
                        'secondary': '#3182CE',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-link {
            transition: all 0.2s;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 3px solid #F59E0B;
        }

        @media (max-width: 1024px) {
            .sidebar-open #sidebar {
                transform: translateX(0);
            }

            .sidebar-open #sidebarOverlay {
                display: block;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside id="sidebar"
            class="w-64 bg-primary fixed h-full shadow-xl -translate-x-full lg:translate-x-0 transition-transform duration-300 z-[60]">
            <div class="p-6">
                <!-- Logo -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-xl">admin_panel_settings</span>
                    </div>
                    <div>
                        <h1 class="text-white font-bold text-lg">Admin Panel</h1>
                        <p class="text-white/60 text-xs">emlakarayis.com</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="space-y-1">
                    <a href="panel.php"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:text-white <?= $currentPage === 'panel.php' ? 'active' : '' ?>">
                        <span class="material-symbols-outlined text-xl">verified_user</span>
                        <span class="font-medium">Doğrulama Talepleri</span>
                    </a>

                    <a href="users.php"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:text-white <?= ($currentPage === 'users.php' || $currentPage === 'user-edit.php') ? 'active' : '' ?>">
                        <span class="material-symbols-outlined text-xl">group</span>
                        <span class="font-medium">Kullanıcılar</span>
                    </a>

                    <a href="talepler.php"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:text-white <?= ($currentPage === 'talepler.php' || $currentPage === 'talep-edit.php') ? 'active' : '' ?>">
                        <span class="material-symbols-outlined text-xl">list_alt</span>
                        <span class="font-medium">Tüm Talepler</span>
                    </a>

                    <a href="kullanici-olustur.php"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:text-white <?= $currentPage === 'kullanici-olustur.php' ? 'active' : '' ?>">
                        <span class="material-symbols-outlined text-xl">person_add</span>
                        <span class="font-medium">Kullanıcı Ekle</span>
                    </a>

                    <a href="talep-olustur.php"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white/80 hover:text-white <?= $currentPage === 'talep-olustur.php' ? 'active' : '' ?>">
                        <span class="material-symbols-outlined text-xl">add_circle</span>
                        <span class="font-medium">Talep Oluştur</span>
                    </a>

                    <div class="border-t border-white/10 my-4"></div>

                    <a href="../index.php" target="_blank"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-white/60 hover:text-white">
                        <span class="material-symbols-outlined text-xl">open_in_new</span>
                        <span class="font-medium">Siteyi Görüntüle</span>
                    </a>
                </nav>
            </div>

            <!-- Bottom Section -->
            <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-white/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-white text-lg">person</span>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-white font-medium text-sm truncate">
                                <?= e($_SESSION['admin_username'] ?? 'Admin') ?>
                            </p>
                            <p class="text-white/50 text-xs">Yönetici</p>
                        </div>
                    </div>
                    <a href="logout.php" class="text-red-400 hover:text-red-300 transition-colors" title="Çıkış Yap">
                        <span class="material-symbols-outlined text-xl">logout</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Sidebar Overlay (Mobile Only) -->
        <div id="sidebarOverlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-black/50 z-50 hidden"></div>

        <!-- Mobile Header -->
        <div class="lg:hidden fixed top-0 left-0 right-0 bg-primary z-40 shadow-lg">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-white">admin_panel_settings</span>
                    <span class="text-white font-bold">Admin Panel</span>
                </div>
                <button onclick="toggleMobileMenu()" class="text-white p-2">
                    <span class="material-symbols-outlined text-2xl" id="menuIcon">menu</span>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 lg:ml-64 pt-16 lg:pt-0">
            <div class="p-4 sm:p-6 lg:p-8">