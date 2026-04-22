<?php
// controllers/MemoireController.php
require_once __DIR__ . '/../utils/Mailer.php';

class MemoireController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
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

    private function envoyerEmailResponsables($titre, $description, $id_memoire) {
        $pdo = Db::getInstance();
        $stmt = $pdo->query("SELECT email, prenom, nom FROM utilisateurs WHERE role='responsable'");
        while ($resp = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sujet = "Nouveau thème soumis";
            $corps = "<p>Bonjour {$resp['prenom']} {$resp['nom']},</p>
                      <p>Un nouveau thème a été soumis par {$_SESSION['user']['prenom']} {$_SESSION['user']['nom']}.</p>
                      <p><strong>Titre :</strong> " . htmlspecialchars($titre) . "</p>
                      <p><strong>Description :</strong> " . htmlspecialchars($description) . "</p>
                      <p><a href='" . BASE_URL . "/memoire/voir/{$id_memoire}'>Voir le thème</a></p>";
            Mailer::send($resp['email'], $sujet, $corps);
        }
    }

    public function index() {
        $this->checkAuth();
        $user = $_SESSION['user'];
        $pdo = Db::getInstance();

        if ($user['role'] == 'etudiant') {
            $stmt = $pdo->prepare("
                SELECT DISTINCT m.* 
                FROM memoires m 
                LEFT JOIN memoire_etudiants me ON m.id = me.id_memoire
                WHERE m.id_etudiant = ? OR me.id_etudiant = ?
                ORDER BY m.created_at DESC
            ");
            $stmt->execute([$user['id'], $user['id']]);
        } elseif ($user['role'] == 'encadreur') {
            $stmt = $pdo->prepare("
                SELECT m.*, u.nom, u.prenom 
                FROM memoires m 
                JOIN utilisateurs u ON m.id_etudiant = u.id 
                WHERE m.id_encadreur = ? 
                ORDER BY m.updated_at DESC
            ");
            $stmt->execute([$user['id']]);
        } else {
            $stmt = $pdo->query("
                SELECT m.*, u.nom, u.prenom 
                FROM memoires m 
                JOIN utilisateurs u ON m.id_etudiant = u.id 
                ORDER BY m.created_at DESC
            ");
        }
        $memoires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/memoire/index.php';
    }

    public function soumettre() {
        $this->checkAuth();
        if ($_SESSION['user']['role'] != 'etudiant') {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $id_etudiant = $_SESSION['user']['id'];

            $errors = [];
            if (empty($titre) || empty($description)) {
                $errors[] = "Le titre et la description du thème sont obligatoires";
            }

            if (empty($errors)) {
                $pdo = Db::getInstance();
                $annee_active = $pdo->query("SELECT id FROM annees_universitaires WHERE active = 1")->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO memoires (titre, theme_description, id_etudiant, id_annee_universitaire, statut, date_soumission) VALUES (?, ?, ?, ?, 'soumis', CURDATE())");
                if ($stmt->execute([$titre, $description, $id_etudiant, $annee_active])) {
                    $id_memoire = $pdo->lastInsertId();
                    $stmt2 = $pdo->prepare("INSERT INTO memoire_etudiants (id_memoire, id_etudiant, role) VALUES (?, ?, 'chef')");
                    $stmt2->execute([$id_memoire, $id_etudiant]);

                    $this->notifierResponsable(
                        "Nouveau thème soumis par " . $_SESSION['user']['prenom'] . " " . $_SESSION['user']['nom'],
                        "/memoire/voir/$id_memoire"
                    );
                    $this->envoyerEmailResponsables($titre, $description, $id_memoire);

                    header('Location: ' . BASE_URL . '/memoire');
                    exit;
                }
            }
        }
        require_once 'views/memoire/soumettre.php';
    }

    public function validerSujet($id) {
        $this->checkAuth();
        if ($_SESSION['user']['role'] != 'encadreur') die("Action non autorisée");
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT id_encadreur, statut, id_etudiant FROM memoires WHERE id = ?");
        $stmt->execute([$id]);
        $m = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$m || $m['id_encadreur'] != $_SESSION['user']['id'] || $m['statut'] != 'soumis') die("Action non autorisée");
        $update = $pdo->prepare("UPDATE memoires SET statut = 'en_cours', theme_feedback = NULL WHERE id = ?");
        $update->execute([$id]);
        $this->notifier($m['id_etudiant'], "Votre sujet a été validé, vous pouvez maintenant déposer votre mémoire", "/memoire/voir/$id");
        header('Location: ' . BASE_URL . '/memoire/voir/' . $id);
        exit;
    }

    public function rejeterSujet($id) {
        $this->checkAuth();
        if ($_SESSION['user']['role'] != 'encadreur') die("Action non autorisée");
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT id_encadreur, statut, id_etudiant FROM memoires WHERE id = ?");
        $stmt->execute([$id]);
        $m = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$m || $m['id_encadreur'] != $_SESSION['user']['id'] || $m['statut'] != 'soumis') die("Action non autorisée");

        $feedback = trim($_POST['feedback'] ?? '');
        if (empty($feedback)) {
            $_SESSION['flash_error'] = "Veuillez fournir un commentaire pour justifier le rejet.";
            header('Location: ' . BASE_URL . '/memoire/voir/' . $id);
            exit;
        }

        $update = $pdo->prepare("UPDATE memoires SET statut = 'rejete', theme_feedback = ? WHERE id = ?");
        $update->execute([$feedback, $id]);
        $this->notifier($m['id_etudiant'], "Votre sujet a été rejeté. Consultez le commentaire de votre encadreur.", "/memoire/voir/$id");
        header('Location: ' . BASE_URL . '/memoire/voir/' . $id);
        exit;
    }

    public function modifierTheme($id) {
        $this->checkAuth();
        if ($_SESSION['user']['role'] != 'etudiant') die("Action non autorisée");

        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM memoires WHERE id = ? AND (id_etudiant = ? OR id IN (SELECT id_memoire FROM memoire_etudiants WHERE id_etudiant = ?))");
        $stmt->execute([$id, $_SESSION['user']['id'], $_SESSION['user']['id']]);
        $memoire = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$memoire) die("Mémoire introuvable");
        if ($memoire['statut'] != 'rejete') die("Vous ne pouvez modifier que les thèmes rejetés.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');

            $errors = [];
            if (empty($titre) || empty($description)) {
                $errors[] = "Le titre et la description sont obligatoires";
            }

            if (empty($errors)) {
                $update = $pdo->prepare("UPDATE memoires SET titre = ?, theme_description = ?, statut = 'soumis', theme_feedback = NULL WHERE id = ?");
                $update->execute([$titre, $description, $id]);
                header('Location: ' . BASE_URL . '/memoire/voir/' . $id);
                exit;
            }
        }

        require_once 'views/memoire/modifier_theme.php';
    }

    public function completer($id) {
    $this->checkAuth();
    if ($_SESSION['user']['role'] != 'etudiant') die("Action non autorisée");

    $pdo = Db::getInstance();
    $stmt = $pdo->prepare("SELECT * FROM memoires WHERE id = ? AND (id_etudiant = ? OR id IN (SELECT id_memoire FROM memoire_etudiants WHERE id_etudiant = ?))");
    $stmt->execute([$id, $_SESSION['user']['id'], $_SESSION['user']['id']]);
    $memoire = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$memoire) die("Mémoire introuvable");
    if ($memoire['statut'] != 'en_cours') die("Vous ne pouvez compléter le mémoire que s'il est en cours.");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resume = trim($_POST['resume'] ?? '');
        $mots_cles = trim($_POST['mots_cles'] ?? '');

        $errors = [];
        if (empty($resume)) $errors[] = "Le résumé est obligatoire.";
        if (!isset($_FILES['couverture']) || $_FILES['couverture']['error'] != UPLOAD_ERR_OK) {
            $errors[] = "L'image de couverture est obligatoire.";
        }

        // Vérification du fichier PDF
        if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == UPLOAD_ERR_OK) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['fichier']['tmp_name']);
            finfo_close($finfo);
            $extension = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
            
            if ($mimeType !== 'application/pdf' && $extension !== 'pdf') {
                $errors[] = "Seuls les fichiers PDF sont acceptés pour le mémoire.";
            }
        } else {
            $errors[] = "Veuillez sélectionner un fichier PDF.";
        }

        if (empty($errors)) {
            $pdo->beginTransaction();
            try {
                $stmtUp = $pdo->prepare("UPDATE memoires SET resume = ?, mots_cles = ? WHERE id = ?");
                $stmtUp->execute([$resume, $mots_cles, $id]);

                // Upload du fichier PDF (déjà vérifié)
                $uploadDir = UPLOAD_DIR . 'memoires/' . date('Y') . '/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $filename = 'memoire_' . $_SESSION['user']['id'] . '_v1_' . time() . '.pdf';
                $filepath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['fichier']['tmp_name'], $filepath)) {
                    $relativePath = 'uploads/memoires/' . date('Y') . '/' . $filename;
                    $stmtVer = $pdo->prepare("INSERT INTO versions (id_memoire, fichier, numero) VALUES (?, ?, 1)");
                    $stmtVer->execute([$id, $relativePath]);
                    $stmtUpd = $pdo->prepare("UPDATE memoires SET fichier = ? WHERE id = ?");
                    $stmtUpd->execute([$relativePath, $id]);
                } else {
                    throw new Exception("Erreur lors de l'upload du fichier.");
                }

                // Upload de l'image de couverture (obligatoire)
                if (isset($_FILES['couverture']) && $_FILES['couverture']['error'] == UPLOAD_ERR_OK) {
                    $uploadImgDir = UPLOAD_DIR . 'couvertures/';
                    if (!is_dir($uploadImgDir)) mkdir($uploadImgDir, 0777, true);
                    $imgExt = pathinfo($_FILES['couverture']['name'], PATHINFO_EXTENSION);
                    $imgFilename = 'couverture_' . $id . '_' . time() . '.' . $imgExt;
                    $imgFilepath = $uploadImgDir . $imgFilename;
                    if (move_uploaded_file($_FILES['couverture']['tmp_name'], $imgFilepath)) {
                        $relativeImgPath = 'uploads/couvertures/' . $imgFilename;
                        $stmtImg = $pdo->prepare("UPDATE memoires SET couverture = ? WHERE id = ?");
                        $stmtImg->execute([$relativeImgPath, $id]);
                    } else {
                        throw new Exception("Erreur lors de l'upload de l'image de couverture.");
                    }
                } else {
                    throw new Exception("L'image de couverture est obligatoire.");
                }

                $pdo->commit();

                // Notifier l'encadreur
                $stmtEnc = $pdo->prepare("SELECT id_encadreur FROM memoires WHERE id = ?");
                $stmtEnc->execute([$id]);
                $enc = $stmtEnc->fetch(PDO::FETCH_ASSOC);
                if ($enc && $enc['id_encadreur']) {
                    $this->notifier($enc['id_encadreur'], "Le mémoire complet a été déposé pour #$id", "/memoire/voir/$id");
                    $stmtUser = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = ?");
                    $stmtUser->execute([$enc['id_encadreur']]);
                    $userEnc = $stmtUser->fetch(PDO::FETCH_ASSOC);
                    if ($userEnc) {
                        $sujet = "Mémoire complet déposé";
                        $corps = "<p>Bonjour {$userEnc['prenom']} {$userEnc['nom']},</p>
                                  <p>{$_SESSION['user']['prenom']} {$_SESSION['user']['nom']} a déposé la version finale du mémoire.</p>
                                  <p><a href='" . BASE_URL . "/memoire/voir/{$id}'>Voir le mémoire</a></p>";
                        Mailer::send($userEnc['email'], $sujet, $corps);
                    }
                }

                header('Location: ' . BASE_URL . '/memoire/voir/' . $id);
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = $e->getMessage();
            }
        }
    }

    require_once 'views/memoire/completer.php';
}

public function ajouterVersion($id) {
    $this->checkAuth();
    if ($_SESSION['user']['role'] != 'etudiant') die("Action non autorisée");

    $pdo = Db::getInstance();
    
    // Vérifier que l'étudiant a accès au mémoire et récupérer les infos
    $stmt = $pdo->prepare("
        SELECT m.id_etudiant, m.version_actuelle, m.verrou_par, m.verrou_le, m.statut,
               (SELECT COUNT(*) FROM versions WHERE id_memoire = m.id) as nb_versions
        FROM memoires m 
        LEFT JOIN memoire_etudiants me ON m.id = me.id_memoire
        WHERE m.id = ? AND (m.id_etudiant = ? OR me.id_etudiant = ?)
    ");
    $stmt->execute([$id, $_SESSION['user']['id'], $_SESSION['user']['id']]);
    $memoire = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$memoire) die("Mémoire non trouvé ou accès refusé");
    
    // Vérifier le statut
    if ($memoire['statut'] != 'en_cours') {
        die("Vous ne pouvez plus déposer de version. Le mémoire n'est pas en cours.");
    }
    
    // Vérifier qu'au moins une version existe déjà (le mémoire a été complété)
    if ($memoire['nb_versions'] == 0) {
        die("Vous devez d'abord soumettre la version finale du mémoire via le formulaire dédié.");
    }

    // Gestion du verrouillage
    if ($memoire['verrou_par'] && $memoire['verrou_par'] != $_SESSION['user']['id']) {
        if (time() < strtotime($memoire['verrou_le']) + 300) {
            die("Ce mémoire est en cours de modification par un autre utilisateur. Veuillez réessayer plus tard.");
        } else {
            $pdo->prepare("UPDATE memoires SET verrou_par = NULL, verrou_le = NULL WHERE id = ?")->execute([$id]);
        }
    }
    $pdo->prepare("UPDATE memoires SET verrou_par = ?, verrou_le = NOW() WHERE id = ?")->execute([$_SESSION['user']['id'], $id]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier'])) {
        // Vérification du type de fichier
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $_FILES['fichier']['tmp_name']);
        finfo_close($finfo);
        $extension = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
        
        if ($mimeType !== 'application/pdf' && $extension !== 'pdf') {
            die("Seuls les fichiers PDF sont acceptés pour le mémoire.");
        }

        $newVersion = $memoire['version_actuelle'] + 1;
        $uploadDir = UPLOAD_DIR . 'memoires/' . date('Y') . '/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = 'memoire_' . $_SESSION['user']['id'] . '_v' . $newVersion . '_' . time() . '.pdf';
        $filepath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['fichier']['tmp_name'], $filepath)) {
            $relativePath = 'uploads/memoires/' . date('Y') . '/' . $filename;
            $pdo->prepare("INSERT INTO versions (id_memoire, fichier, numero) VALUES (?, ?, ?)")->execute([$id, $relativePath, $newVersion]);
            $pdo->prepare("UPDATE memoires SET version_actuelle = ?, fichier = ? WHERE id = ?")->execute([$newVersion, $relativePath, $id]);

            $stmtEnc = $pdo->prepare("SELECT id_encadreur FROM memoires WHERE id = ?");
            $stmtEnc->execute([$id]);
            $enc = $stmtEnc->fetch(PDO::FETCH_ASSOC);
            if ($enc && $enc['id_encadreur']) {
                $this->notifier($enc['id_encadreur'], "Nouvelle version déposée pour le mémoire #$id", "/memoire/voir/$id");
                $stmtUser = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = ?");
                $stmtUser->execute([$enc['id_encadreur']]);
                $userEnc = $stmtUser->fetch(PDO::FETCH_ASSOC);
                if ($userEnc) {
                    $sujet = "Nouvelle version déposée";
                    $corps = "<p>Bonjour {$userEnc['prenom']} {$userEnc['nom']},</p>
                              <p>{$_SESSION['user']['prenom']} {$_SESSION['user']['nom']} a déposé une nouvelle version (v{$newVersion}) pour le mémoire #{$id}.</p>
                              <p><a href='" . BASE_URL . "/memoire/voir/{$id}'>Voir le mémoire</a></p>";
                    Mailer::send($userEnc['email'], $sujet, $corps);
                }
            }
        }
        $pdo->prepare("UPDATE memoires SET verrou_par = NULL, verrou_le = NULL WHERE id = ?")->execute([$id]);
        header('Location: ' . BASE_URL . '/memoire/voir/' . $id);
        exit;
    }
}

    public function voir($id) {
    $this->checkAuth();
    $pdo = Db::getInstance();

    $stmt = $pdo->prepare("SELECT m.*, u.nom, u.prenom, u.email FROM memoires m JOIN utilisateurs u ON m.id_etudiant = u.id WHERE m.id = ?");
    $stmt->execute([$id]);
    $memoire = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$memoire) die("Mémoire introuvable");

    $user = $_SESSION['user'];
    $acces = false;
    $is_admin_or_responsable = false;

    if ($user['role'] == 'admin' || $user['role'] == 'responsable') {
        $acces = true;
        $is_admin_or_responsable = true;
    } elseif ($user['role'] == 'encadreur' && $memoire['id_encadreur'] == $user['id']) {
        $acces = true;
    } elseif ($user['role'] == 'etudiant') {
        // Vérifier si l'étudiant fait partie du groupe
        $stmtGroupe = $pdo->prepare("SELECT id FROM memoire_etudiants WHERE id_memoire = ? AND id_etudiant = ?");
        $stmtGroupe->execute([$id, $user['id']]);
        if ($stmtGroupe->fetch() || $memoire['id_etudiant'] == $user['id']) {
            $acces = true;
        }
    }
    if (!$acces) die("Accès non autorisé");

    // Groupe d'étudiants (pour l'affichage)
    $stmtGroupe = $pdo->prepare("SELECT u.*, me.role FROM memoire_etudiants me JOIN utilisateurs u ON me.id_etudiant = u.id WHERE me.id_memoire = ?");
    $stmtGroupe->execute([$id]);
    $groupe = $stmtGroupe->fetchAll(PDO::FETCH_ASSOC);

    // Versions : uniquement pour l'encadreur et l'étudiant
    $versions = [];
    if (!$is_admin_or_responsable) {
        $stmtVers = $pdo->prepare("SELECT * FROM versions WHERE id_memoire = ? ORDER BY numero DESC");
        $stmtVers->execute([$id]);
        $versions = $stmtVers->fetchAll(PDO::FETCH_ASSOC);
    }

    // Feedbacks : pour l'encadreur et l'étudiant (auteur ou membre du groupe)
    $feedbacks = [];
    if (($user['role'] == 'encadreur' && $user['id'] == $memoire['id_encadreur']) ||
        ($user['role'] == 'etudiant' && (in_array($user['id'], array_column($groupe, 'id')) || $user['id'] == $memoire['id_etudiant']))) {
        $stmtFeed = $pdo->prepare("
            SELECT f.*, u.nom, u.prenom, u.role
            FROM feedbacks f 
            JOIN utilisateurs u ON f.id_utilisateur = u.id 
            WHERE f.id_version IN (SELECT id FROM versions WHERE id_memoire = ?) 
            ORDER BY f.date ASC
        ");
        $stmtFeed->execute([$id]);
        $feedbacks = $stmtFeed->fetchAll(PDO::FETCH_ASSOC);
    }

    // Plagiat : seulement pour l'encadreur
    $plagiat = null;
    if ($user['role'] == 'encadreur' && $user['id'] == $memoire['id_encadreur']) {
        $stmtPlagiat = $pdo->prepare("SELECT * FROM plagiat_checks WHERE id_memoire = ? ORDER BY date_check DESC LIMIT 1");
        $stmtPlagiat->execute([$id]);
        $plagiat = $stmtPlagiat->fetch(PDO::FETCH_ASSOC);
    }

    // Verrou
    $verrou = null;
    if ($memoire['verrou_par']) {
        $stmtVerrou = $pdo->prepare("SELECT prenom, nom FROM utilisateurs WHERE id = ?");
        $stmtVerrou->execute([$memoire['verrou_par']]);
        $verrou = $stmtVerrou->fetch(PDO::FETCH_ASSOC);
    }

    // Critères (pour le formulaire de feedback, mais seulement si l'utilisateur peut commenter)
    $criteres = $pdo->query("SELECT * FROM criteres_evaluation WHERE actif = 1")->fetchAll(PDO::FETCH_ASSOC);

    // Étudiants disponibles pour ajout au groupe (si chef de groupe)
    $etudiantsDisponibles = [];
    if ($user['role'] == 'etudiant' && $memoire['id_etudiant'] == $user['id']) {
        $tous = $pdo->query("SELECT id, nom, prenom FROM utilisateurs WHERE role='etudiant' ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
        $idsGroupe = array_column($groupe, 'id');
        $etudiantsDisponibles = array_filter($tous, fn($e) => !in_array($e['id'], $idsGroupe));
    }

    require_once 'views/memoire/voir.php';
}

    public function commenter($id_version) {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT m.id_etudiant, m.id_encadreur, m.id as id_memoire FROM versions v JOIN memoires m ON v.id_memoire = m.id WHERE v.id = ?");
        $stmt->execute([$id_version]);
        $mem = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mem) die("Version introuvable");

        $user = $_SESSION['user'];
        $acces = false;
        if ($user['role'] == 'admin' || $user['role'] == 'responsable') {
            $acces = true;
        } elseif ($user['role'] == 'encadreur' && $mem['id_encadreur'] == $user['id']) {
            $acces = true;
        } elseif ($user['role'] == 'etudiant') {
            $stmtGroupe = $pdo->prepare("SELECT id FROM memoire_etudiants WHERE id_memoire = ? AND id_etudiant = ?");
            $stmtGroupe->execute([$mem['id_memoire'], $user['id']]);
            if ($stmtGroupe->fetch() || $mem['id_etudiant'] == $user['id']) {
                $acces = true;
            }
        }
        if (!$acces) die("Action non autorisée");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = trim($_POST['message'] ?? '');
            if (empty($message)) {
                $_SESSION['flash_error'] = "Le message ne peut pas être vide.";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }

            $piece_jointe = null;
            if (isset($_FILES['piece_jointe']) && $_FILES['piece_jointe']['error'] == UPLOAD_ERR_OK) {
                $uploadDir = UPLOAD_DIR . 'feedbacks/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $ext = pathinfo($_FILES['piece_jointe']['name'], PATHINFO_EXTENSION);
                $filename = 'feedback_' . $id_version . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['piece_jointe']['tmp_name'], $uploadDir . $filename)) {
                    $piece_jointe = 'uploads/feedbacks/' . $filename;
                }
            }

            $stmtIns = $pdo->prepare("INSERT INTO feedbacks (id_version, id_utilisateur, message, piece_jointe) VALUES (?, ?, ?, ?)");
            $stmtIns->execute([$id_version, $user['id'], $message, $piece_jointe]);

            $destinataire = ($user['id'] == $mem['id_etudiant']) ? $mem['id_encadreur'] : $mem['id_etudiant'];
            if ($destinataire) {
                $this->notifier($destinataire, "Nouveau commentaire sur le mémoire #{$mem['id_memoire']}", "/memoire/voir/{$mem['id_memoire']}");
                $stmtDest = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = ?");
                $stmtDest->execute([$destinataire]);
                $dest = $stmtDest->fetch(PDO::FETCH_ASSOC);
                if ($dest) {
                    $sujet = "Nouveau commentaire sur votre mémoire";
                    $corps = "<p>Bonjour {$dest['prenom']} {$dest['nom']},</p>
                              <p>{$_SESSION['user']['prenom']} {$_SESSION['user']['nom']} a posté un commentaire sur le mémoire #{$mem['id_memoire']}.</p>
                              <p><a href='" . BASE_URL . "/memoire/voir/{$mem['id_memoire']}'>Voir le mémoire</a></p>";
                    Mailer::send($dest['email'], $sujet, $corps);
                }
            }
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function valider($id) {
        $this->checkAuth();
        if ($_SESSION['user']['role'] != 'encadreur') die("Action non autorisée");
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT id_encadreur, statut, id_etudiant FROM memoires WHERE id = ?");
        $stmt->execute([$id]);
        $mem = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mem || $mem['id_encadreur'] != $_SESSION['user']['id']) die("Vous n'êtes pas l'encadreur de ce mémoire");
        if ($mem['statut'] != 'en_cours') die("Le mémoire doit être en cours pour être validé.");
        $pdo->prepare("UPDATE memoires SET statut = 'valide' WHERE id = ?")->execute([$id]);

        $this->notifier($mem['id_etudiant'], "Votre mémoire a été validé par l'encadreur", "/memoire/voir/$id");
        $this->notifierResponsable("Le mémoire #$id a été validé, il est en attente de planification de soutenance.", "/memoire/voir/$id");
        $this->notifierAdmins("Un mémoire (#$id) a été validé et est prêt pour la soutenance.", "/memoire/voir/$id");

        $stmtEtud = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = ?");
        $stmtEtud->execute([$mem['id_etudiant']]);
        $etudiant = $stmtEtud->fetch(PDO::FETCH_ASSOC);
        if ($etudiant) {
            $sujet = "Mémoire validé";
            $corps = "<p>Bonjour {$etudiant['prenom']} {$etudiant['nom']},</p><p>Votre mémoire a été validé par votre encadreur. Vous pouvez maintenant planifier votre soutenance.</p><p><a href='" . BASE_URL . "/memoire/voir/{$id}'>Voir le mémoire</a></p>";
            Mailer::send($etudiant['email'], $sujet, $corps);
        }

        header('Location: ' . BASE_URL . '/memoire/voir/' . $id);
        exit;
    }

    public function ajouterEtudiant($id_memoire) {
        $this->checkAuth();
        if ($_SESSION['user']['role'] != 'etudiant') die("Accès refusé");
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT id_etudiant FROM memoires WHERE id = ?");
        $stmt->execute([$id_memoire]);
        $mem = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mem || $mem['id_etudiant'] != $_SESSION['user']['id']) die("Action non autorisée");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_etudiant = $_POST['id_etudiant'];
            $role = $_POST['role'] ?? 'membre';
            $stmtCheck = $pdo->prepare("SELECT id FROM memoire_etudiants WHERE id_memoire = ? AND id_etudiant = ?");
            $stmtCheck->execute([$id_memoire, $id_etudiant]);
            if (!$stmtCheck->fetch()) {
                $stmtIns = $pdo->prepare("INSERT INTO memoire_etudiants (id_memoire, id_etudiant, role) VALUES (?, ?, ?)");
                $stmtIns->execute([$id_memoire, $id_etudiant, $role]);
            }
        }
        header('Location: ' . BASE_URL . '/memoire/voir/' . $id_memoire);
        exit;
    }

    public function retirerEtudiant($id_memoire, $id_etudiant) {
        $this->checkAuth();
        if ($_SESSION['user']['role'] != 'etudiant') die("Accès refusé");
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT id_etudiant FROM memoires WHERE id = ?");
        $stmt->execute([$id_memoire]);
        $mem = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$mem || $mem['id_etudiant'] != $_SESSION['user']['id']) die("Action non autorisée");
        if ($id_etudiant == $_SESSION['user']['id']) die("Vous ne pouvez pas vous retirer vous-même");
        $pdo->prepare("DELETE FROM memoire_etudiants WHERE id_memoire = ? AND id_etudiant = ?")->execute([$id_memoire, $id_etudiant]);
        header('Location: ' . BASE_URL . '/memoire/voir/' . $id_memoire);
        exit;
    }

    public function comparerVersions($id_memoire, $v1, $v2) {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT fichier, numero FROM versions WHERE id_memoire = ? AND numero IN (?, ?)");
        $stmt->execute([$id_memoire, $v1, $v2]);
        $versions = $stmt->fetchAll();
        if (count($versions) != 2) die("Versions invalides");

        require_once __DIR__ . '/../vendor/autoload.php';
        $parser = new \Smalot\PdfParser\Parser();
        $texts = [];
        foreach ($versions as $v) {
            $filepath = __DIR__ . '/../' . $v['fichier'];
            $pdf = $parser->parseFile($filepath);
            $texts[$v['numero']] = $pdf->getText();
        }

        $words1 = str_word_count(strtolower($texts[$v1]), 1);
        $words2 = str_word_count(strtolower($texts[$v2]), 1);
        $common = array_intersect($words1, $words2);
        $total = max(count($words1), count($words2));
        $similarity = $total > 0 ? round((count($common) / $total) * 100, 2) : 0;

        $diff = [
            'old' => $texts[$v1],
            'new' => $texts[$v2],
            'similarity' => $similarity,
            'v1' => $v1,
            'v2' => $v2
        ];
        require_once 'views/memoire/comparaison.php';
    }

    public function checkPlagiat($id_memoire) {
        $this->checkAuth();
        if ($_SESSION['user']['role'] != 'encadreur') die("Accès refusé (seul l'encadreur peut vérifier le plagiat)");
        $pdo = Db::getInstance();

        $stmt = $pdo->prepare("SELECT id, fichier FROM versions WHERE id_memoire = ? ORDER BY numero DESC LIMIT 1");
        $stmt->execute([$id_memoire]);
        $version = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$version) die("Aucune version trouvée");

        $filepath = __DIR__ . '/../' . $version['fichier'];
        if (!file_exists($filepath)) die("Fichier introuvable");

        require_once __DIR__ . '/../vendor/autoload.php';
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filepath);
        $texte_courant = $pdf->getText();

        $stmtAutres = $pdo->prepare("
            SELECT m.id, v.fichier, u.nom, u.prenom, m.titre
            FROM memoires m
            JOIN versions v ON m.id = v.id_memoire AND v.numero = m.version_actuelle
            JOIN utilisateurs u ON m.id_etudiant = u.id
            WHERE m.statut = 'soutenu' AND m.id != ?
        ");
        $stmtAutres->execute([$id_memoire]);
        $autres_memoires = $stmtAutres->fetchAll(PDO::FETCH_ASSOC);

        $scores = [];
        foreach ($autres_memoires as $mem) {
            $path = __DIR__ . '/../' . $mem['fichier'];
            if (!file_exists($path)) continue;
            $pdf2 = $parser->parseFile($path);
            $texte_autre = $pdf2->getText();

            $words1 = str_word_count(strtolower($texte_courant), 1);
            $words2 = str_word_count(strtolower($texte_autre), 1);
            $common = array_intersect($words1, $words2);
            $total = max(count($words1), count($words2));
            $similarity = $total > 0 ? round((count($common) / $total) * 100, 2) : 0;
            $scores[] = [
                'id' => $mem['id'],
                'titre' => $mem['titre'],
                'auteur' => $mem['prenom'] . ' ' . $mem['nom'],
                'score' => $similarity
            ];
        }

        usort($scores, function($a, $b) { return $b['score'] <=> $a['score']; });
        $top_scores = array_slice($scores, 0, 5);
        $max_score = !empty($top_scores) ? $top_scores[0]['score'] : 0;

        $result = [
            'score_max' => $max_score,
            'details' => $top_scores
        ];
        $stmtIns = $pdo->prepare("INSERT INTO plagiat_checks (id_memoire, id_version, score, rapport, status) VALUES (?, ?, ?, ?, 'termine')");
        $stmtIns->execute([$id_memoire, $version['id'], $max_score, json_encode($result)]);

        $_SESSION['flash'] = "Score de plagiat maximal : " . $max_score . "%";
        header('Location: ' . BASE_URL . '/memoire/voir/' . $id_memoire);
        exit;
    }

    private function getEtudiantId($id_memoire) {
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT id_etudiant FROM memoires WHERE id = ?");
        $stmt->execute([$id_memoire]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $r['id_etudiant'] : 0;
    }
}