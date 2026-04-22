<?php
// controllers/ProfilController.php
require_once __DIR__ . '/../utils/Mailer.php';

class ProfilController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $id = $_SESSION['user']['id'];
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        require_once 'views/profil/index.php';
    }

    public function update() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $email = trim($_POST['email']);
            $filiere = $_POST['filiere'] ?? null;

            $pdo = Db::getInstance();
            $id = $_SESSION['user']['id'];

            $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->fetch()) {
                $_SESSION['flash_error'] = "Cet email est déjà utilisé.";
            } else {
                $pdo->prepare("UPDATE utilisateurs SET nom=?, prenom=?, email=?, filiere=? WHERE id=?")
                    ->execute([$nom, $prenom, $email, $filiere, $id]);
                $_SESSION['user']['nom'] = $nom;
                $_SESSION['user']['prenom'] = $prenom;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['filiere'] = $filiere;
                $_SESSION['flash_success'] = "Profil mis à jour avec succès.";
            }
        }
        header('Location: ' . BASE_URL . '/profil');
        exit;
    }

    public function updatePhoto() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
            $uploadDir = UPLOAD_DIR . 'photos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $_SESSION['user']['id'] . '_' . time() . '.' . $ext;
            $filepath = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $filepath)) {
                $pdo = Db::getInstance();
                $relativePath = 'uploads/photos/' . $filename;
                $pdo->prepare("UPDATE utilisateurs SET photo = ? WHERE id = ?")->execute([$relativePath, $_SESSION['user']['id']]);
                $_SESSION['user']['photo'] = $relativePath;
                $_SESSION['flash_success'] = "Photo mise à jour avec succès.";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de l'upload de la photo.";
            }
        }
        header('Location: ' . BASE_URL . '/profil');
        exit;
    }

    public function changerMotDePasse() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ancien = $_POST['ancien'];
            $nouveau = $_POST['nouveau'];
            $confirme = $_POST['confirme'];

            $pdo = Db::getInstance();
            $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = ?");
            $stmt->execute([$_SESSION['user']['id']]);
            $hash = $stmt->fetchColumn();

            if (!password_verify($ancien, $hash)) {
                $_SESSION['flash_error'] = "Ancien mot de passe incorrect.";
            } elseif ($nouveau !== $confirme) {
                $_SESSION['flash_error'] = "Les mots de passe ne correspondent pas.";
            } elseif (strlen($nouveau) < 6) {
                $_SESSION['flash_error'] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
            } else {
                $newHash = password_hash($nouveau, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?")->execute([$newHash, $_SESSION['user']['id']]);
                $_SESSION['flash_success'] = "Mot de passe changé avec succès.";
            }
        }
        header('Location: ' . BASE_URL . '/profil');
        exit;
    }
}