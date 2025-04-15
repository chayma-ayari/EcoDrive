<?php
ob_start();
require_once '../../controller/TransportController.php';
require_once '../../model/Transport.php';

$controller = new TransportController();

if (isset($_GET['matricule'])) {
    $matricule = $_GET['matricule'];
    $transport = $controller->getTransportByMatricule($matricule);
} else {
    echo "Transport not found!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['matricule_ancien'], $_POST['matricule'], $_POST['brand'], $_POST['type_t'], $_POST['dispo'], $_POST['etat'])) {
        $oldMatricule = $_POST['matricule_ancien'];
        $matricule = $_POST['matricule'];
        $brand = $_POST['brand'];
        $type_t = $_POST['type_t'];
        $dispo = $_POST['dispo'];
        $etat = $_POST['etat'];

        $updatedTransport = new Transport($matricule, $brand, $type_t, $dispo, $etat);

        // ici on passe l'ancien matricule comme référence pour la mise à jour
        $controller->updateTransport($updatedTransport, $oldMatricule);

        header('Location: ../backOffice/index.php?updated=1');
        exit;
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier un Transport</title>
    <link href="../../assets/css/bootstrap.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="width: 500px;">
        <h3 class="text-center text-primary mb-4">Modifier Transport</h3>
        <form method="post" action="" class="form">

            <!-- Ancien matricule (caché) -->
            <input type="hidden" name="matricule_ancien" value="<?= $transport->getMatricule(); ?>">

            <div class="mb-3">
                <label class="form-label fw-bold">Matricule</label>
                <input type="text" class="form-control" name="matricule" value="<?= $transport->getMatricule(); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Marque</label>
                <input type="text" class="form-control" name="brand" value="<?= $transport->getBrand(); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Type</label>
                <select class="form-select" name="type_t">
                    <option value="car" <?= $transport->getType_t() == 'car' ? 'selected' : ''; ?>>Car</option>
                    <option value="scooter" <?= $transport->getType_t() == 'scooter' ? 'selected' : ''; ?>>Scooter</option>
                    <option value="bike" <?= $transport->getType_t() == 'bike' ? 'selected' : ''; ?>>Bike</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Disponible</label>
                <select class="form-select" name="dispo">
                    <option value="1" <?= $transport->getDispo() ? 'selected' : ''; ?>>Oui</option>
                    <option value="0" <?= !$transport->getDispo() ? 'selected' : ''; ?>>Non</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">État</label>
                <select class="form-select" name="etat">
                    <option value="1" <?= $transport->getEtat() ? 'selected' : ''; ?>>Bon</option>
                    <option value="0" <?= !$transport->getEtat() ? 'selected' : ''; ?>>Mauvais</option>
                </select>
            </div>

            <div class="d-grid">
                <input type="submit" class="btn btn-success" value="Enregistrer les modifications">
            </div>
        </form>
    </div>

    <!-- Bootstrap 5 (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</body>

</html>
