<?php
define('BASE_URL', 'http://localhost/e-mémoire-lcs');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'ememoire_lcs');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuration email
define('MAIL_HOST', 'smtp.gmail.com');      // Votre serveur SMTP
define('MAIL_PORT', 587);                    // Port
define('MAIL_USER', 'votre.email@gmail.com'); // Identifiant
define('MAIL_PASS', 'votre-mot-de-passe');    // Mot de passe
define('MAIL_FROM', 'noreply@ememoire-lcs.com');
define('MAIL_FROM_NAME', 'e-Mémoire LCS');

session_start();