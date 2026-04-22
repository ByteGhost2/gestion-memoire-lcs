<?php
// models/Notification.php
class Notification {
    private $pdo;

    public function __construct() {
        $this->pdo = Db::getInstance();
    }

    public function getUnreadByUser($id_user) {
        $stmt = $this->pdo->prepare("SELECT * FROM notifications WHERE id_utilisateur = ? AND lu = FALSE ORDER BY date_creation DESC");
        $stmt->execute([$id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsRead($id_notif) {
        $stmt = $this->pdo->prepare("UPDATE notifications SET lu = TRUE WHERE id = ?");
        return $stmt->execute([$id_notif]);
    }

    public function create($id_user, $message, $lien = null) {
        $stmt = $this->pdo->prepare("INSERT INTO notifications (id_utilisateur, message, lien) VALUES (?, ?, ?)");
        return $stmt->execute([$id_user, $message, $lien]);
    }
}