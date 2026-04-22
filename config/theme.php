<?php
// config/theme.php
// Définition des couleurs et du mode

// Initialiser le mode depuis la session ou cookie
if (!isset($_SESSION['theme'])) {
    // Vérifier le cookie
    if (isset($_COOKIE['theme'])) {
        $_SESSION['theme'] = $_COOKIE['theme'];
    } else {
        $_SESSION['theme'] = 'light'; // par défaut
    }
}

// Si on change le mode via GET
if (isset($_GET['toggle_theme'])) {
    $_SESSION['theme'] = ($_SESSION['theme'] === 'light') ? 'dark' : 'light';
    setcookie('theme', $_SESSION['theme'], time() + 30*24*3600, '/'); // 30 jours
    header('Location: ' . str_replace('?toggle_theme=1', '', $_SERVER['REQUEST_URI']));
    exit;
}

$theme = $_SESSION['theme'];
?>