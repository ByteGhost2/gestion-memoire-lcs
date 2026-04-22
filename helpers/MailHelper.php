<?php
// helpers/MailHelper.php
class MailHelper {
    public static function envoyer($destinataire, $sujet, $messageHtml) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: e-Memoire LCS <no-reply@ememoire-lcs.iucs.bj>" . "\r\n";

        return mail($destinataire, $sujet, $messageHtml, $headers);
    }

    public static function notifierNouveauMessage($destinataire, $expediteur) {
        $sujet = "Nouveau message sur e-Memoire LCS";
        $message = "<p>Bonjour,</p>";
        $message .= "<p>Vous avez reçu un nouveau message de " . htmlspecialchars($expediteur) . " sur la plateforme.</p>";
        $message .= "<p><a href='" . BASE_URL . "/messages'>Cliquez ici pour le lire.</a></p>";
        return self::envoyer($destinataire, $sujet, $message);
    }

    public static function notifierSoutenancePlanifiee($destinataire, $etudiant, $date, $heure, $salle) {
        $sujet = "Soutenance planifiée";
        $message = "<p>Bonjour,</p>";
        $message .= "<p>La soutenance de mémoire de $etudiant a été planifiée.</p>";
        $message .= "<p><strong>Date :</strong> $date</p>";
        $message .= "<p><strong>Heure :</strong> $heure</p>";
        $message .= "<p><strong>Salle :</strong> $salle</p>";
        return self::envoyer($destinataire, $sujet, $message);
    }

    // Ajouter d'autres types de notification...
}