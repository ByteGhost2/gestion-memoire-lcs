<?php
// install.php - Script d'installation de e-Mémoire LCS
// Ce fichier doit être placé à la racine du projet.

session_start();

// Configuration par défaut
$configFile = __DIR__ . '/config/config.php';
$configTemplate = <<<'PHP'
<?php
// config/config.php
define('BASE_URL', 'http://localhost/e-memoire-lcs');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'ememoire_lcs');
define('DB_USER', 'root');
define('DB_PASS', '');

session_start();
PHP;

// Fonction pour tester la connexion PDO
function testDBConnection($host, $dbname, $user, $pass) {
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Vérifier si la base existe, sinon la créer
        $stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
        if (!$stmt->fetchColumn()) {
            $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
        $pdo->exec("USE `$dbname`");
        return ['success' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// Fonction pour exécuter le schéma SQL
function createTables($pdo) {
    $sql = "
    CREATE TABLE IF NOT EXISTS utilisateurs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        mot_de_passe VARCHAR(255) NOT NULL,
        role ENUM('etudiant', 'encadreur', 'responsable', 'admin', 'jury') NOT NULL,
        filiere VARCHAR(100) DEFAULT NULL,
        photo VARCHAR(255) DEFAULT NULL,
        date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        remember_token VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS memoires (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        resume TEXT,
        mots_cles VARCHAR(255),
        fichier VARCHAR(255) DEFAULT NULL,
        statut ENUM('brouillon', 'soumis', 'en_cours', 'valide', 'soutenu', 'archive') DEFAULT 'brouillon',
        date_soumission DATE,
        id_etudiant INT NOT NULL,
        id_encadreur INT DEFAULT NULL,
        version_actuelle INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (id_etudiant) REFERENCES utilisateurs(id) ON DELETE CASCADE,
        FOREIGN KEY (id_encadreur) REFERENCES utilisateurs(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS versions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_memoire INT NOT NULL,
        fichier VARCHAR(255) NOT NULL,
        commentaire TEXT,
        date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        numero INT NOT NULL,
        FOREIGN KEY (id_memoire) REFERENCES memoires(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS feedbacks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_version INT NOT NULL,
        id_utilisateur INT NOT NULL,
        message TEXT NOT NULL,
        date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_version) REFERENCES versions(id) ON DELETE CASCADE,
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS soutenances (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_memoire INT NOT NULL,
        date DATE NOT NULL,
        heure_debut TIME NOT NULL,
        heure_fin TIME NOT NULL,
        salle VARCHAR(50) NOT NULL,
        statut ENUM('planifiee', 'terminee') DEFAULT 'planifiee',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_memoire) REFERENCES memoires(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS jury (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_soutenance INT NOT NULL,
        id_utilisateur INT NOT NULL,
        role ENUM('president', 'examinateur') NOT NULL,
        FOREIGN KEY (id_soutenance) REFERENCES soutenances(id) ON DELETE CASCADE,
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,
        UNIQUE KEY unique_membre_soutenance (id_soutenance, id_utilisateur)
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS evaluations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_soutenance INT NOT NULL,
        id_utilisateur INT NOT NULL,
        note DECIMAL(4,2) NOT NULL,
        appreciation TEXT,
        date_evaluation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_soutenance) REFERENCES soutenances(id) ON DELETE CASCADE,
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_utilisateur INT NOT NULL,
        message TEXT NOT NULL,
        lien VARCHAR(255) DEFAULT NULL,
        lu BOOLEAN DEFAULT FALSE,
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;
    ";
    
    try {
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

// Étapes de l'installation
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = null;
$success = null;

// Traitement du formulaire de configuration (étape 1)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'write_config') {
        $base_url = rtrim($_POST['base_url'], '/');
        $db_host = $_POST['db_host'];
        $db_name = $_POST['db_name'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];
        
        // Tester la connexion avant d'écrire
        $test = testDBConnection($db_host, $db_name, $db_user, $db_pass);
        if (!$test['success']) {
            $error = "Erreur de connexion à la base : " . $test['error'];
        } else {
            // Écrire le fichier de configuration
            $configContent = "<?php\n";
            $configContent .= "// config/config.php\n";
            $configContent .= "define('BASE_URL', '$base_url');\n";
            $configContent .= "define('UPLOAD_DIR', __DIR__ . '/../uploads/');\n";
            $configContent .= "define('DB_HOST', '$db_host');\n";
            $configContent .= "define('DB_NAME', '$db_name');\n";
            $configContent .= "define('DB_USER', '$db_user');\n";
            $configContent .= "define('DB_PASS', '$db_pass');\n\n";
            $configContent .= "session_start();\n";
            $configContent .= "?>";
            
            if (!is_dir(dirname($configFile))) {
                mkdir(dirname($configFile), 0755, true);
            }
            if (file_put_contents($configFile, $configContent)) {
                // Stocker la connexion en session pour la suite
                $_SESSION['db_pdo'] = serialize($test['pdo']);
                header('Location: install.php?step=2');
                exit;
            } else {
                $error = "Impossible d'écrire le fichier de configuration. Vérifiez les permissions.";
            }
        }
    } elseif ($_POST['action'] === 'create_admin') {
        // Étape 2 : création de l'admin
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        
        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            $error = "Tous les champs sont obligatoires.";
        } elseif ($password !== $confirm) {
            $error = "Les mots de passe ne correspondent pas.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email invalide.";
        } else {
            // Récupérer la connexion depuis la session
            if (!isset($_SESSION['db_pdo'])) {
                // Sinon, on la recrée
                require_once $configFile;
                require_once __DIR__ . '/config/Db.php';
                $pdo = Db::getInstance();
            } else {
                $pdo = unserialize($_SESSION['db_pdo']);
            }
            
            // Vérifier si un utilisateur existe déjà
            $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs");
            if ($stmt->fetchColumn() > 0) {
                $error = "Des utilisateurs existent déjà. L'installation ne peut pas continuer.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'admin')");
                if ($stmt->execute([$nom, $prenom, $email, $hashed])) {
                    $success = "Compte administrateur créé avec succès. Vous pouvez maintenant vous connecter.";
                    // Nettoyer la session
                    unset($_SESSION['db_pdo']);
                    // Proposer un lien vers la connexion
                    $step = 3;
                } else {
                    $error = "Erreur lors de la création du compte.";
                }
            }
        }
    }
}

// Si le fichier config existe déjà et qu'on est à l'étape 1, on passe à l'étape 2
if ($step == 1 && file_exists($configFile)) {
    header('Location: install.php?step=2');
    exit;
}

// Si on est à l'étape 2, on vérifie que la connexion est possible
if ($step == 2) {
    // Si pas de config, retour étape 1
    if (!file_exists($configFile)) {
        header('Location: install.php?step=1');
        exit;
    }
    require_once $configFile;
    require_once __DIR__ . '/config/Db.php';
    try {
        $pdo = Db::getInstance();
        // Vérifier si les tables existent, sinon les créer
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        if (empty($tables)) {
            $createResult = createTables($pdo);
            if ($createResult !== true) {
                $error = "Erreur lors de la création des tables : $createResult";
            }
        }
        // Vérifier si un admin existe déjà
        $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='admin'");
        if ($stmt->fetchColumn() > 0) {
            // Admin existe déjà, on peut passer à l'étape de fin
            $step = 3;
            $success = "L'installation est déjà terminée. Vous pouvez vous connecter.";
        }
    } catch (Exception $e) {
        $error = "Erreur de connexion à la base : " . $e->getMessage();
        // Proposer de recommencer à l'étape 1
        $step = 1;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation de e-Mémoire LCS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen py-12">
    <div class="container max-w-2xl mx-auto px-4" data-aos="fade-up">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <h1 class="text-3xl font-bold text-center mb-6">Installation de e-Mémoire LCS</h1>
            
            <!-- Indicateur d'étape -->
            <div class="flex justify-center mb-8">
                <div class="flex items-center">
                    <div class="<?= $step >= 1 ? 'bg-blue-500 text-white' : 'bg-gray-300' ?> rounded-full w-8 h-8 flex items-center justify-center font-bold">1</div>
                    <span class="mx-2 <?= $step >= 2 ? 'text-blue-500' : 'text-gray-400' ?>">Configuration</span>
                </div>
                <div class="w-12 h-1 <?= $step >= 2 ? 'bg-blue-500' : 'bg-gray-300' ?> mx-2"></div>
                <div class="flex items-center">
                    <div class="<?= $step >= 2 ? 'bg-blue-500 text-white' : 'bg-gray-300' ?> rounded-full w-8 h-8 flex items-center justify-center font-bold">2</div>
                    <span class="mx-2 <?= $step >= 3 ? 'text-blue-500' : 'text-gray-400' ?>">Administrateur</span>
                </div>
                <div class="w-12 h-1 <?= $step >= 3 ? 'bg-blue-500' : 'bg-gray-300' ?> mx-2"></div>
                <div class="flex items-center">
                    <div class="<?= $step >= 3 ? 'bg-blue-500 text-white' : 'bg-gray-300' ?> rounded-full w-8 h-8 flex items-center justify-center font-bold">3</div>
                    <span class="mx-2 <?= $step >= 3 ? 'text-blue-500' : 'text-gray-400' ?>">Finalisation</span>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if ($step == 1): ?>
                <!-- Étape 1 : Configuration de la base de données -->
                <h2 class="text-xl font-semibold mb-4">Configuration de la base de données</h2>
                <form method="POST" action="install.php?step=1">
                    <input type="hidden" name="action" value="write_config">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="base_url">URL de base du site</label>
                        <input type="url" name="base_url" id="base_url" value="http://localhost/e-memoire-lcs" required class="shadow border rounded w-full py-2 px-3">
                        <p class="text-sm text-gray-500">Exemple : http://localhost/e-memoire-lcs (sans slash final)</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="db_host">Hôte MySQL</label>
                        <input type="text" name="db_host" id="db_host" value="localhost" required class="shadow border rounded w-full py-2 px-3">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="db_name">Nom de la base</label>
                        <input type="text" name="db_name" id="db_name" value="ememoire_lcs" required class="shadow border rounded w-full py-2 px-3">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="db_user">Utilisateur MySQL</label>
                        <input type="text" name="db_user" id="db_user" value="root" required class="shadow border rounded w-full py-2 px-3">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="db_pass">Mot de passe MySQL</label>
                        <input type="password" name="db_pass" id="db_pass" class="shadow border rounded w-full py-2 px-3">
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Tester et enregistrer</button>
                </form>
                
            <?php elseif ($step == 2): ?>
                <!-- Étape 2 : Création du compte administrateur -->
                <h2 class="text-xl font-semibold mb-4">Création du compte administrateur</h2>
                <form method="POST" action="install.php?step=2">
                    <input type="hidden" name="action" value="create_admin">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="nom">Nom</label>
                        <input type="text" name="nom" id="nom" required class="shadow border rounded w-full py-2 px-3">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="prenom">Prénom</label>
                        <input type="text" name="prenom" id="prenom" required class="shadow border rounded w-full py-2 px-3">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                        <input type="email" name="email" id="email" required class="shadow border rounded w-full py-2 px-3">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Mot de passe</label>
                        <input type="password" name="password" id="password" required class="shadow border rounded w-full py-2 px-3">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" id="confirm_password" required class="shadow border rounded w-full py-2 px-3">
                    </div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Créer l'administrateur</button>
                </form>
                
            <?php elseif ($step == 3): ?>
                <!-- Étape 3 : Installation terminée -->
                <div class="text-center">
                    <p class="mb-4">L'installation est terminée !</p>
                    <a href="<?= isset($base_url) ? $base_url : (file_exists($configFile) ? include($configFile) : '') ?>/auth/login" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">Se connecter</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init();</script>
</body>
</html>