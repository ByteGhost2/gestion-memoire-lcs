<?php
class LogHelper {
    public static function log($action, $details = null) {
        $pdo = Db::getInstance();
        $user_id = $_SESSION['user']['id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO logs (utilisateur_id, action, details, ip) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $action, $details, $ip]);
    }
}