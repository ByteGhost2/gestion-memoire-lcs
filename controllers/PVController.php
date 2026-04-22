<?php
// controllers/PVController.php

/**
 * Contrôleur pour la génération des procès-verbaux de soutenance
 * Nécessite TCPDF pour la génération de PDF, sinon génère un fichier HTML.
 */

// Tentative de chargement de TCPDF via Composer ou chemin manuel
$tcpdfPaths = [
    __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php',
    __DIR__ . '/../vendor/tcpdf/tcpdf.php',
    __DIR__ . '/../libs/tcpdf/tcpdf.php',
    __DIR__ . '/../../tcpdf/tcpdf.php'
];
foreach ($tcpdfPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

// Si TCPDF n'est pas trouvé, définir une classe fictive pour éviter les erreurs
if (!class_exists('TCPDF')) {
    class TCPDF {
        public function __construct($orientation, $unit, $format, $unicode, $encoding) {}
        public function SetCreator($creator) {}
        public function SetAuthor($author) {}
        public function SetTitle($title) {}
        public function AddPage() {}
        public function writeHTML($html, $ln, $fill, $reseth, $cell, $align) {}
        // On ne définit pas Output pour permettre la détection de la vraie classe
    }
}

class PVController {
    private function checkAdmin() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Génère le procès-verbal pour une soutenance
     * @param int $id_soutenance ID de la soutenance
     */
    public function generer($id_soutenance) {
        $this->checkAdmin();
        $pdo = Db::getInstance();

        // Récupérer les informations de la soutenance, mémoire, étudiant
        $stmt = $pdo->prepare("
            SELECT s.*, m.titre, u.nom, u.prenom, u.filiere
            FROM soutenances s
            JOIN memoires m ON s.id_memoire = m.id
            JOIN utilisateurs u ON m.id_etudiant = u.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id_soutenance]);
        $soutenance = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$soutenance) {
            die("Soutenance introuvable.");
        }

        // Récupérer les évaluations
        $stmtEval = $pdo->prepare("
            SELECT e.*, u.nom, u.prenom, u.role
            FROM evaluations e
            JOIN utilisateurs u ON e.id_utilisateur = u.id
            WHERE e.id_soutenance = ?
        ");
        $stmtEval->execute([$id_soutenance]);
        $evaluations = $stmtEval->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les membres du jury
        $stmtJury = $pdo->prepare("
            SELECT u.nom, u.prenom, j.role
            FROM jury j
            JOIN utilisateurs u ON j.id_utilisateur = u.id
            WHERE j.id_soutenance = ?
        ");
        $stmtJury->execute([$id_soutenance]);
        $jury = $stmtJury->fetchAll(PDO::FETCH_ASSOC);

        // Calculer la moyenne des notes
        $notes = array_column($evaluations, 'note');
        $nbNotes = count($notes);
        $moyenne = $nbNotes > 0 ? array_sum($notes) / $nbNotes : 0;

        // Déterminer la mention à partir de la moyenne
        $mention = $this->calculerMention($moyenne);

        // Générer le contenu HTML du PV
        $html = $this->genererHTML($soutenance, $jury, $evaluations, $moyenne, $mention);

        // Vérifier si TCPDF est réellement disponible (présence de la méthode Output)
        if (class_exists('TCPDF') && method_exists('TCPDF', 'Output')) {
            try {
                $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->SetCreator('e-Memoire LCS');
                $pdf->SetAuthor('IUCS');
                $pdf->SetTitle('Procès-verbal de soutenance');
                $pdf->AddPage();
                $pdf->writeHTML($html, true, false, true, false, '');
                $pdf->Output('PV_soutenance_' . $id_soutenance . '.pdf', 'D');
                exit;
            } catch (Exception $e) {
                // En cas d'erreur avec TCPDF, on tombe sur le fallback HTML
                error_log("Erreur TCPDF : " . $e->getMessage());
            }
        }

        // Fallback : télécharger un fichier HTML
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="PV_soutenance_' . $id_soutenance . '.html"');
        echo $html;
        exit;
    }

    /**
     * Génère le HTML du procès-verbal
     */
    private function genererHTML($soutenance, $jury, $evaluations, $moyenne, $mention) {
        $date = date('d/m/Y', strtotime($soutenance['date']));
        $heure = substr($soutenance['heure_debut'], 0, 5);

        $html = "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <title>Procès-verbal de soutenance</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                h1 { text-align: center; color: #1e3a8a; }
                h2 { margin-top: 30px; border-bottom: 1px solid #ccc; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .footer { margin-top: 40px; text-align: right; }
            </style>
        </head>
        <body>
            <h1>PROCÈS-VERBAL DE SOUTENANCE</h1>
            <p><strong>Date :</strong> $date à $heure</p>
            <p><strong>Lieu :</strong> " . htmlspecialchars($soutenance['salle']) . "</p>

            <h2>Étudiant</h2>
            <p>Nom : " . htmlspecialchars($soutenance['prenom'] . ' ' . $soutenance['nom']) . "<br>
            Filière : " . htmlspecialchars($soutenance['filiere']) . "</p>

            <h2>Mémoire</h2>
            <p>Titre : " . htmlspecialchars($soutenance['titre']) . "</p>

            <h2>Jury</h2>
            <ul>
        ";
        foreach ($jury as $j) {
            $html .= "<li>" . htmlspecialchars($j['prenom'] . ' ' . $j['nom']) . " (" . htmlspecialchars($j['role']) . ")</li>";
        }
        $html .= "</ul>";

        $html .= "<h2>Évaluations</h2>";
        if (empty($evaluations)) {
            $html .= "<p>Aucune évaluation enregistrée.</p>";
        } else {
            $html .= "<table>
                <tr>
                    <th>Membre du jury</th>
                    <th>Note /20</th>
                    <th>Mention</th>
                    <th>Appréciation</th>
                </tr>";
            foreach ($evaluations as $e) {
                $html .= "<tr>
                    <td>" . htmlspecialchars($e['prenom'] . ' ' . $e['nom']) . "</td>
                    <td>" . htmlspecialchars($e['note']) . "</td>
                    <td>" . htmlspecialchars($e['mention'] ?? '') . "</td>
                    <td>" . nl2br(htmlspecialchars($e['appreciation'] ?? '')) . "</td>
                </tr>";
            }
            $html .= "</table>";
            $html .= "<h3>Moyenne : " . number_format($moyenne, 2) . "/20 - Mention : $mention</h3>";
        }

        $html .= "
            <div class='footer'>
                <p>Fait à Cotonou, le " . date('d/m/Y') . "</p>
                <p>Le Président du jury,</p>
                <p style='margin-top:60px;'><em>Signature</em></p>
            </div>
        </body>
        </html>
        ";
        return $html;
    }

    /**
     * Calcule la mention à partir d'une note moyenne
     */
    private function calculerMention($note) {
        if ($note >= 16) return "Très bien";
        if ($note >= 14) return "Bien";
        if ($note >= 12) return "Assez bien";
        if ($note >= 10) return "Passable";
        return "Insuffisant";
    }
}