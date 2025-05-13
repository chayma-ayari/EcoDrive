<?php
require_once '../model/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Initialize PDO connection
        $pdo = config::getConnexion();

        // Get POST data
        $date_t = $_POST['date_t'];
        $type_t = $_POST['type_t'];

        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare("SELECT r.id
                               FROM reservation r
                               JOIN transport t ON r.id_t = t.id_t
                               WHERE r.date_t = :date_t AND t.type_t = :type_t
                               LIMIT 1");
        $stmt->execute(['date_t' => $date_t, 'type_t' => $type_t]);

        // Check if any result was found
        echo $stmt->fetch() ? 'reserved' : 'available';

    } catch (PDOException $e) {
        // Handle database connection or query error
        echo '❌ Erreur de communication avec le serveur : ' . $e->getMessage();
    }
}
?>
