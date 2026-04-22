<?php
// models/Soutenance.php
class Soutenance {
    private $pdo;

    public function __construct() {
        $this->pdo = Db::getInstance();
    }

    public function getByUser($id_user, $role) {
        // selon le rôle
    }

    public function create($data) {
        $sql = "INSERT INTO soutenances (id_memoire, date, heure_debut, heure_fin, salle) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$data['id_memoire'], $data['date'], $data['heure_debut'], $data['heure_fin'], $data['salle']]);
    }

    public function verifierDisponibilite($salle, $date, $debut, $fin) {
        $stmt = $this->pdo->prepare("
            SELECT id FROM soutenances 
            WHERE salle = ? AND date = ? 
            AND (
                (heure_debut <= ? AND heure_fin > ?) OR
                (heure_debut < ? AND heure_fin >= ?)
            )
        ");
        $stmt->execute([$salle, $date, $debut, $debut, $fin, $fin]);
        return $stmt->fetch() ? false : true;
    }
}