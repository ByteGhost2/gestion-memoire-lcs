<?php
// controllers/IcalController.php
class IcalController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function exporter() {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];
        $role = $_SESSION['user']['role'];

        // Récupérer les soutenances selon le rôle (comme dans SoutenanceController)
        if ($role === 'admin' || $role === 'responsable') {
            $sql = "SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id";
            $stmt = $pdo->query($sql);
        } elseif ($role === 'etudiant') {
            $sql = "SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id WHERE m.id_etudiant = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_user]);
        } elseif ($role === 'encadreur') {
            $sql = "SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id WHERE m.id_encadreur = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_user]);
        } elseif ($role === 'jury') {
            $sql = "SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN jury j ON s.id = j.id_soutenance JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id WHERE j.id_utilisateur = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_user]);
        } else {
            die("Accès non autorisé");
        }
        $soutenances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Générer le contenu iCal
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//e-Memoire LCS//FR\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:PUBLISH\r\n";

        foreach ($soutenances as $s) {
            $uid = $s['id'] . '@' . $_SERVER['HTTP_HOST'];
            $dtstart = date('Ymd', strtotime($s['date'])) . 'T' . date('His', strtotime($s['heure_debut']));
            $dtend = date('Ymd', strtotime($s['date'])) . 'T' . date('His', strtotime($s['heure_fin']));
            $summary = 'Soutenance: ' . addslashes($s['titre']);
            $description = 'Étudiant: ' . addslashes($s['prenom'] . ' ' . $s['nom']) . ' - Salle: ' . $s['salle'];

            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "UID:$uid\r\n";
            $ics .= "DTSTART:$dtstart\r\n";
            $ics .= "DTEND:$dtend\r\n";
            $ics .= "SUMMARY:$summary\r\n";
            $ics .= "DESCRIPTION:$description\r\n";
            $ics .= "LOCATION:" . addslashes($s['salle']) . "\r\n";
            $ics .= "END:VEVENT\r\n";
        }

        $ics .= "END:VCALENDAR\r\n";

        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="soutenances.ics"');
        echo $ics;
        exit;
    }
}