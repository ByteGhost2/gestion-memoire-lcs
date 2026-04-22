<?php
// controllers/BibliothequeController.php
class BibliothequeController {
    public function index() {
        $pdo = Db::getInstance();

        // Récupération des paramètres de filtrage
        $search = trim($_GET['search'] ?? '');
        $filiere = $_GET['filiere'] ?? '';
        $annee = $_GET['annee'] ?? '';
        $encadreur = $_GET['encadreur'] ?? '';
        $mention = $_GET['mention'] ?? '';
        $annee_univ = $_GET['annee_univ'] ?? '';

        // Construction de la requête : seuls les mémoires soutenus ET avec attestation
        $sql = "SELECT m.*, u.nom, u.prenom, u.filiere,
                       (SELECT COUNT(*) FROM evaluations e 
                        JOIN soutenances s ON e.id_soutenance = s.id 
                        WHERE s.id_memoire = m.id) as nb_notes
                FROM memoires m 
                JOIN utilisateurs u ON m.id_etudiant = u.id 
                WHERE m.statut = 'soutenu' 
                  AND EXISTS (SELECT 1 FROM attestations a WHERE a.id_memoire = m.id)";
        $params = [];

        // Filtre recherche plein texte
        if (!empty($search)) {
            $sql .= " AND (m.titre LIKE :search OR m.resume LIKE :search 
                        OR m.mots_cles LIKE :search OR u.nom LIKE :search 
                        OR u.prenom LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Filtre par filière
        if (!empty($filiere)) {
            $sql .= " AND u.filiere = :filiere";
            $params[':filiere'] = $filiere;
        }

        // Filtre par année de soutenance
        if (!empty($annee)) {
            $sql .= " AND YEAR(m.date_soumission) = :annee";
            $params[':annee'] = $annee;
        }

        // Filtre par encadreur
        if (!empty($encadreur)) {
            $sql .= " AND m.id_encadreur = :encadreur";
            $params[':encadreur'] = $encadreur;
        }

        // Filtre par mention
        if (!empty($mention)) {
            $sql .= " AND EXISTS (SELECT 1 FROM evaluations e 
                        JOIN soutenances s ON e.id_soutenance = s.id 
                        WHERE s.id_memoire = m.id AND e.mention = :mention)";
            $params[':mention'] = $mention;
        }

        // Filtre par année universitaire
        if (!empty($annee_univ)) {
            $sql .= " AND m.id_annee_universitaire = :annee_univ";
            $params[':annee_univ'] = $annee_univ;
        }

        // Tri par date de soumission (la plus récente en premier)
        $sql .= " ORDER BY m.date_soumission DESC";

        // Exécution
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $memoires = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupération des données pour les listes déroulantes
        $annees = $pdo->query("SELECT DISTINCT YEAR(date_soumission) as annee 
                               FROM memoires WHERE statut='soutenu' 
                               ORDER BY annee DESC")->fetchAll(PDO::FETCH_COLUMN);

        $filieres = $pdo->query("SELECT DISTINCT filiere FROM utilisateurs 
                                 WHERE role='etudiant' AND filiere IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

        $encadreurs = $pdo->query("SELECT id, nom, prenom FROM utilisateurs 
                                   WHERE role='encadreur' ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

        $mentions = $pdo->query("SELECT DISTINCT mention FROM evaluations 
                                 WHERE mention IS NOT NULL AND mention != ''")->fetchAll(PDO::FETCH_COLUMN);

        $anneesUniv = $pdo->query("SELECT id, libelle FROM annees_universitaires 
                                   ORDER BY libelle DESC")->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/bibliotheque/index.php';
    }

    public function telecharger($id) {
        $pdo = Db::getInstance();

        // Incrémentation du compteur de téléchargements (si la colonne existe)
        try {
            $pdo->prepare("UPDATE memoires SET nb_telechargements = nb_telechargements + 1 WHERE id = ?")->execute([$id]);
        } catch (PDOException $e) {
            // Ignorer l'erreur si la colonne n'existe pas
        }

        // Récupération du fichier
        $stmt = $pdo->prepare("SELECT fichier, titre FROM memoires WHERE id = ? AND statut = 'soutenu'");
        $stmt->execute([$id]);
        $mem = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mem || empty($mem['fichier'])) {
            die("Fichier non disponible");
        }

        $filepath = __DIR__ . '/../' . $mem['fichier'];
        if (!file_exists($filepath)) {
            die("Fichier introuvable sur le serveur");
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

    public function vue($id) {
        $pdo = Db::getInstance();

        // Incrémentation du compteur de vues (si la colonne existe)
        try {
            $pdo->prepare("UPDATE memoires SET nb_vues = nb_vues + 1 WHERE id = ?")->execute([$id]);
        } catch (PDOException $e) {
            // Ignorer l'erreur si la colonne n'existe pas
        }

        // Redirection vers le fichier PDF
        $stmt = $pdo->prepare("SELECT fichier FROM memoires WHERE id = ? AND statut = 'soutenu'");
        $stmt->execute([$id]);
        $mem = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($mem) {
            header('Location: ' . BASE_URL . '/' . $mem['fichier']);
        } else {
            die("Fichier non trouvé");
        }
        exit;
    }
}