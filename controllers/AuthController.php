<?php
// controllers/AuthController.php
require_once __DIR__ . '/../utils/Mailer.php';

class AuthController {
    public function login() {
        try {
            $pdo = Db::getInstance();
            $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs");
            if ($stmt->fetchColumn() == 0) {
                header('Location: ' . BASE_URL . '/install');
                exit;
            }
        } catch (PDOException $e) {
            header('Location: ' . BASE_URL . '/install');
            exit;
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die('Requête invalide (CSRF)');
            }

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Veuillez remplir tous les champs.";
            } else {
                $pdo = Db::getInstance();
                $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['mot_de_passe'])) {
                    $_SESSION['user'] = $user;

                    if ($user['role'] === 'admin' || $user['role'] === 'responsable') {
                        header('Location: ' . BASE_URL . '/admin/dashboard');
                    } elseif (in_array($user['role'], ['encadreur', 'jury'])) {
                        header('Location: ' . BASE_URL . '/enseignant/dashboard');
                    } else {
                        header('Location: ' . BASE_URL . '/dashboard');
                    }
                    exit;
                } else {
                    $error = "Email ou mot de passe incorrect.";
                }
            }
        }

        require_once 'views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }

    public function register() {
        try {
            $pdo = Db::getInstance();
            $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs");
            if ($stmt->fetchColumn() == 0) {
                header('Location: ' . BASE_URL . '/install');
                exit;
            }
        } catch (PDOException $e) {
            header('Location: ' . BASE_URL . '/install');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matricule = trim($_POST['matricule'] ?? '');
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            $filiere = $_POST['filiere'] ?? '';

            $errors = [];

            $stmtMat = $pdo->prepare("SELECT id FROM matricules WHERE matricule = ? AND etudiant_id IS NULL");
            $stmtMat->execute([$matricule]);
            if (!$stmtMat->fetch()) {
                $errors[] = "Numéro matricule invalide ou déjà utilisé. Veuillez contacter le service scolarité.";
            }

            if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                $errors[] = "Tous les champs sont obligatoires.";
            }
            if ($password !== $confirm) {
                $errors[] = "Les mots de passe ne correspondent pas.";
            }
            if (strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Adresse email invalide.";
            }

            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Cet email est déjà utilisé.";
            }

            if (empty($errors)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $role = 'etudiant';
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, filiere, matricule) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$nom, $prenom, $email, $hashed, $role, $filiere, $matricule])) {
                    $id_etudiant = $pdo->lastInsertId();
                    $pdo->prepare("UPDATE matricules SET etudiant_id = ? WHERE matricule = ?")->execute([$id_etudiant, $matricule]);

                    $sujet = "Bienvenue sur e-Mémoire LCS";
                    $corps = "<p>Bonjour $prenom $nom,</p>
                              <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.</p>
                              <p><a href='" . BASE_URL . "/auth/login'>Se connecter</a></p>";
                    Mailer::send($email, $sujet, $corps);

                    header('Location: ' . BASE_URL . '/auth/login?registered=1');
                    exit;
                } else {
                    $errors[] = "Erreur lors de l'inscription.";
                }
            }
        }

        require_once 'views/auth/register.php';
    }
}