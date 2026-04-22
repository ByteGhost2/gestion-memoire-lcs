<?php
require_once __DIR__ . '/../../config/theme.php';

$notifications_non_lues = [];
$nb_notifications = 0;
if (isset($_SESSION['user'])) {
    try {
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id_utilisateur = ? AND lu = FALSE ORDER BY date_creation DESC LIMIT 10");
        $stmt->execute([$_SESSION['user']['id']]);
        $notifications_non_lues = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $nb_notifications = count($notifications_non_lues);
    } catch (Exception $e) {}
}
?>
<!DOCTYPE html>
<html lang="fr" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-Mémoire LCS – Institut Universitaire Les COURS SONOU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300">

<!-- Header moderne avec icône de profil -->
<nav class="bg-white dark:bg-gray-800 shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Logo et titre -->
            <div class="flex items-center space-x-3">
                <img src="<?= BASE_URL ?>/public/images/logo-iucs.jpg" alt="Logo IUCS" class="h-10 w-auto">
                <a href="<?= BASE_URL ?>" class="text-xl font-bold text-gray-800 dark:text-white">
                    <i class="fas fa-graduation-cap mr-2 text-blue-600"></i> e-Mémoire LCS
                </a>
            </div>

            <!-- Actions droite -->
            <div class="flex items-center space-x-4">
                <!-- Bouton thème -->
                <a href="?toggle_theme=1" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                    <?php if ($theme === 'light'): ?>
                        <i class="fas fa-moon text-xl"></i>
                    <?php else: ?>
                        <i class="fas fa-sun text-xl"></i>
                    <?php endif; ?>
                </a>

                <?php if (isset($_SESSION['user'])): ?>
                    <!-- Cloche de notifications -->
                    <a href="<?= BASE_URL ?>/notification" class="relative p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if ($nb_notifications > 0): ?>
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full"><?= $nb_notifications ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- Profil utilisateur avec dropdown -->
                    <div class="relative">
                        <button id="profileButton" class="flex items-center space-x-2 focus:outline-none p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <?php if (!empty($user['photo'])): ?>
                        <img src="<?= BASE_URL . '/' . $user['photo'] ?>" alt="Photo de profil" class="w-10 h-10 mx-auto rounded-full object-cover border-2 border-white shadow-lg">
                    <?php else: ?>
                        
                    <?php endif; ?>
                            <span class="hidden md:inline text-gray-700 dark:text-gray-200"><?= htmlspecialchars($_SESSION['user']['prenom']) ?></span>
                            <i class="fas fa-chevron-down text-xs text-gray-500 hidden md:inline"></i>
                        </button>
                        <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-xl z-50 border border-gray-200 dark:border-gray-700">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white"><?= htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                            </div>
                            <a href="<?= BASE_URL ?>/profil" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Mon profil</a>
                            <a href="<?= BASE_URL ?>/auth/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Déconnexion</a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Icône de profil pour les visiteurs (menu connexion/inscription) -->
                    <div class="relative">
                        <button id="guestProfileButton" class="flex items-center justify-center w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition focus:outline-none">
                            <i class="fas fa-user text-lg"></i>
                        </button>
                        <div id="guestProfileDropdown" class="hidden absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-lg shadow-xl z-50 border border-gray-200 dark:border-gray-700">
                            <a href="<?= BASE_URL ?>/auth/login" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Connexion</a>
                            <a href="<?= BASE_URL ?>/auth/register" class="block px-4 py-2 text-sm text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">Inscription</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
    // Gestion du dropdown pour l'utilisateur connecté
    const profileBtn = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', () => {
            profileDropdown.classList.add('hidden');
        });
        profileDropdown.addEventListener('click', (e) => e.stopPropagation());
    }

    // Gestion du dropdown pour les visiteurs
    const guestBtn = document.getElementById('guestProfileButton');
    const guestDropdown = document.getElementById('guestProfileDropdown');
    if (guestBtn && guestDropdown) {
        guestBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            guestDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', () => {
            guestDropdown.classList.add('hidden');
        });
        guestDropdown.addEventListener('click', (e) => e.stopPropagation());
    }
</script>

<div class="flex">
    <?php if (isset($_SESSION['user'])): ?>
        <?php include 'sidebar.php'; ?>
        <main class="flex-1 p-4 md:p-8 min-h-screen">
    <?php else: ?>
        <main class="w-full">
    <?php endif; ?>