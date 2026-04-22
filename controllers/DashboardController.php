<?php
// controllers/DashboardController.php
class DashboardController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $user = $_SESSION['user'];
        $role = $user['role'];

        if ($role === 'etudiant') {
            $this->etudiant();
        } elseif ($role === 'admin') {
            header('Location: ' . BASE_URL . '/admin/dashboard');
        } elseif (in_array($role, ['encadreur', 'jury', 'responsable'])) {
            header('Location: ' . BASE_URL . '/enseignant/dashboard');
        } else {
            // fallback
            require_once 'views/dashboard/etudiant.php';
        }
    }

    private function etudiant() {
        $pdo = Db::getInstance();
        $id_etudiant = $_SESSION['user']['id'];

        // Récupérer les mémoires où l'étudiant est membre du groupe ou créateur
        $stmt = $pdo->prepare("
            SELECT DISTINCT m.* 
            FROM memoires m 
            LEFT JOIN memoire_etudiants me ON m.id = me.id_memoire
            WHERE m.id_etudiant = ? OR me.id_etudiant = ?
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$id_etudiant, $id_etudiant]);
        $memoires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque mémoire, récupérer les notes et la soutenance
        $notes = [];
        $soutenances = [];
        $attestations = [];
        foreach ($memoires as $m) {
            // Récupérer la soutenance associée
            $stmtSout = $pdo->prepare("SELECT * FROM soutenances WHERE id_memoire = ?");
            $stmtSout->execute([$m['id']]);
            $soutenance = $stmtSout->fetch(PDO::FETCH_ASSOC);
            if ($soutenance) {
                $soutenances[$m['id']] = $soutenance;
                // Récupérer les évaluations de cette soutenance
                $stmtEval = $pdo->prepare("
                    SELECT e.*, u.nom, u.prenom, u.role 
                    FROM evaluations e 
                    JOIN utilisateurs u ON e.id_utilisateur = u.id 
                    WHERE e.id_soutenance = ?
                ");
                $stmtEval->execute([$soutenance['id']]);
                $notes[$m['id']] = $stmtEval->fetchAll(PDO::FETCH_ASSOC);
            }
            // Vérifier si une attestation existe
            $stmtAtt = $pdo->prepare("SELECT id FROM attestations WHERE id_memoire = ?");
            $stmtAtt->execute([$m['id']]);
            if ($stmtAtt->fetch()) {
                $attestations[$m['id']] = true;
            }
        }

        // Statistiques d'avancement
        $totalMemos = count($memoires);
        $termines = 0;
        $enCours = 0;
        foreach ($memoires as $m) {
            if ($m['statut'] === 'soutenu') $termines++;
            elseif ($m['statut'] === 'valide' || $m['statut'] === 'en_cours') $enCours++;
        }

        require_once 'views/dashboard/etudiant.php';
    }
}