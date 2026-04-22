<?php
// models/User.php
class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Db::getInstance();
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, filiere) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$data['nom'], $data['prenom'], $data['email'], $data['mot_de_passe'], $data['role'], $data['filiere'] ?? null]);
    }

    public function update($id, $data) {
        // Mise à jour partielle
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}