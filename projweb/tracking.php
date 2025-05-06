<?php
require_once 'config.php';
require_once 'model/livraisons.php';
require_once 'model/colis.php';

$delivery_info = null;
$error = null;

if (isset($_GET['id'])) {
    try {
        $db = config::getConnexion();
        $id = $_GET['id'];
        $query = "SELECT l.*, c.* FROM livraisons l 
                 LEFT JOIN colis c ON l.id_liv = c.id_colis 
                 WHERE l.id_liv = :id";
        $stmt = $db->prepare($query);
        $stmt->execute(['id' => $id]);
        $delivery_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$delivery_info) {
            $error = "Delivery not found";
        }
    } catch (Exception $e) {
        $error = "Error retrieving delivery information";
    }
} else {
    $error = "No tracking ID provided";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Delivery Tracking</h2>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif ($delivery_info): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Delivery Details</h4>
                            <p><strong>Delivery ID:</strong> <?php echo htmlspecialchars($delivery_info['id_liv']); ?></p>
                            <p><strong>Delivery Date:</strong> <?php echo htmlspecialchars($delivery_info['date_liv']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($delivery_info['lieu']); ?></p>
                            <p><strong>Contact:</strong> <?php echo htmlspecialchars($delivery_info['tel']); ?></p>
                            <p><strong>Transport:</strong> <?php echo htmlspecialchars($delivery_info['moy_transport']); ?></p>
                        </div>
                        <?php if (isset($delivery_info['id_colis'])): ?>
                            <div class="col-md-6">
                                <h4>Package Details</h4>
                                <p><strong>Weight:</strong> <?php echo htmlspecialchars($delivery_info['poids']); ?> kg</p>
                                <p><strong>Content:</strong> <?php echo htmlspecialchars($delivery_info['contenu']); ?></p>
                                <p><strong>Status:</strong> <?php echo htmlspecialchars($delivery_info['statut']); ?></p>
                                <p><strong>Send Date:</strong> <?php echo htmlspecialchars($delivery_info['date_envoi']); ?></p>
                                <p><strong>Expected Delivery:</strong> <?php echo htmlspecialchars($delivery_info['date_livraison']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>