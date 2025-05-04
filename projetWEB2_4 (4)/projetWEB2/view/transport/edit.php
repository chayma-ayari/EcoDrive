<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../controller/TransportController.php';
require_once '../../model/Transport.php';

$controller = new TransportController();

// Récupération via id_t (clé primaire)
if (isset($_GET['id_t'])) {
    $id_t = $_GET['id_t'];
    $transport = $controller->getTransportById($id_t);
} else {
    echo "Transport non trouvé !";
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_t'], $_POST['matricule'], $_POST['brand'], $_POST['type_t'], $_POST['dispo'], $_POST['etat'])) {
        $id_t = $_POST['id_t'];
        $matricule = $_POST['matricule'];
        $brand = $_POST['brand'];
        $type_t = $_POST['type_t'];
        $dispo = $_POST['dispo'];
        $etat = $_POST['etat'];

        // Création de l'objet Transport avec l'ID
        $updatedTransport = new Transport($matricule, $brand, $type_t, $dispo, $etat);
        $updatedTransport->setId($id_t);

        // Mise à jour
        $controller->updateTransport($updatedTransport);
        header('Location: ../backOffice/index.php?updated=1');
        exit;
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Transport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.sidebar {
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    width: 220px;
    background-color: #ffffff; /* Change to white */
    padding-top: 60px;
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1); /* Optional: Add a light shadow to separate the sidebar */
}

.sidebar a {
    color: #333; /* Darker text color for better contrast */
    padding: 12px;
    display: block;
    text-decoration: none;
}

.sidebar a:hover {
    background-color: #f8f9fa; /* Light gray background on hover */
}

.content {
    margin-left: 220px;
    padding: 2rem;
    background-color: #ffffff; /* Set the background of the content to white */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Optional: Add shadow to content area */
}

.card {
    background-color: #ffffff; /* Make sure the card background is also white */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Light shadow for the card */
}

.card .form-label {
    font-weight: bold;
}

.card .form-select, .card .form-control {
    border: 1px solid #ddd; /* Lighter border color for input fields */
}
h2 {
    font-size: 2.5rem; /* Increase font size for greater emphasis */
    font-weight: bold; /* Ensure the title stands out with bold weight */
    color: #007bff; /* Use a primary color that contrasts well */
    text-align: center; /* Ensure the title is centered */
    margin-bottom: 30px; /* Provide space below the title */
    padding: 10px 0; /* Add vertical padding for better spacing */
    background-color: #f8f9fa; /* Subtle background color to differentiate the title */
    border-radius: 5px; /* Smooth edges to the background */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow to make the title more prominent */
}
.navbar {
    background-color: transparent !important; /* Makes the navbar background transparent */
}
.navbar {
    padding: 5px 0; /* Reduces the height of the navbar */
    background-color: #007bff; /* Optional: change background color if needed */
}


    </style>
</head>
<body>

<!-- Navbar -->
<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Gestion de Transport</a>
    </div>
</nav>



<!-- Sidebar -->
<div class="sidebar">
    <a href="../backOffice/index.php">🏠 Tableau de bord</a>
    <a href="add.php">➕ Ajouter Transport</a>
    <a href="#">⚙ Paramètres</a>
</div>

<!-- Contenu principal -->
<div class="content">
    <div class="container">
        <h2 class="text-center text-primary mb-4">Modifier un Transport</h2>
        <div class="card shadow-sm p-4">
            <form method="post" action="">

                <!-- Ancien matricule (caché) -->
                <input type="hidden" name="id_t" value="<?= $transport->getId(); ?>">


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
             
...
<input type="submit" class="btn btn-success" value="Enregistrer les modifications">


                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
