<?php
// controllers/SalleController.php
class SalleController {
    private function checkAdmin() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function index() {
        $this->checkAdmin();
        $pdo = Db::getInstance();
        $salles = $pdo->query("SELECT * FROM salles ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/salle/index.php';
    }

    public function ajouter() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $capacite = intval($_POST['capacite']);
            $equipement = $_POST['equipement'] ?? '';
            $pdo = Db::getInstance();
            $pdo->prepare("INSERT INTO salles (nom, capacite, equipement) VALUES (?, ?, ?)")->execute([$nom, $capacite, $equipement]);
            header('Location: ' . BASE_URL . '/salle?added=1');
            exit;
        }
        require_once 'views/salle/ajouter.php';
    }

    public function modifier($id) {
        $this->checkAdmin();
        $pdo = Db::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $capacite = intval($_POST['capacite']);
            $equipement = $_POST['equipement'] ?? '';
            $active = isset($_POST['active']) ? 1 : 0;
            $pdo->prepare("UPDATE salles SET nom=?, capacite=?, equipement=?, active=? WHERE id=?")->execute([$nom, $capacite, $equipement, $active, $id]);
            header('Location: ' . BASE_URL . '/salle?updated=1');
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM salles WHERE id = ?");
        $stmt->execute([$id]);
        $salle = $stmt->fetch();
        if (!$salle) die("Salle introuvable");
        require_once 'views/salle/modifier.php';
    }

    public function supprimer($id) {
        $this->checkAdmin();
        $pdo = Db::getInstance();
        $pdo->prepare("DELETE FROM salles WHERE id = ?")->execute([$id]);
        header('Location: ' . BASE_URL . '/salle?deleted=1');
        exit;
    }
}