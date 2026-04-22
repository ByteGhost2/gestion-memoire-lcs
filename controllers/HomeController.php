<?php
// controllers/HomeController.php
class HomeController {
    public function index() {
        $pdo = Db::getInstance();
        $stats = [];

        try {
            // Compter les mémoires
            $stats['memoires'] = $pdo->query("SELECT COUNT(*) FROM memoires")->fetchColumn();
            // Compter les étudiants
            $stats['etudiants'] = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='etudiant'")->fetchColumn();
            // Compter les encadreurs
            $stats['encadreurs'] = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='encadreur'")->fetchColumn();
            // Compter les soutenances
            $stats['soutenances'] = $pdo->query("SELECT COUNT(*) FROM soutenances")->fetchColumn();
        } catch (Exception $e) {
            // En cas d'absence de tables ou de base non installée
            $stats = ['memoires' => 0, 'etudiants' => 0, 'encadreurs' => 0, 'soutenances' => 0];
        }

        require_once 'views/home/index.php';
    }
}