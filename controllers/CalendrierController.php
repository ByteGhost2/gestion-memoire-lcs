<?php
// controllers/CalendrierController.php
class CalendrierController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        require_once 'views/calendrier/index.php';
    }

    public function events() {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $user = $_SESSION['user'];
        $role = $user['role'];

        $sql = "SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id";
        if ($role == 'etudiant') {
            $sql .= " WHERE m.id_etudiant = " . $user['id'];
        } elseif ($role == 'encadreur') {
            $sql .= " WHERE m.id_encadreur = " . $user['id'];
        } elseif ($role == 'jury') {
            $sql .= " WHERE s.id IN (SELECT id_soutenance FROM jury WHERE id_utilisateur = " . $user['id'] . ")";
        }
        // admin voit tout

        $soutenances = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $events = [];
        foreach ($soutenances as $s) {
            $events[] = [
                'title' => $s['titre'] . ' - ' . $s['prenom'] . ' ' . $s['nom'],
                'start' => $s['date'] . 'T' . $s['heure_debut'],
                'end' => $s['date'] . 'T' . $s['heure_fin'],
                'url' => BASE_URL . '/soutenance/planning',
                'color' => $s['statut'] == 'terminee' ? 'green' : 'blue'
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($events);
    }
}