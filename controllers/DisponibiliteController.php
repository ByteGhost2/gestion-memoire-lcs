<?php
// controllers/DisponibiliteController.php
class DisponibiliteController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        $stmt = $pdo->prepare("SELECT * FROM disponibilites WHERE id_utilisateur = ? AND date >= CURDATE() ORDER BY date, heure_debut");
        $stmt->execute([$id_user]);
        $dispos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/disponibilite/index.php';
    }

    public function ajouter() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = $_POST['date'];
            $heure_debut = $_POST['heure_debut'];
            $heure_fin = $_POST['heure_fin'];
            $motif = $_POST['motif'] ?? '';

            // Validations
            if (empty($date) || empty($heure_debut) || empty($heure_fin)) {
                $_SESSION['flash_error'] = "Tous les champs sont obligatoires.";
                header('Location: ' . BASE_URL . '/disponibilite/ajouter');
                exit;
            }

            if ($heure_debut >= $heure_fin) {
                $_SESSION['flash_error'] = "L'heure de début doit être antérieure à l'heure de fin.";
                header('Location: ' . BASE_URL . '/disponibilite/ajouter');
                exit;
            }

            $pdo = Db::getInstance();
            $stmt = $pdo->prepare("INSERT INTO disponibilites (id_utilisateur, date, heure_debut, heure_fin, motif) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user']['id'], $date, $heure_debut, $heure_fin, $motif]);

            header('Location: ' . BASE_URL . '/disponibilite?added=1');
            exit;
        }
        require_once 'views/disponibilite/ajouter.php';
    }

    public function supprimer($id) {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("DELETE FROM disponibilites WHERE id = ? AND id_utilisateur = ?");
        $stmt->execute([$id, $_SESSION['user']['id']]);
        header('Location: ' . BASE_URL . '/disponibilite?deleted=1');
        exit;
    }
}