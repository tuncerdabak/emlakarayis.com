<?php
/**
 * Emlak Arayış - Admin Giriş
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if (isAdminLoggedIn()) {
    header('Location: panel.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: panel.php');
        exit;
    } else {
        $error = 'Hatalı kullanıcı adı veya şifre';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş | Admin Panel</title>
    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #1e3a8a, #0f172a);
        }

        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-white/10 rounded-2xl mb-4 backdrop-blur-sm">
                <span class="material-symbols-outlined text-white text-5xl">admin_panel_settings</span>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Emlak Arayış</h1>
            <p class="text-blue-200/60 font-medium">Yönetici Girişi</p>
        </div>

        <div class="glass rounded-3xl shadow-2xl overflow-hidden">
            <div class="p-8">
                <?php if ($error): ?>
                    <div
                        class="mb-6 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl text-sm font-medium flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg">error</span>
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Kullanıcı Adı</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">person</span>
                            <input type="text" name="username" required
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-transparent focus:border-blue-500 focus:bg-white rounded-2xl outline-none transition-all text-gray-900 font-medium"
                                placeholder="Admin kullanıcı adınız">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Şifre</label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">lock</span>
                            <input type="password" name="password" required
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-transparent focus:border-blue-500 focus:bg-white rounded-2xl outline-none transition-all text-gray-900 font-medium"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-blue-600/20 active:scale-[0.98]">
                        Giriş Yap
                    </button>
                </form>
            </div>

            <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 text-center">
                <a href="../index.php"
                    class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Siteye Geri Dön
                </a>
            </div>
        </div>

        <p class="text-center mt-8 text-blue-200/40 text-xs font-medium">
            &copy; <?= date('Y') ?> Emlak Arayış. Tüm hakları saklıdır.
        </p>
    </div>
</body>

</html>