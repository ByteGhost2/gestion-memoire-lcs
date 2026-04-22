<?php
// controllers/InstallController.php
class InstallController {
    
    public function index() {
        // Vérifier si des utilisateurs existent déjà
        $pdo = Db::getInstance();
        $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs");
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            // Des utilisateurs existent, rediriger vers la page de connexion
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password'];
            
            $errors = [];
            if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                $errors[] = "Tous les champs sont obligatoires";
            }
            if ($password !== $confirm) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide";
            }
            
            if (empty($errors)) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'admin')");
                if ($stmt->execute([$nom, $prenom, $email, $hashed])) {
                    // Rediriger vers la page de connexion
                    header('Location: ' . BASE_URL . '/auth/login?installed=1');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la création du compte";
                }
            }
        }
        
        require_once 'views/install/index.php';
    }
}