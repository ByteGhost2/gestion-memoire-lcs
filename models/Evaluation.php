<?php
// models/Evaluation.php
class Evaluation {
    private $pdo;

    public function __construct() {
        $this->pdo = Db::getInstance();
    }

    public function getBySoutenanceAndUser($id_soutenance, $id_user) {
        $stmt = $this->pdo->prepare("SELECT * FROM evaluations WHERE id_soutenance = ? AND id_utilisateur = ?");
        $stmt->execute([$id_soutenance, $id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function save($id_soutenance, $id_user, $note, $appreciation) {
        // Vérifier si existe
        $existing = $this->getBySoutenanceAndUser($id_soutenance, $id_user);
        if ($existing) {
            $stmt = $this->pdo->prepare("UPDATE evaluations SET note = ?, appreciation = ? WHERE id_soutenance = ? AND id_utilisateur = ?");
            return $stmt->execute([$note, $appreciation, $id_soutenance, $id_user]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO evaluations (id_soutenance, id_utilisateur, note, appreciation) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$id_soutenance, $id_user, $note, $appreciation]);
        }
    }
}