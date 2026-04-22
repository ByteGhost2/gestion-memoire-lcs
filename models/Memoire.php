<?php
// models/Memoire.php
class Memoire {
    private $pdo;

    public function __construct() {
        $this->pdo = Db::getInstance();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT m.*, u.nom, u.prenom FROM memoires m JOIN utilisateurs u ON m.id_etudiant = u.id ORDER BY m.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByEtudiant($id_etudiant) {
        $stmt = $this->pdo->prepare("SELECT * FROM memoires WHERE id_etudiant = ? ORDER BY created_at DESC");
        $stmt->execute([$id_etudiant]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByEncadreur($id_encadreur) {
        $stmt = $this->pdo->prepare("SELECT m.*, u.nom, u.prenom FROM memoires m JOIN utilisateurs u ON m.id_etudiant = u.id WHERE m.id_encadreur = ? ORDER BY m.updated_at DESC");
        $stmt->execute([$id_encadreur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT m.*, u.nom, u.prenom, u.email FROM memoires m JOIN utilisateurs u ON m.id_etudiant = u.id WHERE m.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO memoires (titre, resume, mots_cles, id_etudiant, statut, date_soumission) VALUES (?, ?, ?, ?, 'soumis', CURDATE())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$data['titre'], $data['resume'], $data['mots_cles'], $data['id_etudiant']]);
        return $this->pdo->lastInsertId();
    }

    public function updateStatut($id, $statut) {
        $stmt = $this->pdo->prepare("UPDATE memoires SET statut = ? WHERE id = ?");
        return $stmt->execute([$statut, $id]);
    }

    public function assignerEncadreur($id_memoire, $id_encadreur) {
        $stmt = $this->pdo->prepare("UPDATE memoires SET id_encadreur = ? WHERE id = ?");
        return $stmt->execute([$id_encadreur, $id_memoire]);
    }
    public function getEtudiants($id_memoire) {
    $pdo = Db::getInstance();
    $stmt = $pdo->prepare("
        SELECT u.* FROM memoire_etudiants me
        JOIN utilisateurs u ON me.id_etudiant = u.id
        WHERE me.id_memoire = ?
    ");
    $stmt->execute([$id_memoire]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function ajouterEtudiant($id_memoire, $id_etudiant, $role = 'membre') {
    $pdo = Db::getInstance();
    $stmt = $pdo->prepare("INSERT INTO memoire_etudiants (id_memoire, id_etudiant, role) VALUES (?, ?, ?)");
    return $stmt->execute([$id_memoire, $id_etudiant, $role]);
}

public function retirerEtudiant($id_memoire, $id_etudiant) {
    $pdo = Db::getInstance();
    $stmt = $pdo->prepare("DELETE FROM memoire_etudiants WHERE id_memoire = ? AND id_etudiant = ?");
    return $stmt->execute([$id_memoire, $id_etudiant]);
}

}