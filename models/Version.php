<?php
// models/Version.php
class Version {
    private $pdo;

    public function __construct() {
        $this->pdo = Db::getInstance();
    }

    public function getByMemoire($id_memoire) {
        $stmt = $this->pdo->prepare("SELECT * FROM versions WHERE id_memoire = ? ORDER BY numero DESC");
        $stmt->execute([$id_memoire]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($id_memoire, $fichier, $numero) {
        $stmt = $this->pdo->prepare("INSERT INTO versions (id_memoire, fichier, numero) VALUES (?, ?, ?)");
        return $stmt->execute([$id_memoire, $fichier, $numero]);
    }

    public function getLastVersion($id_memoire) {
        $stmt = $this->pdo->prepare("SELECT MAX(numero) as max FROM versions WHERE id_memoire = ?");
        $stmt->execute([$id_memoire]);
        $row = $stmt->fetch();
        return $row['max'] ?? 0;
    }
}