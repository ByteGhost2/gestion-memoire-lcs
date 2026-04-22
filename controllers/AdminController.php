<?php
// controllers/AdminController.php
require_once __DIR__ . '/../utils/Mailer.php';

class AdminController {
    // Vérifie si l'utilisateur est admin OU responsable
    private function checkAdminOrResponsable() {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'responsable'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    // Vérifie uniquement admin (pour les actions sensibles)
    

    private function logAction($action, $details = '') {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $stmt = $pdo->prepare("INSERT INTO logs (id_utilisateur, action, details, ip, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user']['id'], $action, $details, $ip, $user_agent]);
    }

    private function notifier($id_user, $message, $lien = null) {
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("INSERT INTO notifications (id_utilisateur, message, lien) VALUES (?, ?, ?)");
        $stmt->execute([$id_user, $message, $lien]);
    }

    // Dashboard accessible au responsable
    public function dashboard() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();

        $stats = [];
        $stats['utilisateurs'] = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
        $stats['memoires'] = $pdo->query("SELECT COUNT(*) FROM memoires")->fetchColumn();
        $stats['soutenances'] = $pdo->query("SELECT COUNT(*) FROM soutenances")->fetchColumn();
        $stats['memoires_soutenus'] = $pdo->query("SELECT COUNT(*) FROM memoires WHERE statut='soutenu'")->fetchColumn();

        $derniers_memoires = $pdo->query("SELECT m.*, u.nom, u.prenom FROM memoires m JOIN utilisateurs u ON m.id_etudiant = u.id ORDER BY m.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $dernieres_soutenances = $pdo->query("SELECT s.*, m.titre, u.nom, u.prenom FROM soutenances s JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id ORDER BY s.date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

        $statsMois = $pdo->query("SELECT DATE_FORMAT(date_soumission, '%Y-%m') as mois, COUNT(*) as total FROM memoires GROUP BY mois ORDER BY mois DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/admin/dashboard.php';
    }

    // ==================== Gestion des utilisateurs (admin only) ====================
    public function utilisateurs() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $stmt = $pdo->query("SELECT * FROM utilisateurs ORDER BY date_inscription DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/utilisateurs.php';
    }

    public function ajouterUtilisateur() {
        $this->checkAdminOrResponsable();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $prenom = trim($_POST['prenom'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? '';
            $filiere = !empty($_POST['filiere']) ? trim($_POST['filiere']) : null;
            $password = $_POST['password'] ?? '';

            $errors = [];
            if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                $errors[] = "Tous les champs sont obligatoires.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Adresse email invalide.";
            }

            if (empty($errors)) {
                $pdo = Db::getInstance();
                $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $errors[] = "Cet email est déjà utilisé.";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    try {
                        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, filiere) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$nom, $prenom, $email, $hashed, $role, $filiere]);
                        $this->logAction("Ajout utilisateur", "Email: $email, Rôle: $role");
                        header('Location: ' . BASE_URL . '/admin/utilisateurs?success=1');
                        exit;
                    } catch (PDOException $e) {
                        if ($e->errorInfo[1] == 1062) {
                            $errors[] = "Cet email est déjà utilisé (contrainte).";
                        } else {
                            $errors[] = "Erreur base de données : " . $e->getMessage();
                        }
                    }
                }
            }
            $error = implode('<br>', $errors);
            require_once 'views/admin/ajouter_utilisateur.php';
        } else {
            require_once 'views/admin/ajouter_utilisateur.php';
        }
    }

    public function modifierUtilisateur($id) {
    $this->checkAdminOrResponsable();
    $pdo = Db::getInstance();

    // Récupérer l'utilisateur à modifier (pas l'admin connecté)
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("Utilisateur introuvable. ID reçu : " . htmlspecialchars($id));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? '';
        $filiere = !empty($_POST['filiere']) ? trim($_POST['filiere']) : null;

        $errors = [];

        // Vérifier si l'email est déjà utilisé par un autre utilisateur
        $stmtCheck = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
        $stmtCheck->execute([$email, $id]);
        if ($stmtCheck->fetch()) {
            $errors[] = "Cet email est déjà utilisé par un autre compte.";
        }

        if (empty($errors)) {
            // Mise à jour des informations
            $stmtUpdate = $pdo->prepare("UPDATE utilisateurs SET nom=?, prenom=?, email=?, role=?, filiere=? WHERE id=?");
            $stmtUpdate->execute([$nom, $prenom, $email, $role, $filiere, $id]);

            // Changer le mot de passe si fourni
            if (!empty($_POST['new_password'])) {
                if (strlen($_POST['new_password']) < 6) {
                    $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
                } else {
                    $newPass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    $pdo->prepare("UPDATE utilisateurs SET mot_de_passe=? WHERE id=?")->execute([$newPass, $id]);
                }
            }

            if (empty($errors)) {
                $this->logAction("Modification utilisateur", "ID: $id, Email: $email");
                header('Location: ' . BASE_URL . '/admin/utilisateurs?updated=1');
                exit;
            }
        }

        // En cas d'erreur, on recharge la vue avec les messages
        $error = implode('<br>', $errors);
    }

    require_once 'views/admin/modifier_utilisateur.php';
}

    public function supprimerUtilisateur($id) {
        $this->checkAdminOrResponsable();
        if ($id == $_SESSION['user']['id']) {
            die("Vous ne pouvez pas vous supprimer vous-même");
        }
        $pdo = Db::getInstance();
        $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$id]);
        $this->logAction("Suppression utilisateur", "ID: $id");
        header('Location: ' . BASE_URL . '/admin/utilisateurs?deleted=1');
        exit;
    }

    // ==================== Gestion des filières (admin only) ====================
    public function filieres() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                $nom = trim($_POST['nom']);
                if (!empty($nom)) {
                    $pdo->prepare("INSERT INTO filieres (nom) VALUES (?)")->execute([$nom]);
                    $this->logAction("Ajout filière", "Nom: $nom");
                }
            } elseif ($_POST['action'] === 'edit') {
                $id = $_POST['id'];
                $nom = trim($_POST['nom']);
                if (!empty($nom)) {
                    $pdo->prepare("UPDATE filieres SET nom = ? WHERE id = ?")->execute([$nom, $id]);
                    $this->logAction("Modification filière", "ID: $id, Nouveau nom: $nom");
                }
            }
            header('Location: ' . BASE_URL . '/admin/filieres');
            exit;
        }
        $filieres = $pdo->query("SELECT * FROM filieres ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/filieres.php';
    }

    public function supprimerFiliere($id) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $pdo->prepare("DELETE FROM filieres WHERE id = ?")->execute([$id]);
        $this->logAction("Suppression filière", "ID: $id");
        header('Location: ' . BASE_URL . '/admin/filieres');
        exit;
    }

    // ==================== Gestion des salles (admin only) ====================
    public function salles() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $capacite = intval($_POST['capacite']);
            $equipement = $_POST['equipement'] ?? '';
            $active = isset($_POST['active']) ? 1 : 0;
            $pdo->prepare("INSERT INTO salles (nom, capacite, equipement, active) VALUES (?, ?, ?, ?)")->execute([$nom, $capacite, $equipement, $active]);
            $this->logAction("Ajout salle", "Nom: $nom");
            header('Location: ' . BASE_URL . '/admin/salles');
            exit;
        }
        $salles = $pdo->query("SELECT * FROM salles ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/salles.php';
    }

    public function modifierSalle($id) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $capacite = intval($_POST['capacite']);
            $equipement = $_POST['equipement'] ?? '';
            $active = isset($_POST['active']) ? 1 : 0;
            $pdo->prepare("UPDATE salles SET nom=?, capacite=?, equipement=?, active=? WHERE id=?")->execute([$nom, $capacite, $equipement, $active, $id]);
            $this->logAction("Modification salle", "ID: $id");
            header('Location: ' . BASE_URL . '/admin/salles');
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM salles WHERE id = ?");
        $stmt->execute([$id]);
        $salle = $stmt->fetch(PDO::FETCH_ASSOC);
        require_once 'views/admin/modifier_salle.php';
    }

    public function supprimerSalle($id) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $pdo->prepare("DELETE FROM salles WHERE id = ?")->execute([$id]);
        $this->logAction("Suppression salle", "ID: $id");
        header('Location: ' . BASE_URL . '/admin/salles');
        exit;
    }

    // ==================== Gestion des années universitaires (admin only) ====================
    public function annees() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $libelle = trim($_POST['libelle']);
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];
            $active = isset($_POST['active']) ? 1 : 0;
            if (!empty($libelle) && !empty($date_debut) && !empty($date_fin)) {
                if ($active) {
                    $pdo->exec("UPDATE annees_universitaires SET active = 0");
                }
                $pdo->prepare("INSERT INTO annees_universitaires (libelle, date_debut, date_fin, active) VALUES (?, ?, ?, ?)")->execute([$libelle, $date_debut, $date_fin, $active]);
                $this->logAction("Ajout année universitaire", "Libellé: $libelle");
            }
            header('Location: ' . BASE_URL . '/admin/annees');
            exit;
        }
        $annees = $pdo->query("SELECT * FROM annees_universitaires ORDER BY date_debut DESC")->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/annees.php';
    }

    public function setAnneeActive($id) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $pdo->exec("UPDATE annees_universitaires SET active = 0");
        $pdo->prepare("UPDATE annees_universitaires SET active = 1 WHERE id = ?")->execute([$id]);
        $this->logAction("Changement année active", "ID: $id");
        header('Location: ' . BASE_URL . '/admin/annees');
        exit;
    }

    // ==================== Gestion des critères d'évaluation (admin only) ====================
    public function criteres() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $description = $_POST['description'] ?? '';
            $poids = floatval($_POST['poids'] ?? 1);
            $actif = isset($_POST['actif']) ? 1 : 0;
            $pdo->prepare("INSERT INTO criteres_evaluation (nom, description, poids, actif) VALUES (?, ?, ?, ?)")->execute([$nom, $description, $poids, $actif]);
            $this->logAction("Ajout critère", "Nom: $nom");
            header('Location: ' . BASE_URL . '/admin/criteres');
            exit;
        }
        $criteres = $pdo->query("SELECT * FROM criteres_evaluation ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/criteres.php';
    }

    public function modifierCritere($id) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom']);
            $description = $_POST['description'] ?? '';
            $poids = floatval($_POST['poids'] ?? 1);
            $actif = isset($_POST['actif']) ? 1 : 0;
            $pdo->prepare("UPDATE criteres_evaluation SET nom=?, description=?, poids=?, actif=? WHERE id=?")->execute([$nom, $description, $poids, $actif, $id]);
            $this->logAction("Modification critère", "ID: $id");
            header('Location: ' . BASE_URL . '/admin/criteres');
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM criteres_evaluation WHERE id = ?");
        $stmt->execute([$id]);
        $critere = $stmt->fetch(PDO::FETCH_ASSOC);
        require_once 'views/admin/modifier_critere.php';
    }

    public function supprimerCritere($id) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $pdo->prepare("DELETE FROM criteres_evaluation WHERE id = ?")->execute([$id]);
        $this->logAction("Suppression critère", "ID: $id");
        header('Location: ' . BASE_URL . '/admin/criteres');
        exit;
    }

    // ==================== Gestion des mémoires (accessible au responsable) ====================
    public function memoires() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $stmt = $pdo->query("SELECT m.*, u.nom, u.prenom, u.email FROM memoires m JOIN utilisateurs u ON m.id_etudiant = u.id ORDER BY m.created_at DESC");
        $memoires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/memoires.php';
    }

    public function voirMemoire($id) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT m.*, u.nom, u.prenom, u.email FROM memoires m JOIN utilisateurs u ON m.id_etudiant = u.id WHERE m.id = ?");
        $stmt->execute([$id]);
        $memoire = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$memoire) die("Mémoire introuvable");

        $versions = $pdo->prepare("SELECT * FROM versions WHERE id_memoire = ? ORDER BY numero DESC");
        $versions->execute([$id]);
        $versions = $versions->fetchAll(PDO::FETCH_ASSOC);

        $encadreurs = $pdo->query("SELECT id, nom, prenom FROM utilisateurs WHERE role='encadreur' ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/admin/voir_memoire.php';
    }

    public function updateMemoireStatut() {
        $this->checkAdminOrResponsable();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $statut = $_POST['statut'];
            $pdo = Db::getInstance();
            $pdo->prepare("UPDATE memoires SET statut = ? WHERE id = ?")->execute([$statut, $id]);
            $this->logAction("Changement statut mémoire", "ID: $id, Nouveau statut: $statut");
            echo json_encode(['success' => true]);
            exit;
        }
    }

    public function assignerEncadreur() {
        $this->checkAdminOrResponsable();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_memoire = $_POST['id_memoire'];
            $id_encadreur = $_POST['id_encadreur'] ?: null;
            $pdo = Db::getInstance();
            $pdo->prepare("UPDATE memoires SET id_encadreur = ? WHERE id = ?")->execute([$id_encadreur, $id_memoire]);

            if ($id_encadreur) {
                $this->notifier($id_encadreur, "Vous avez été assigné comme encadreur pour le mémoire #$id_memoire", "/memoire/voir/$id_memoire");
                $this->logAction("Assignation encadreur", "Mémoire ID: $id_memoire, Encadreur ID: $id_encadreur");

                $stmt = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = ?");
                $stmt->execute([$id_encadreur]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $sujet = "Affectation en tant qu'encadreur";
                    $corps = "<p>Bonjour {$user['prenom']} {$user['nom']},</p>
                              <p>Vous avez été affecté comme encadreur pour le mémoire <strong>#{$id_memoire}</strong>.</p>
                              <p><a href='" . BASE_URL . "/memoire/voir/{$id_memoire}'>Voir le mémoire</a></p>";
                    Mailer::send($user['email'], $sujet, $corps);
                }
            }
            header('Location: ' . BASE_URL . '/admin/voirMemoire/' . $id_memoire);
            exit;
        }
    }

    // ==================== Gestion des soutenances (accessible au responsable) ====================
    public function soutenances() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $stmt = $pdo->query("
            SELECT s.*, m.titre, u.nom, u.prenom 
            FROM soutenances s 
            JOIN memoires m ON s.id_memoire = m.id 
            JOIN utilisateurs u ON m.id_etudiant = u.id 
            ORDER BY s.date DESC
        ");
        $soutenances = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/soutenances.php';
    }

    public function planifierSoutenance() {
        $this->checkAdminOrResponsable();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_memoire = $_POST['id_memoire'];
            $date = $_POST['date'];
            $heure_debut = $_POST['heure_debut'];
            $heure_fin = $_POST['heure_fin'];
            $salle = $_POST['salle'];

            $pdo = Db::getInstance();

            // Vérifier la disponibilité de la salle
            $stmt = $pdo->prepare("
                SELECT id FROM soutenances 
                WHERE salle = ? AND date = ? 
                AND (
                    (heure_debut <= ? AND heure_fin > ?) OR
                    (heure_debut < ? AND heure_fin >= ?)
                )
            ");
            $stmt->execute([$salle, $date, $heure_debut, $heure_debut, $heure_fin, $heure_fin]);
            if ($stmt->fetch()) {
                $error = "La salle est déjà occupée sur ce créneau";
            } else {
                $stmtIns = $pdo->prepare("INSERT INTO soutenances (id_memoire, date, heure_debut, heure_fin, salle) VALUES (?, ?, ?, ?, ?)");
                if ($stmtIns->execute([$id_memoire, $date, $heure_debut, $heure_fin, $salle])) {
                    $id_soutenance = $pdo->lastInsertId();
                    $etudiant = $this->getEtudiantId($id_memoire);
                    $encadreur = $this->getEncadreurId($id_memoire);
                    if ($etudiant) {
                        $this->notifier($etudiant, "Votre soutenance est planifiée le $date à $heure_debut", "/soutenance/planning");
                        $stmt = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = ?");
                        $stmt->execute([$etudiant]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($user) {
                            $sujet = "Soutenance planifiée";
                            $corps = "<p>Bonjour {$user['prenom']} {$user['nom']},</p>
                                      <p>Votre soutenance est planifiée le " . date('d/m/Y', strtotime($date)) . " à {$heure_debut} en salle {$salle}.</p>
                                      <p><a href='" . BASE_URL . "/soutenance/planning'>Voir le planning</a></p>";
                            Mailer::send($user['email'], $sujet, $corps);
                        }
                    }
                    if ($encadreur) {
                        $this->notifier($encadreur, "Soutenance planifiée pour votre étudiant le $date", "/soutenance/planning");
                        $stmt = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = ?");
                        $stmt->execute([$encadreur]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($user) {
                            $sujet = "Soutenance de votre étudiant planifiée";
                            $corps = "<p>Bonjour {$user['prenom']} {$user['nom']},</p>
                                      <p>La soutenance de votre étudiant est planifiée le " . date('d/m/Y', strtotime($date)) . " à {$heure_debut} en salle {$salle}.</p>
                                      <p><a href='" . BASE_URL . "/soutenance/planning'>Voir le planning</a></p>";
                            Mailer::send($user['email'], $sujet, $corps);
                        }
                    }
                    $this->logAction("Planification soutenance", "Mémoire ID: $id_memoire, Date: $date");
                    header('Location: ' . BASE_URL . '/admin/soutenances?planned=1');
                    exit;
                }
            }
        }

        $pdo = Db::getInstance();
        $memoires = $pdo->query("
            SELECT m.id, m.titre, u.nom, u.prenom 
            FROM memoires m 
            JOIN utilisateurs u ON m.id_etudiant = u.id 
            WHERE m.statut = 'valide' 
            AND NOT EXISTS (SELECT 1 FROM soutenances s WHERE s.id_memoire = m.id)
        ")->fetchAll(PDO::FETCH_ASSOC);

        $salles = $pdo->query("SELECT nom FROM salles WHERE active = 1 ORDER BY nom")->fetchAll(PDO::FETCH_COLUMN);

        require_once 'views/admin/planifier_soutenance.php';
    }

    public function proposerCreneaux($id_memoire) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();

        try {
            $stmt = $pdo->prepare("SELECT id_etudiant, id_encadreur FROM memoires WHERE id = ?");
            $stmt->execute([$id_memoire]);
            $memoire = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$memoire) throw new Exception("Mémoire introuvable");

            $stmtJurys = $pdo->query("SELECT id FROM utilisateurs WHERE role IN ('jury', 'encadreur')");
            $jurys = $stmtJurys->fetchAll(PDO::FETCH_COLUMN);
            $participants = array_merge([$memoire['id_encadreur']], $jurys);
            $participants = array_values(array_unique(array_filter($participants)));

            $indispos = [];
            if (!empty($participants)) {
                $placeholders = implode(',', array_fill(0, count($participants), '?'));
                $sql = "SELECT id_utilisateur, date, heure_debut, heure_fin FROM disponibilites WHERE id_utilisateur IN ($placeholders) AND date >= CURDATE()";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($participants);
                $indispos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $salles = $pdo->query("SELECT nom FROM salles WHERE active = 1")->fetchAll(PDO::FETCH_COLUMN);
            if (empty($salles)) throw new Exception("Aucune salle active disponible.");

            $propositions = [];
            for ($i = 1; $i <= 7; $i++) {
                $date = date('Y-m-d', strtotime("+$i days"));
                for ($h = 8; $h <= 16; $h++) {
                    $debut = sprintf("%02d:00", $h);
                    $fin = sprintf("%02d:00", $h+1);
                    $disponible = true;
                    foreach ($indispos as $ind) {
                        if ($ind['date'] == $date && $ind['heure_debut'] <= $fin && $ind['heure_fin'] > $debut) {
                            $disponible = false;
                            break;
                        }
                    }
                    if (!$disponible) continue;

                    foreach ($salles as $salle) {
                        $stmt = $pdo->prepare("SELECT id FROM soutenances WHERE salle = ? AND date = ? AND heure_debut < ? AND heure_fin > ?");
                        $stmt->execute([$salle, $date, $fin, $debut]);
                        if (!$stmt->fetch()) {
                            $propositions[] = [
                                'date' => $date,
                                'heure_debut' => $debut,
                                'heure_fin' => $fin,
                                'salle' => $salle
                            ];
                            break;
                        }
                    }
                }
            }

            header('Content-Type: application/json');
            echo json_encode($propositions);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public function gererJury($id_soutenance) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_utilisateur = $_POST['id_utilisateur'] ?? 0;
            $role = $_POST['role'] ?? '';

            $stmtCheck = $pdo->prepare("SELECT id FROM jury WHERE id_soutenance = ? AND id_utilisateur = ?");
            $stmtCheck->execute([$id_soutenance, $id_utilisateur]);
            if ($stmtCheck->fetch()) {
                $_SESSION['flash_error'] = "Ce membre est déjà dans le jury.";
                header('Location: ' . BASE_URL . '/admin/gererJury/' . $id_soutenance);
                exit;
            }

            if ($role == 'president') {
                $stmtPres = $pdo->prepare("SELECT id FROM jury WHERE id_soutenance = ? AND role = 'president'");
                $stmtPres->execute([$id_soutenance]);
                if ($stmtPres->fetch()) {
                    $_SESSION['flash_error'] = "Un président est déjà affecté.";
                    header('Location: ' . BASE_URL . '/admin/gererJury/' . $id_soutenance);
                    exit;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO jury (id_soutenance, id_utilisateur, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$id_soutenance, $id_utilisateur, $role])) {
                $this->notifier($id_utilisateur, "Vous avez été ajouté au jury de la soutenance #$id_soutenance", "/soutenance/planning");
                $this->logAction("Ajout membre jury", "Soutenance ID: $id_soutenance, Utilisateur ID: $id_utilisateur");
            }

            $stmtUser = $pdo->prepare("SELECT email, prenom, nom FROM utilisateurs WHERE id = ?");
            $stmtUser->execute([$id_utilisateur]);
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

            $stmtSout = $pdo->prepare("SELECT date, heure_debut FROM soutenances WHERE id = ?");
            $stmtSout->execute([$id_soutenance]);
            $soutenance = $stmtSout->fetch(PDO::FETCH_ASSOC);

            if ($user && $soutenance) {
                $sujet = "Affectation au jury de soutenance";
                $corps = "<p>Bonjour {$user['prenom']} {$user['nom']},</p>
                          <p>Vous avez été ajouté en tant que <strong>{$role}</strong> pour la soutenance du " . date('d/m/Y', strtotime($soutenance['date'])) . " à " . substr($soutenance['heure_debut'],0,5) . ".</p>
                          <p><a href='" . BASE_URL . "/soutenance/planning'>Voir le planning</a></p>";
                Mailer::send($user['email'], $sujet, $corps);
            }

            header('Location: ' . BASE_URL . '/admin/gererJury/' . $id_soutenance);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT s.*, m.titre, u.nom, u.prenom 
            FROM soutenances s 
            JOIN memoires m ON s.id_memoire = m.id 
            JOIN utilisateurs u ON m.id_etudiant = u.id 
            WHERE s.id = ?
        ");
        $stmt->execute([$id_soutenance]);
        $soutenance = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$soutenance) die("Soutenance introuvable");

        $stmtJury = $pdo->prepare("SELECT j.*, u.nom, u.prenom FROM jury j JOIN utilisateurs u ON j.id_utilisateur = u.id WHERE j.id_soutenance = ?");
        $stmtJury->execute([$id_soutenance]);
        $jury = $stmtJury->fetchAll(PDO::FETCH_ASSOC);

        $stmtEns = $pdo->prepare("SELECT id, nom, prenom, email FROM utilisateurs WHERE role IN ('encadreur', 'jury') ORDER BY nom");
        $stmtEns->execute();
        $enseignants = $stmtEns->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/admin/gerer_jury.php';
    }

    public function retirerMembreJury($id_jury) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT id_soutenance FROM jury WHERE id = ?");
        $stmt->execute([$id_jury]);
        $jury = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($jury) {
            $pdo->prepare("DELETE FROM jury WHERE id = ?")->execute([$id_jury]);
            $this->logAction("Retrait membre jury", "Jury ID: $id_jury");
        }
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/admin/soutenances');
        exit;
    }

    public function terminerSoutenance($id_soutenance) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();

        $stmt = $pdo->prepare("SELECT id FROM soutenances WHERE id = ?");
        $stmt->execute([$id_soutenance]);
        if (!$stmt->fetch()) die("Soutenance introuvable.");

        $pdo->prepare("UPDATE soutenances SET statut = 'terminee' WHERE id = ?")->execute([$id_soutenance]);

        $stmtJury = $pdo->prepare("SELECT id_utilisateur FROM jury WHERE id_soutenance = ?");
        $stmtJury->execute([$id_soutenance]);
        while ($membre = $stmtJury->fetch()) {
            $this->notifier($membre['id_utilisateur'], "La soutenance #$id_soutenance a été marquée comme terminée.", "/soutenance/planning");
        }

        $this->logAction("Marquage soutenance terminée", "Soutenance ID: $id_soutenance");
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/admin/soutenances');
        exit;
    }

    // ==================== Gestion de la bibliothèque (accessible au responsable) ====================
    public function bibliotheque() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $memoires = $pdo->query("
            SELECT m.*, u.nom, u.prenom 
            FROM memoires m 
            JOIN utilisateurs u ON m.id_etudiant = u.id 
            WHERE m.statut = 'soutenu' 
            ORDER BY m.date_soumission DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/bibliotheque.php';
    }

    public function togglePublic($id) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $pdo->prepare("UPDATE memoires SET statut = 'archive' WHERE id = ? AND statut='soutenu'")->execute([$id]);
        $this->logAction("Masquage mémoire bibliothèque", "Mémoire ID: $id");
        header('Location: ' . BASE_URL . '/admin/bibliotheque');
        exit;
    }

    // ==================== Gestion des matricules (admin only) ====================
    public function matricules() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $matricule = trim($_POST['matricule']);
            if (!empty($matricule)) {
                $pdo->prepare("INSERT INTO matricules (matricule) VALUES (?)")->execute([$matricule]);
                $this->logAction("Ajout matricule", "Matricule: $matricule");
            }
            header('Location: ' . BASE_URL . '/admin/matricules');
            exit;
        }
        require_once 'views/admin/matricules.php';
    }

    public function supprimerMatricule($id) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $pdo->prepare("DELETE FROM matricules WHERE id = ?")->execute([$id]);
        $this->logAction("Suppression matricule", "ID: $id");
        header('Location: ' . BASE_URL . '/admin/matricules');
        exit;
    }

    // ==================== Export (admin only) ====================
    public function export($type, $format) {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $data = [];
        $filename = 'export_' . $type . '_' . date('Ymd_His');

        switch ($type) {
            case 'utilisateurs':
                $data = $pdo->query("SELECT id, nom, prenom, email, role, filiere, date_inscription FROM utilisateurs ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
                $filename .= '_utilisateurs';
                break;
            case 'memoires':
                $data = $pdo->query("SELECT m.id, m.titre, u.nom, u.prenom, m.statut, m.date_soumission FROM memoires m JOIN utilisateurs u ON m.id_etudiant = u.id ORDER BY m.id")->fetchAll(PDO::FETCH_ASSOC);
                $filename .= '_memoires';
                break;
            case 'soutenances':
                $data = $pdo->query("SELECT s.id, m.titre, u.nom, u.prenom, s.date, s.heure_debut, s.heure_fin, s.salle, s.statut FROM soutenances s JOIN memoires m ON s.id_memoire = m.id JOIN utilisateurs u ON m.id_etudiant = u.id ORDER BY s.date")->fetchAll(PDO::FETCH_ASSOC);
                $filename .= '_soutenances';
                break;
            default:
                die("Type d'export invalide");
        }

        if ($format === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            $output = fopen('php://output', 'w');
            if (!empty($data)) {
                fputcsv($output, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
            fclose($output);
            exit;
        } elseif ($format === 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
            echo "<table border='1'>";
            if (!empty($data)) {
                echo "<thead><tr>";
                foreach (array_keys($data[0]) as $header) {
                    echo "<th>" . htmlspecialchars($header) . "</th>";
                }
                echo "</thead><tbody>";
                foreach ($data as $row) {
                    echo "<tr>";
                    foreach ($row as $cell) {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            exit;
        }
    }

    // ==================== Logs (admin only) ====================
    public function logs() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $logs = $pdo->query("SELECT l.*, u.nom, u.prenom FROM logs l JOIN utilisateurs u ON l.id_utilisateur = u.id ORDER BY l.date DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/logs.php';
    }

    // ==================== Paramètres (admin only) ====================
    public function parametres() {
    $this->checkAdminOrResponsable();
    $pdo = Db::getInstance();
    $settingsFile = __DIR__ . '/../config/settings.json';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $settings = [
            'nom_etablissement' => trim($_POST['nom_etablissement'] ?? 'Institut Universitaire Les COURS SONOU'),
            'email_contact' => trim($_POST['email_contact'] ?? 'contact@iucs.bj'),
            'telephone' => trim($_POST['telephone'] ?? '+229 01 23 45 67'),
            'adresse' => trim($_POST['adresse'] ?? 'Cotonou, Bénin'),
            'site_web' => trim($_POST['site_web'] ?? 'https://iucs.bj'),
            'facebook' => trim($_POST['facebook'] ?? ''),
            'twitter' => trim($_POST['twitter'] ?? ''),
            'linkedin' => trim($_POST['linkedin'] ?? ''),
            'instagram' => trim($_POST['instagram'] ?? ''),
            'mail_host' => trim($_POST['mail_host'] ?? MAIL_HOST),
            'mail_port' => trim($_POST['mail_port'] ?? MAIL_PORT),
            'mail_user' => trim($_POST['mail_user'] ?? MAIL_USER),
            'mail_pass' => trim($_POST['mail_pass'] ?? MAIL_PASS),
            'mail_from' => trim($_POST['mail_from'] ?? MAIL_FROM),
            'mail_from_name' => trim($_POST['mail_from_name'] ?? MAIL_FROM_NAME),
        ];
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
        $this->logAction("Modification paramètres");
        $success = "Paramètres enregistrés avec succès.";
        // Mettre à jour les constantes pour la session courante
        define('MAIL_HOST', $settings['mail_host']);
        define('MAIL_PORT', $settings['mail_port']);
        define('MAIL_USER', $settings['mail_user']);
        define('MAIL_PASS', $settings['mail_pass']);
        define('MAIL_FROM', $settings['mail_from']);
        define('MAIL_FROM_NAME', $settings['mail_from_name']);
    }
    
    $settings = [];
    if (file_exists($settingsFile)) {
        $settings = json_decode(file_get_contents($settingsFile), true);
    } else {
        // Valeurs par défaut
        $settings = [
            'nom_etablissement' => 'Institut Universitaire Les COURS SONOU',
            'email_contact' => 'contact@iucs.bj',
            'telephone' => '+229 01 23 45 67',
            'adresse' => 'Cotonou, Bénin',
            'site_web' => 'https://iucs.bj',
            'facebook' => '',
            'twitter' => '',
            'linkedin' => '',
            'instagram' => '',
            'mail_host' => MAIL_HOST,
            'mail_port' => MAIL_PORT,
            'mail_user' => MAIL_USER,
            'mail_pass' => MAIL_PASS,
            'mail_from' => MAIL_FROM,
            'mail_from_name' => MAIL_FROM_NAME,
        ];
    }
    
    require_once 'views/admin/parametres.php';
}

    // ==================== Statistiques (accessible au responsable) ====================
    public function statistiques() {
        $this->checkAdminOrResponsable();
        $pdo = Db::getInstance();
        $mentions = $pdo->query("SELECT mention, COUNT(*) as total FROM evaluations WHERE mention IS NOT NULL AND mention != '' GROUP BY mention")->fetchAll(PDO::FETCH_ASSOC);
        $statuts = $pdo->query("SELECT statut, COUNT(*) as total FROM memoires GROUP BY statut")->fetchAll(PDO::FETCH_ASSOC);
        $mois = $pdo->query("SELECT DATE_FORMAT(date_soumission, '%Y-%m') as mois, COUNT(*) as total FROM memoires GROUP BY mois ORDER BY mois DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/admin/statistiques.php';
    }

    // ==================== Méthodes utilitaires ====================
    private function getEtudiantId($id_memoire) {
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT id_etudiant FROM memoires WHERE id = ?");
        $stmt->execute([$id_memoire]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $r['id_etudiant'] : 0;
    }

    private function getEncadreurId($id_memoire) {
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT id_encadreur FROM memoires WHERE id = ?");
        $stmt->execute([$id_memoire]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $r['id_encadreur'] : 0;
    }
}