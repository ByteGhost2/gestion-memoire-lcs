<?php
// controllers/JuryController.php
class JuryController {
    private function checkJury() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'jury') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Tableau de bord du jury : liste des soutenances affectées
     */
    public function dashboard() {
        $this->checkJury();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        // Récupérer les soutenances où l'utilisateur est membre du jury
        $stmt = $pdo->prepare("
            SELECT s.*, m.titre, m.fichier, u.nom, u.prenom 
            FROM soutenances s 
            JOIN jury j ON s.id = j.id_soutenance
            JOIN memoires m ON s.id_memoire = m.id
            JOIN utilisateurs u ON m.id_etudiant = u.id
            WHERE j.id_utilisateur = ? 
            ORDER BY s.date DESC
        ");
        $stmt->execute([$id_user]);
        $soutenances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque soutenance, vérifier si déjà évaluée
        $evaluations = [];
        foreach ($soutenances as $s) {
            $stmtEval = $pdo->prepare("SELECT id FROM evaluations WHERE id_soutenance = ? AND id_utilisateur = ?");
            $stmtEval->execute([$s['id'], $id_user]);
            $evaluations[$s['id']] = $stmtEval->fetch() ? true : false;
        }

        require_once 'views/jury/dashboard.php';
    }

    /**
     * Formulaire d'évaluation pour une soutenance
     */
    public function evaluer($id_soutenance) {
        $this->checkJury();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        // Vérifier que l'utilisateur est bien dans le jury de cette soutenance
        $stmt = $pdo->prepare("SELECT id FROM jury WHERE id_soutenance = ? AND id_utilisateur = ?");
        $stmt->execute([$id_soutenance, $id_user]);
        if (!$stmt->fetch()) {
            die("Accès non autorisé à cette soutenance.");
        }

        // Récupérer les informations de la soutenance
        $stmtSout = $pdo->prepare("
            SELECT s.*, m.titre, m.fichier, u.nom, u.prenom 
            FROM soutenances s 
            JOIN memoires m ON s.id_memoire = m.id
            JOIN utilisateurs u ON m.id_etudiant = u.id
            WHERE s.id = ?
        ");
        $stmtSout->execute([$id_soutenance]);
        $soutenance = $stmtSout->fetch(PDO::FETCH_ASSOC);
        if (!$soutenance) {
            die("Soutenance introuvable.");
        }

        // Récupérer l'évaluation existante si déjà faite
        $stmtEval = $pdo->prepare("SELECT * FROM evaluations WHERE id_soutenance = ? AND id_utilisateur = ?");
        $stmtEval->execute([$id_soutenance, $id_user]);
        $evaluation = $stmtEval->fetch(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $note = floatval($_POST['note'] ?? 0);
            $appreciation = trim($_POST['appreciation'] ?? '');

            if ($note < 0 || $note > 20) {
                $error = "La note doit être comprise entre 0 et 20.";
            } else {
                if ($evaluation) {
                    // Mise à jour
                    $stmtUp = $pdo->prepare("UPDATE evaluations SET note = ?, appreciation = ? WHERE id = ?");
                    $stmtUp->execute([$note, $appreciation, $evaluation['id']]);
                } else {
                    // Insertion
                    $stmtIns = $pdo->prepare("INSERT INTO evaluations (id_soutenance, id_utilisateur, note, appreciation) VALUES (?, ?, ?, ?)");
                    $stmtIns->execute([$id_soutenance, $id_user, $note, $appreciation]);
                }
                header('Location: ' . BASE_URL . '/jury/dashboard?evaluated=1');
                exit;
            }
        }

        require_once 'views/jury/evaluer.php';
    }

    /**
     * Télécharger le mémoire (PDF) associé à une soutenance
     */
    public function telechargerMemoire($id_soutenance) {
        $this->checkJury();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        // Vérifier que l'utilisateur est dans le jury
        $stmt = $pdo->prepare("SELECT id FROM jury WHERE id_soutenance = ? AND id_utilisateur = ?");
        $stmt->execute([$id_soutenance, $id_user]);
        if (!$stmt->fetch()) {
            die("Accès non autorisé.");
        }

        // Récupérer le chemin du fichier
        $stmt = $pdo->prepare("
            SELECT m.fichier 
            FROM soutenances s
            JOIN memoires m ON s.id_memoire = m.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id_soutenance]);
        $mem = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mem || empty($mem['fichier'])) {
            die("Fichier non disponible.");
        }

        $filepath = __DIR__ . '/../' . $mem['fichier'];
        if (!file_exists($filepath)) {
            die("Fichier introuvable sur le serveur.");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($mem['fichier']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
}