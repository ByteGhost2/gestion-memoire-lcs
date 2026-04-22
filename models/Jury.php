<?php
// models/Jury.php
class Jury {
    private $pdo;

    public function __construct() {
        $this->pdo = Db::getInstance();
    }

    public function getBySoutenance($id_soutenance) {
        $stmt = $this->pdo->prepare("SELECT j.*, u.nom, u.prenom FROM jury j JOIN utilisateurs u ON j.id_utilisateur = u.id WHERE j.id_soutenance = ?");
        $stmt->execute([$id_soutenance]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addMember($id_soutenance, $id_utilisateur, $role) {
        $stmt = $this->pdo->prepare("INSERT INTO jury (id_soutenance, id_utilisateur, role) VALUES (?, ?, ?)");
        return $stmt->execute([$id_soutenance, $id_utilisateur, $role]);
    }

    public function removeMember($id_soutenance, $id_utilisateur) {
        $stmt = $this->pdo->prepare("DELETE FROM jury WHERE id_soutenance = ? AND id_utilisateur = ?");
        return $stmt->execute([$id_soutenance, $id_utilisateur]);
    }
}