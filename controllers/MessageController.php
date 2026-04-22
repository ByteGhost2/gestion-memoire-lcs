<?php
// controllers/MessageController.php
class MessageController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        // Récupérer les conversations de l'utilisateur
        $stmt = $pdo->prepare("
            SELECT c.*, 
                (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND lu = FALSE AND expediteur_id != ?) as non_lus,
                (SELECT message FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as dernier_message,
                (SELECT created_at FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as derniere_activite
            FROM conversations c
            JOIN conversation_participants cp ON c.id = cp.conversation_id
            WHERE cp.utilisateur_id = ?
            ORDER BY derniere_activite DESC
        ");
        $stmt->execute([$id_user, $id_user]);
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/messages/index.php';
    }

    public function voir($id_conversation) {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        // Vérifier que l'utilisateur est participant
        $stmt = $pdo->prepare("SELECT id FROM conversation_participants WHERE conversation_id = ? AND utilisateur_id = ?");
        $stmt->execute([$id_conversation, $id_user]);
        if (!$stmt->fetch()) {
            die("Accès non autorisé à cette conversation.");
        }

        // Marquer les messages comme lus
        $pdo->prepare("UPDATE messages SET lu = TRUE WHERE conversation_id = ? AND expediteur_id != ?")->execute([$id_conversation, $id_user]);

        // Récupérer les messages
        $stmt = $pdo->prepare("
            SELECT m.*, u.nom, u.prenom 
            FROM messages m
            JOIN utilisateurs u ON m.expediteur_id = u.id
            WHERE m.conversation_id = ?
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$id_conversation]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Infos conversation
        $conv = $pdo->prepare("SELECT * FROM conversations WHERE id = ?")->execute([$id_conversation]);
        $conv = $stmt->fetch(PDO::FETCH_ASSOC);

        require_once 'views/messages/voir.php';
    }

    public function envoyer() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $conversation_id = $_POST['conversation_id'];
            $message = trim($_POST['message']);
            if (empty($message)) {
                die("Message vide");
            }
            $pdo = Db::getInstance();
            $id_user = $_SESSION['user']['id'];

            // Vérifier participant
            $stmt = $pdo->prepare("SELECT id FROM conversation_participants WHERE conversation_id = ? AND utilisateur_id = ?");
            $stmt->execute([$conversation_id, $id_user]);
            if (!$stmt->fetch()) {
                die("Non autorisé");
            }

            $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, expediteur_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$conversation_id, $id_user, $message]);

            header('Location: ' . BASE_URL . '/messages/voir/' . $conversation_id);
            exit;
        }
    }

    public function nouvelle($id_autre) {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $id_user = $_SESSION['user']['id'];

        // Vérifier si une conversation existe déjà entre les deux
        $stmt = $pdo->prepare("
            SELECT c.id 
            FROM conversations c
            JOIN conversation_participants cp1 ON c.id = cp1.conversation_id
            JOIN conversation_participants cp2 ON c.id = cp2.conversation_id
            WHERE cp1.utilisateur_id = ? AND cp2.utilisateur_id = ?
            GROUP BY c.id
            HAVING COUNT(*) = 2
        ");
        $stmt->execute([$id_user, $id_autre]);
        $existing = $stmt->fetch();
        if ($existing) {
            header('Location: ' . BASE_URL . '/messages/voir/' . $existing['id']);
            exit;
        }

        // Créer nouvelle conversation
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO conversations (titre) VALUES (?)");
            $stmt->execute(['Discussion']);
            $conv_id = $pdo->lastInsertId();

            // Ajouter les deux participants
            $pdo->prepare("INSERT INTO conversation_participants (conversation_id, utilisateur_id) VALUES (?, ?)")->execute([$conv_id, $id_user]);
            $pdo->prepare("INSERT INTO conversation_participants (conversation_id, utilisateur_id) VALUES (?, ?)")->execute([$conv_id, $id_autre]);

            $pdo->commit();
            header('Location: ' . BASE_URL . '/messages/voir/' . $conv_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Erreur création conversation");
        }
    }
}