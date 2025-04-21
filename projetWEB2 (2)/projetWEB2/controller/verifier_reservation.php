<?php
require_once '../model/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = config::getConnexion();
    $date_t = $_POST['date_t'];
    $type_t = $_POST['type_t'];

    $stmt = $pdo->prepare("SELECT r.id
                           FROM reservation r
                           JOIN transport t ON r.id_t = t.matricule
                           WHERE r.date_t = :date_t AND t.type_t = :type_t
                           LIMIT 1");
    $stmt->execute(['date_t' => $date_t, 'type_t' => $type_t]);

    echo $stmt->fetch() ? 'reserved' : 'available';
}
?>