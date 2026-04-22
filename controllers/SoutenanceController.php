<?php
// controllers/SoutenanceController.php
class SoutenanceController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Affiche le planning des soutenances selon le rôle
     */
    public function planning() {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $user = $_SESSION['user'];
        $role = $user['role'];
        $id = $user['id'];

        // Construire la requête en fonction du rôle
        if ($role === 'admin') {
            // Admin voit tout
            $sql = "
                SELECT s.*, m.titre, u.nom, u.prenom, u.id as etudiant_id,
                       (SELECT GROUP_CONCAT(CONCAT(us.prenom, ' ', us.nom) SEPARATOR ', ') 
                        FROM jury j JOIN utilisateurs us ON j.id_utilisateur = us.id 
                        WHERE j.id_soutenance = s.id) as membres_jury
                FROM soutenances s
                JOIN memoires m ON s.id_memoire = m.id
                JOIN utilisateurs u ON m.id_etudiant = u.id
                ORDER BY s.date, s.heure_debut
            ";
            $stmt = $pdo->query($sql);
        } elseif ($role === 'etudiant') {
            // Étudiant : ses propres soutenances
            $sql = "
                SELECT s.*, m.titre, u.nom, u.prenom,
                       (SELECT GROUP_CONCAT(CONCAT(us.prenom, ' ', us.nom) SEPARATOR ', ') 
                        FROM jury j JOIN utilisateurs us ON j.id_utilisateur = us.id 
                        WHERE j.id_soutenance = s.id) as membres_jury
                FROM soutenances s
                JOIN memoires m ON s.id_memoire = m.id
                JOIN utilisateurs u ON m.id_etudiant = u.id
                WHERE m.id_etudiant = ?
                ORDER BY s.date, s.heure_debut
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
        } elseif ($role === 'encadreur') {
            // Encadreur : soutenances des mémoires qu'il encadre
            $sql = "
                SELECT s.*, m.titre, u.nom, u.prenom,
                       (SELECT GROUP_CONCAT(CONCAT(us.prenom, ' ', us.nom) SEPARATOR ', ') 
                        FROM jury j JOIN utilisateurs us ON j.id_utilisateur = us.id 
                        WHERE j.id_soutenance = s.id) as membres_jury
                FROM soutenances s
                JOIN memoires m ON s.id_memoire = m.id
                JOIN utilisateurs u ON m.id_etudiant = u.id
                WHERE m.id_encadreur = ?
                ORDER BY s.date, s.heure_debut
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
        } elseif ($role === 'jury') {
            // Jury : soutenances où il est membre
            $sql = "
                SELECT s.*, m.titre, u.nom, u.prenom,
                       (SELECT GROUP_CONCAT(CONCAT(us.prenom, ' ', us.nom) SEPARATOR ', ') 
                        FROM jury j2 JOIN utilisateurs us ON j2.id_utilisateur = us.id 
                        WHERE j2.id_soutenance = s.id) as membres_jury
                FROM soutenances s
                JOIN jury j ON s.id = j.id_soutenance
                JOIN memoires m ON s.id_memoire = m.id
                JOIN utilisateurs u ON m.id_etudiant = u.id
                WHERE j.id_utilisateur = ?
                ORDER BY s.date, s.heure_debut
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
        } elseif ($role === 'responsable') {
            // Responsable : voit tout comme admin
            $sql = "
                SELECT s.*, m.titre, u.nom, u.prenom,
                       (SELECT GROUP_CONCAT(CONCAT(us.prenom, ' ', us.nom) SEPARATOR ', ') 
                        FROM jury j JOIN utilisateurs us ON j.id_utilisateur = us.id 
                        WHERE j.id_soutenance = s.id) as membres_jury
                FROM soutenances s
                JOIN memoires m ON s.id_memoire = m.id
                JOIN utilisateurs u ON m.id_etudiant = u.id
                ORDER BY s.date, s.heure_debut
            ";
            $stmt = $pdo->query($sql);
        } else {
            // Autres rôles : rien
            $soutenances = [];
        }

        $soutenances = isset($stmt) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        require_once 'views/soutenance/planning.php';
    }

    /**
     * Version JSON pour calendrier interactif (optionnel)
     */
    public function json() {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $user = $_SESSION['user'];
        $role = $user['role'];
        $id = $user['id'];

        // Même logique que planning mais retourne JSON
        if ($role === 'admin' || $role === 'responsable') {
            $sql = "SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id";
            $stmt = $pdo->query($sql);
        } elseif ($role === 'etudiant') {
            $sql = "SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id WHERE m.id_etudiant = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
        } elseif ($role === 'encadreur') {
            $sql = "SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id WHERE m.id_encadreur = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
        } elseif ($role === 'jury') {
            $sql = "SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN jury j ON s.id = j.id_soutenance JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id WHERE j.id_utilisateur = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
        } else {
            $soutenances = [];
        }
        $soutenances = isset($stmt) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        header('Content-Type: application/json');
        echo json_encode($soutenances);
        exit;
    }
}