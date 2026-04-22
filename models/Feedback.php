<?php
// models/Feedback.php
class Feedback {
    private $pdo;

    public function __construct() {
        $this->pdo = Db::getInstance();
    }

    public function getByVersion($id_version) {
        $stmt = $this->pdo->prepare("SELECT f.*, u.nom, u.prenom, u.role FROM feedbacks f JOIN utilisateurs u ON f.id_utilisateur = u.id WHERE f.id_version = ? ORDER BY f.date DESC");
        $stmt->execute([$id_version]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($id_version, $id_utilisateur, $message) {
        $stmt = $this->pdo->prepare("INSERT INTO feedbacks (id_version, id_utilisateur, message) VALUES (?, ?, ?)");
        return $stmt->execute([$id_version, $id_utilisateur, $message]);
    }
}