<?php
// utils/MentionHelper.php
class MentionHelper {
    public static function calculer($note) {
        if ($note >= 16) return "Très bien";
        if ($note >= 14) return "Bien";
        if ($note >= 12) return "Assez bien";
        if ($note >= 10) return "Passable";
        return "Insuffisant";
    }
}