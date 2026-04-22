<?php
// controllers/AttestationController.php
require_once __DIR__ . '/../utils/Mailer.php';

class AttestationController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    private function checkAdminOrResponsable() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Liste des attestations pour l'admin
     */
    public function index() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $stmt = $pdo->query("
            SELECT a.*, u.nom, u.prenom, m.titre 
            FROM attestations a
            JOIN utilisateurs u ON a.id_etudiant = u.id
            JOIN memoires m ON a.id_memoire = m.id
            ORDER BY a.date_emission DESC
        ");
        $attestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/attestation/index.php';
    }

    /**
     * Liste des attestations de l'étudiant connecté
     */
    public function mesAttestations() {
        $this->checkAuth();
        if ($_SESSION['user']['role'] != 'etudiant') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("
            SELECT a.*, m.titre 
            FROM attestations a
            JOIN memoires m ON a.id_memoire = m.id
            WHERE a.id_etudiant = ?
            ORDER BY a.date_emission DESC
        ");
        $stmt->execute([$_SESSION['user']['id']]);
        $attestations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/attestation/etudiant.php';
    }

    /**
     * Formulaire d'upload d'attestation pour un mémoire soutenu (admin)
     */
    public function upload($id_memoire) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();

        // Récupérer tous les étudiants associés au mémoire (auteur + co-auteurs/binômes)
        $stmt = $pdo->prepare("
            SELECT DISTINCT u.id, u.nom, u.prenom, u.email,
                   CASE WHEN m.id_etudiant = u.id THEN 'auteur' ELSE me.role END as role
            FROM memoires m
            LEFT JOIN memoire_etudiants me ON m.id = me.id_memoire
            JOIN utilisateurs u ON (m.id_etudiant = u.id OR me.id_etudiant = u.id)
            WHERE m.id = ? AND m.statut = 'soutenu'
        ");
        $stmt->execute([$id_memoire]);
        $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($etudiants)) {
            die("Mémoire non trouvé ou non soutenu.");
        }
        $memoire_titre = $etudiants[0]['titre'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_etudiant = $_POST['id_etudiant'] ?? 0;
            if (empty($id_etudiant)) {
                $error = "Veuillez sélectionner un étudiant.";
            } elseif (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == UPLOAD_ERR_OK) {
                $uploadDir = UPLOAD_DIR . 'attestations/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $extension = pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION);
                $filename = 'attestation_' . $id_memoire . '_' . $id_etudiant . '_' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['fichier']['tmp_name'], $filepath)) {
                    $relativePath = 'uploads/attestations/' . $filename;
                    $numero = 'ATT-' . date('Y') . '-' . str_pad($id_memoire, 5, '0', STR_PAD_LEFT) . '-' . $id_etudiant;

                    // Vérifier si une attestation existe déjà pour ce couple (mémoire, étudiant)
                    $stmtCheck = $pdo->prepare("SELECT id FROM attestations WHERE id_memoire = ? AND id_etudiant = ?");
                    $stmtCheck->execute([$id_memoire, $id_etudiant]);
                    if ($stmtCheck->fetch()) {
                        $pdo->prepare("UPDATE attestations SET fichier = ?, numero = ?, date_emission = CURDATE() WHERE id_memoire = ? AND id_etudiant = ?")
                            ->execute([$relativePath, $numero, $id_memoire, $id_etudiant]);
                    } else {
                        $pdo->prepare("INSERT INTO attestations (id_etudiant, id_memoire, type, numero, date_emission, fichier) VALUES (?, ?, 'soutenance', ?, CURDATE(), ?)")
                            ->execute([$id_etudiant, $id_memoire, $numero, $relativePath]);
                    }

                    // Récupérer l'email de l'étudiant
                    $stmtUser = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = ?");
                    $stmtUser->execute([$id_etudiant]);
                    $etudiant = $stmtUser->fetch(PDO::FETCH_ASSOC);
                    if ($etudiant) {
                        $sujet = "Votre attestation de soutenance est disponible";
                        $corps = "<p>Bonjour {$etudiant['prenom']} {$etudiant['nom']},</p>
                                  <p>Votre attestation de soutenance a été téléversée par l'administration. Vous pouvez la télécharger dès maintenant sur votre espace.</p>
                                  <p><a href='" . BASE_URL . "/attestation/telecharger/{$id_memoire}?etudiant={$id_etudiant}'>Télécharger l'attestation</a></p>";
                        Mailer::send($etudiant['email'], $sujet, $corps);
                    }

                    $_SESSION['flash_success'] = "Attestation téléversée avec succès pour l'étudiant.";
                    header('Location: ' . BASE_URL . '/admin/voirMemoire/' . $id_memoire);
                    exit;
                } else {
                    $error = "Erreur lors du téléversement du fichier.";
                }
            } else {
                $error = "Veuillez sélectionner un fichier PDF.";
            }
        }

        require_once 'views/admin/upload_attestation.php';
    }

    /**
     * Télécharger une attestation (admin ou étudiant propriétaire)
     */
    public function telecharger($id_memoire) {
        $this->checkAuth();
        $pdo = Db::getInstance();

        $id_etudiant = $_GET['etudiant'] ?? $_SESSION['user']['id'];
        $user = $_SESSION['user'];
        if ($user['role'] != 'admin' && $user['id'] != $id_etudiant) {
            die("Accès non autorisé. Vous n'êtes pas le propriétaire de cette attestation.");
        }

        $stmt = $pdo->prepare("
            SELECT a.* 
            FROM attestations a
            WHERE a.id_memoire = ? AND a.id_etudiant = ?
        ");
        $stmt->execute([$id_memoire, $id_etudiant]);
        $att = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$att || empty($att['fichier'])) {
            die("Attestation non trouvée.");
        }

        $filepath = __DIR__ . '/../' . $att['fichier'];
        if (!file_exists($filepath)) {
            die("Fichier introuvable.");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($att['fichier']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    /**
     * Supprimer une attestation (admin)
     */
    public function supprimer($id_memoire, $id_etudiant = null) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        if ($id_etudiant) {
            $pdo->prepare("DELETE FROM attestations WHERE id_memoire = ? AND id_etudiant = ?")->execute([$id_memoire, $id_etudiant]);
            $message = "Attestation supprimée pour cet étudiant.";
        } else {
            $pdo->prepare("DELETE FROM attestations WHERE id_memoire = ?")->execute([$id_memoire]);
            $message = "Toutes les attestations supprimées.";
        }
        $_SESSION['flash_success'] = $message;
        header('Location: ' . BASE_URL . '/admin/voirMemoire/' . $id_memoire);
        exit;
    }
}