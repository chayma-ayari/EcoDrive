
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once  '../../model/config.php';
require_once '../../controller/TransportController.php';
require_once '../../model/Transport.php';












if (isset($_GET['matricule'])) {
    $controller = new TransportController();
    $controller->deleteTransport($_GET['matricule']);
}
header('Location: ../backOffice/index.php?deleted=1');

// ✅ REDIRECTION VERS LA PAGE DE LISTE AVEC DESIGN

exit;
?>

