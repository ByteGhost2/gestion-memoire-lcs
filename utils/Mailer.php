<?php
// utils/Mailer.php

class Mailer {
    /**
     * Envoie un email
     * @param string $to Destinataire
     * @param string $subject Sujet
     * @param string $body Corps du message (HTML)
     * @return bool Succès
     */
    public static function send($to, $subject, $body) {
        // Vérifier si PHPMailer est disponible
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return self::sendWithPHPMailer($to, $subject, $body);
        } else {
            return self::sendWithMail($to, $subject, $body);
        }
    }

    private static function sendWithPHPMailer($to, $subject, $body) {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
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
            error_log("Erreur d'envoi email : " . $mail->ErrorInfo);
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
}