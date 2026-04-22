<?php
// index.php - Routeur principal

require_once 'config/config.php';
require_once 'config/Db.php';

// Autoload simple des classes
spl_autoload_register(function ($class) {
    if (file_exists('controllers/' . $class . '.php')) {
        require_once 'controllers/' . $class . '.php';
    } elseif (file_exists('models/' . $class . '.php')) {
        require_once 'models/' . $class . '.php';
    }
});

// Récupérer l'URL
$url = isset($_GET['url']) ? $_GET['url'] : '';
$url = rtrim($url, '/');
$url = explode('/', $url);

// Définir le contrôleur et la méthode par défaut
$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$methodName = !empty($url[1]) ? $url[1] : 'index';

// Récupérer les paramètres (toujours un tableau)
$params = array_slice($url, 2);
if (!is_array($params)) {
    $params = []; // Sécurité
}

// Vérifier si le fichier du contrôleur existe
if (file_exists('controllers/' . $controllerName . '.php')) {
    require_once 'controllers/' . $controllerName . '.php';
    $controller = new $controllerName();
    if (method_exists($controller, $methodName)) {
        // Appel de la méthode avec les paramètres
        call_user_func_array([$controller, $methodName], $params);
    } else {
        die("Méthode '$methodName' non trouvée dans le contrôleur '$controllerName'.");
    }
} else {
    // Contrôleur par défaut si non trouvé
    $controller = new AuthController();
    $controller->login();
}