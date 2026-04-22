<?php
// controllers/NotificationController.php
class NotificationController {
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE id_utilisateur = ? ORDER BY date_creation DESC");
        $stmt->execute([$_SESSION['user']['id']]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/notification/index.php';
    }

    public function markAsRead() {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? 0;
            if ($id) {
                $pdo = Db::getInstance();
                $stmt = $pdo->prepare("UPDATE notifications SET lu = TRUE WHERE id = ? AND id_utilisateur = ?");
                $stmt->execute([$id, $_SESSION['user']['id']]);
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false]);
        exit;
    }

    public function markAsReadGet($id) {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $pdo->prepare("UPDATE notifications SET lu = TRUE WHERE id = ? AND id_utilisateur = ?")->execute([$id, $_SESSION['user']['id']]);
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/notification');
        exit;
    }

    public function markAllRead() {
        $this->checkAuth();
        $pdo = Db::getInstance();
        $pdo->prepare("UPDATE notifications SET lu = TRUE WHERE id_utilisateur = ?")->execute([$_SESSION['user']['id']]);
        header('Location: ' . BASE_URL . '/notification');
        exit;
    }
}