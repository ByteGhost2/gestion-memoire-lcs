<?php

class Mailer {
    /**
     * Envoie un email
     * @param string $to Destinataire
     * @param string $subject Sujet
     * @param string $body Corps du message (HTML)
     * @return bool Succès
     */
    public static function send($to, $subject, $body) {
        // Si PHPMailer est installé, l'utiliser
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return self::sendWithPHPMailer($to, $subject, $body);
        }
        
        // Sinon, utiliser la fonction mail() avec vérification de configuration
        // En développement local, on simule l'envoi pour éviter l'erreur
        if (self::isLocalEnvironment()) {
            // Simuler un envoi réussi en mode développement
            error_log("Email simulé (mode développement) : $subject -> $to");
            return true;
        }
        
        // En production, tenter l'envoi réel
        return self::sendWithMail($to, $subject, $body);
    }

    private static function sendWithPHPMailer($to, $subject, $body) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // Configuration SMTP (à adapter)
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASS;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;

            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
            return false;
        }
    }

    private static function sendWithMail($to, $subject, $body) {
        $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
        $headers .= "Reply-To: " . MAIL_FROM . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        return mail($to, $subject, $body, $headers);
    }

    private static function isLocalEnvironment() {
        // Vérifier si on est en local (XAMPP, WAMP, etc.)
        $host = $_SERVER['SERVER_NAME'] ?? '';
        return in_array($host, ['localhost', '127.0.0.1', '::1']) || strpos($host, '.test') !== false;
    }
}