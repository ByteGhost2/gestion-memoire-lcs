<?php
// controllers/PageController.php
class PageController {
    public function apropos() {
    $pdo = Db::getInstance();
    $stats = [];

    try {
        // Au lieu d'années d'existence, on met l'année de création ou 1
        $stats['annees'] = 1; // ou date('Y') - 2026 ? On met 1 car plateforme créée cette année
        $stats['memoires'] = $pdo->query("SELECT COUNT(*) FROM memoires")->fetchColumn();
        $stats['encadreurs'] = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='encadreur'")->fetchColumn();
        $stats['etudiants'] = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='etudiant'")->fetchColumn();
    } catch (Exception $e) {
        $stats = ['annees' => 1, 'memoires' => 0, 'encadreurs' => 0, 'etudiants' => 0];
    }

    require_once 'views/pages/apropos.php';
}

    public function contact() {
    $settingsFile = __DIR__ . '/../config/settings.json';
    $settings = [];
    if (file_exists($settingsFile)) {
        $settings = json_decode(file_get_contents($settingsFile), true);
    }
    $adminEmail = $settings['email_contact'] ?? 'contact@iucs.bj';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $sujet = trim($_POST['sujet'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];
        if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
            $errors[] = "Tous les champs sont obligatoires.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse email invalide.";
        }

        if (empty($errors)) {
            $to = $adminEmail;
            $headers = "From: $email\r\n";
            $headers .= "Reply-To: $email\r\n";
            $corps = "Nom: $nom\nEmail: $email\nSujet: $sujet\nMessage:\n$message\n";
            
            if (class_exists('Mailer')) {
                $bodyHtml = "<p><strong>Nom:</strong> $nom</p>
                             <p><strong>Email:</strong> $email</p>
                             <p><strong>Sujet:</strong> $sujet</p>
                             <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";
                $sent = Mailer::send($to, $sujet, $bodyHtml);
            } else {
                $sent = mail($to, $sujet, $corps, $headers);
            }
            
            if ($sent) {
                $success = "Message envoyé avec succès.";
            } else {
                $errors[] = "Erreur d'envoi.";
            }
        }
    }
    
    require_once 'views/pages/contact.php';
}
}