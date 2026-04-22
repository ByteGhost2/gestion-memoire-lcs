<?php
// controllers/EnseignantController.php
require_once __DIR__ . '/../utils/Mailer.php';
require_once __DIR__ . '/../utils/MentionHelper.php';

class EnseignantController {
    private function checkEnseignant() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        $role = $_SESSION['user']['role'];
        if (!in_array($role, ['encadreur', 'jury', 'responsable'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    private function notifier($id_user, $message, $lien = null) {
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("INSERT INTO notifications (id_utilisateur, message, lien) VALUES (?, ?, ?)");
        $stmt->execute([$id_user, $message, $lien]);
    }

    private function notifierResponsable($message, $lien) {
        $pdo = Db::getInstance();
        $stmt = $pdo->query("SELECT id FROM utilisateurs WHERE role = 'responsable'");
        while ($row = $stmt->fetch()) {
            $this->notifier($row['id'], $message, $lien);
        }
    }

    private function notifierAdmins($message, $lien) {
        $pdo = Db::getInstance();
        $stmt = $pdo->query("SELECT id FROM utilisateurs WHERE role = 'admin'");
        while ($row = $stmt->fetch()) {
            $this->notifier($row['id'], $message, $lien);
        }
    }

    public function dashboard() {
        $this->checkEnseignant();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        $sql = "
            SELECT s.*, m.titre, u.nom, u.prenom, u.id as etudiant_id,
                (SELECT COUNT(*) FROM evaluations WHERE id_soutenance = s.id AND id_utilisateur = ?) as deja_evalue,
                (SELECT COUNT(*) FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?) as est_jury,
                m.id_encadreur,
                (SELECT COUNT(*) FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ? AND role = 'president') as est_president
            FROM soutenances s
            JOIN memoires m ON s.id_memoire = m.id
            JOIN utilisateurs u ON m.id_etudiant = u.id
            WHERE (EXISTS (SELECT 1 FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?) OR m.id_encadreur = ?)
            ORDER BY s.date DESC, s.heure_debut DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_user, $id_user, $id_user, $id_user, $id_user]);
        $soutenances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtMemos = $pdo->prepare("
            SELECT m.*, u.nom, u.prenom
            FROM memoires m
            JOIN utilisateurs u ON m.id_etudiant = u.id
            WHERE m.id_encadreur = ? AND m.statut IN ('en_cours', 'valide')
            ORDER BY m.updated_at DESC
        ");
        $stmtMemos->execute([$id_user]);
        $memoires_encadres = $stmtMemos->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/enseignant/dashboard.php';
    }

    public function calendrier() {
        $this->checkEnseignant();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        $sql = "
            SELECT s.id, s.date, s.heure_debut, s.heure_fin, s.salle, s.statut,
                   m.titre, u.nom, u.prenom,
                   (SELECT COUNT(*) FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?) as est_jury,
                   m.id_encadreur
            FROM soutenances s
            JOIN memoires m ON s.id_memoire = m.id
            JOIN utilisateurs u ON m.id_etudiant = u.id
            WHERE (EXISTS (SELECT 1 FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?) OR m.id_encadreur = ?)
            ORDER BY s.date, s.heure_debut
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_user, $id_user, $id_user]);
        $soutenances = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $events = [];
        foreach ($soutenances as $s) {
            $start = $s['date'] . 'T' . $s['heure_debut'];
            $end = $s['date'] . 'T' . $s['heure_fin'];
            $color = ($s['statut'] == 'terminee') ? '#28a745' : (($s['statut'] == 'archive') ? '#6c757d' : '#007bff');
            $title = $s['titre'] . ' - ' . $s['prenom'] . ' ' . $s['nom'] . ' (' . $s['salle'] . ')';
            $events[] = [
                'id' => $s['id'],
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'color' => $color,
                'url' => BASE_URL . '/enseignant/evaluer/' . $s['id']
            ];
        }

        require_once 'views/enseignant/calendrier.php';
    }

    public function evaluer($id_soutenance) {
        $this->checkEnseignant();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        $stmt = $pdo->prepare("
            SELECT s.*, m.titre, u.nom, u.prenom 
            FROM soutenances s
            JOIN memoires m ON s.id_memoire = m.id
            JOIN utilisateurs u ON m.id_etudiant = u.id
            WHERE s.id = ? AND (
                EXISTS (SELECT 1 FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?)
                OR m.id_encadreur = ?
            )
        ");
        $stmt->execute([$id_soutenance, $id_user, $id_user]);
        $soutenance = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$soutenance) die("Accès non autorisé à cette soutenance.");

        $stmtEval = $pdo->prepare("SELECT * FROM evaluations WHERE id_soutenance = ? AND id_utilisateur = ?");
        $stmtEval->execute([$id_soutenance, $id_user]);
        $evaluation = $stmtEval->fetch(PDO::FETCH_ASSOC);

        $criteres = $pdo->query("SELECT * FROM criteres_evaluation WHERE actif = 1")->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notes_critere = $_POST['note_critere'] ?? [];
            $appreciation = trim($_POST['appreciation'] ?? '');
            $mention_manuelle = trim($_POST['mention'] ?? '');

            $totalPoints = 0;
            $totalPoids = 0;
            foreach ($notes_critere as $id_critere => $note) {
                if (!empty($note)) {
                    $stmtPoids = $pdo->prepare("SELECT poids FROM criteres_evaluation WHERE id = ?");
                    $stmtPoids->execute([$id_critere]);
                    $poids = $stmtPoids->fetchColumn();
                    if ($poids) {
                        $totalPoints += $note * $poids;
                        $totalPoids += $poids;
                    }
                }
            }

            if ($totalPoids > 0) {
                $noteFinale = $totalPoints / $totalPoids;
            } else {
                $noteFinale = floatval($_POST['note'] ?? 0);
            }

            if ($noteFinale < 0 || $noteFinale > 20) {
                $error = "La note doit être comprise entre 0 et 20.";
            } else {
                if (empty($mention_manuelle)) {
                    $mention = MentionHelper::calculer($noteFinale);
                } else {
                    $mention = $mention_manuelle;
                }

                if ($evaluation) {
                    $stmtUp = $pdo->prepare("UPDATE evaluations SET note = ?, appreciation = ?, mention = ? WHERE id = ?");
                    $stmtUp->execute([$noteFinale, $appreciation, $mention, $evaluation['id']]);
                } else {
                    $stmtIns = $pdo->prepare("INSERT INTO evaluations (id_soutenance, id_utilisateur, note, appreciation, mention) VALUES (?, ?, ?, ?, ?)");
                    $stmtIns->execute([$id_soutenance, $id_user, $noteFinale, $appreciation, $mention]);
                }
                header('Location: ' . BASE_URL . '/enseignant/dashboard?evaluated=1');
                exit;
            }
        }

        require_once 'views/enseignant/evaluer.php';
    }

    public function telechargerMemoire($id_soutenance) {
        $this->checkEnseignant();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        $stmt = $pdo->prepare("
            SELECT m.fichier 
            FROM soutenances s
            JOIN memoires m ON s.id_memoire = m.id
            WHERE s.id = ? AND (
                EXISTS (SELECT 1 FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?)
                OR m.id_encadreur = ?
            )
        ");
        $stmt->execute([$id_soutenance, $id_user, $id_user]);
        $mem = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mem || empty($mem['fichier'])) {
            die("Fichier non disponible.");
        }

        $filepath = __DIR__ . '/../' . $mem['fichier'];
        if (!file_exists($filepath)) {
            die("Fichier introuvable.");
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

    public function terminerSoutenance($id_soutenance) {
        $this->checkEnseignant();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        $stmt = $pdo->prepare("
            SELECT s.id FROM soutenances s
            WHERE s.id = ? AND (
                EXISTS (SELECT 1 FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?)
                OR EXISTS (SELECT 1 FROM memoires m WHERE m.id = s.id_memoire AND m.id_encadreur = ?)
            )
        ");
        $stmt->execute([$id_soutenance, $id_user, $id_user]);
        if (!$stmt->fetch()) die("Action non autorisée.");

        $pdo->prepare("UPDATE soutenances SET statut = 'terminee' WHERE id = ?")->execute([$id_soutenance]);

        // Notifier les administrateurs
        $stmtAdmins = $pdo->query("SELECT id FROM utilisateurs WHERE role = 'admin'");
        while ($admin = $stmtAdmins->fetch()) {
            $this->notifier($admin['id'], "La soutenance #$id_soutenance a été marquée comme terminée.", "/admin/soutenances");
        }

        header('Location: ' . BASE_URL . '/enseignant/dashboard?terminee=1');
        exit;
    }

    public function archiverSoutenance($id_soutenance) {
        $this->checkEnseignant();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        $stmt = $pdo->prepare("
            SELECT s.id FROM soutenances s
            WHERE s.id = ? AND (
                EXISTS (SELECT 1 FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?)
                OR EXISTS (SELECT 1 FROM memoires m WHERE m.id = s.id_memoire AND m.id_encadreur = ?)
            )
        ");
        $stmt->execute([$id_soutenance, $id_user, $id_user]);
        if (!$stmt->fetch()) die("Action non autorisée.");

        $pdo->prepare("UPDATE soutenances SET statut = 'archive' WHERE id = ?")->execute([$id_soutenance]);
        header('Location: ' . BASE_URL . '/enseignant/dashboard?archive=1');
        exit;
    }

    public function supprimerSoutenance($id_soutenance) {
        $this->checkEnseignant();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        $stmt = $pdo->prepare("
            SELECT s.id FROM soutenances s
            WHERE s.id = ? AND (
                EXISTS (SELECT 1 FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?)
                OR EXISTS (SELECT 1 FROM memoires m WHERE m.id = s.id_memoire AND m.id_encadreur = ?)
            )
        ");
        $stmt->execute([$id_soutenance, $id_user, $id_user]);
        if (!$stmt->fetch()) die("Action non autorisée.");

        $pdo->prepare("DELETE FROM soutenances WHERE id = ?")->execute([$id_soutenance]);
        header('Location: ' . BASE_URL . '/enseignant/dashboard?supprime=1');
        exit;
    }

    public function finaliserSoutenance($id_soutenance) {
        $this->checkEnseignant();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        $stmt = $pdo->prepare("SELECT id_soutenance FROM jury WHERE id_soutenance = ? AND id_utilisateur = ? AND role = 'president'");
        $stmt->execute([$id_soutenance, $id_user]);
        if (!$stmt->fetch()) {
            die("Seul le président du jury peut finaliser la soutenance.");
        }

        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total, 
                   (SELECT COUNT(*) FROM evaluations WHERE id_soutenance = ?) as eval_count
            FROM jury 
            WHERE id_soutenance = ?
        ");
        $stmt->execute([$id_soutenance, $id_soutenance]);
        $jury = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($jury['eval_count'] < $jury['total']) {
            die("Tous les membres du jury n'ont pas encore évalué.");
        }

        $stmt = $pdo->prepare("SELECT id_memoire FROM soutenances WHERE id = ?");
        $stmt->execute([$id_soutenance]);
        $soutenance = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$soutenance) die("Soutenance introuvable");

        $pdo->prepare("UPDATE memoires SET statut = 'soutenu' WHERE id = ?")->execute([$soutenance['id_memoire']]);

        $this->notifierResponsable("Le mémoire #{$soutenance['id_memoire']} a été soutenu et est en attente d'attestation.", "/admin/voirMemoire/{$soutenance['id_memoire']}");
        $this->notifierAdmins("Le mémoire #{$soutenance['id_memoire']} a été soutenu. Veuillez téléverser l'attestation.", "/admin/voirMemoire/{$soutenance['id_memoire']}");

        $admins = $pdo->query("SELECT email, prenom, nom FROM utilisateurs WHERE role = 'admin'")->fetchAll();
        foreach ($admins as $admin) {
            $sujet = "Mémoire soutenu - Attestation à téléverser";
            $corps = "<p>Bonjour {$admin['prenom']} {$admin['nom']},</p>
                      <p>Le mémoire #{$soutenance['id_memoire']} a été soutenu. Veuillez téléverser l'attestation.</p>
                      <p><a href='" . BASE_URL . "/admin/voirMemoire/{$soutenance['id_memoire']}'>Voir le mémoire</a></p>";
            Mailer::send($admin['email'], $sujet, $corps);
        }

        header('Location: ' . BASE_URL . '/enseignant/dashboard?finalise=1');
        exit;
    }
}