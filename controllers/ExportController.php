<?php
// controllers/ExportController.php
require_once __DIR__ . '/../lib/fpdf/fpdf.php';

class ExportController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function pvSoutenance($id_soutenance) {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        // Vérifier droits (admin, responsable, ou jury de cette soutenance)
        $stmt = $pdo->prepare("
            SELECT s.*, m.titre, u.nom, u.prenom, u.email,
                GROUP_CONCAT(DISTINCT CONCAT(e.note, '|', e.mention, '|', e.appreciation, '|', j.role, '|', ev.nom, ' ', ev.prenom) SEPARATOR '||') as evaluations
            FROM soutenances s
            JOIN memoires m ON s.id_memoire = m.id
            JOIN utilisateurs u ON m.id_etudiant = u.id
            LEFT JOIN jury j ON s.id = j.id_soutenance
            LEFT JOIN evaluations e ON s.id = e.id_soutenance AND e.id_utilisateur = j.id_utilisateur
            LEFT JOIN utilisateurs ev ON e.id_utilisateur = ev.id
            WHERE s.id = ?
            GROUP BY s.id
        ");
        $stmt->execute([$id_soutenance]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) die("Soutenance introuvable");

        // Génération PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'Procès-verbal de soutenance',0,1,'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,10,'Mémoire : ' . utf8_decode($data['titre']),0,1);
        $pdf->Cell(0,10,'Étudiant : ' . utf8_decode($data['prenom'] . ' ' . $data['nom']),0,1);
        $pdf->Cell(0,10,'Date : ' . date('d/m/Y', strtotime($data['date'])) . ' à ' . substr($data['heure_debut'],0,5),0,1);
        $pdf->Cell(0,10,'Salle : ' . $data['salle'],0,1);
        $pdf->Ln(10);

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,10,'Évaluations :',0,1);
        $pdf->SetFont('Arial','',12);
        if ($data['evaluations']) {
            $evals = explode('||', $data['evaluations']);
            foreach ($evals as $e) {
                list($note, $mention, $app, $role, $nom) = explode('|', $e);
                $pdf->Cell(0,10,"$nom ($role) : $note/20 - $mention",0,1);
                if (!empty($app)) {
                    $pdf->MultiCell(0,10,"Appréciation : " . utf8_decode($app));
                }
            }
        } else {
            $pdf->Cell(0,10,'Aucune évaluation enregistrée.',0,1);
        }

        $pdf->Output('I', 'pv_soutenance_'.$id_soutenance.'.pdf');
        exit;
    }
}