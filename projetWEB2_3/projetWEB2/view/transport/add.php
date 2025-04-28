<?php
require_once '../../controller/TransportController.php';
require_once '../../model/Transport.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$controller = new TransportController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['matricule'], $_POST['brand'], $_POST['type_t'], $_POST['dispo'], $_POST['etat'])) {
        $matricule = intval($_POST['matricule']);
        $brand = $_POST['brand'];
        $type_t = $_POST['type_t'];
        $dispo = intval($_POST['dispo']);
        $etat = intval($_POST['etat']);

        $newTransport = new Transport($matricule, $brand, $type_t, $dispo, $etat);
        $controller->addTransport($newTransport);

        header('Location: ../backOffice/index.php?added=1');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Ajouter Transport</title>
    <link href="../backOffice/assets/css/bootstrap.css" rel="stylesheet" />
    <link href="../backOffice/assets/css/font-awesome.css" rel="stylesheet" />
    <link href="../backOffice/assets/css/custom.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            width: 600px;
            margin: 50px auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .form-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group label {
            font-weight: 600;
            color: #555;
        }

        .form-control {
            height: 50px;
            border-radius: 12px;
            font-size: 17px;
            padding: 10px 15px;
        }

        .btn-submit {
            background-color: #00b894;
            color: white;
            font-size: 18px;
            font-weight: bold;
            padding: 12px;
            border-radius: 12px;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #019875;
        }

        .btn-back {
            background-color: #ffd54f;
            color: #333;
            font-weight: bold;
            border-radius: 12px;
            padding: 10px;
            width: 100%;
            text-align: center;
            display: block;
            margin-top: 15px;
            text-decoration: none;
        }

        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="adjust-nav">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#"><i class="fa fa-square-o "></i>&nbsp;Gestion des Transports</a>
                </div>
            </div>
        </div>

        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li><a href="../backOffice/index.php"><i class="fa fa-desktop "></i>Transports</a></li>
                    <li><a href="#"><i class="fa fa-table "></i>Utilisateurs</a></li>
                    <li><a href="#"><i class="fa fa-edit "></i>Livraison</a></li>
                    <li><a href="#"><i class="fa fa-qrcode "></i>Réservations</a></li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">
            <div id="page-inner">
        
            <div class="form-container">
    <h2><i class="fas fa-plus-circle"></i> Ajouter un Transport</h2>

    <!-- ✅ UNE SEULE balise form -->
    <form method="POST" action="add.php">

    <!-- <form onsubmit="return validateForm()" novalidate> -->

        <div class="form-group mb-3">
            <label for="matricule">Matricule :</label>
            <input type="text" class="form-control" id="matricule" name="matricule" required>
            <div id="matriculeError" class="error"></div>
        </div>

        <div class="form-group mb-3">
            <label for="brand">Marque :</label>
            <input type="text" class="form-control" id="brand" name="brand" required>
            <div id="brandError" class="error"></div>
        </div>

        <div class="form-group mb-3">
            <label>Type :</label>
            <select class="form-control" name="type_t" id="type_t">
                <option value="">-- Sélectionner --</option>
                <option value="car">Car</option>
                <option value="scooter">Scooter</option>
                <option value="bike">Bike</option>
            </select>
            <div id="typeError" class="error"></div>
        </div>

        <div class="form-group mb-3">
            <label>Disponible :</label>
            <select class="form-control" name="dispo" id="dispo">
                <option value="">-- Sélectionner --</option>
                <option value="1">Oui</option>
                <option value="0">Non</option>
            </select>
            <div id="dispoError" class="error"></div>
        </div>

        <div class="form-group mb-4">
            <label>État :</label>
            <select class="form-control" name="etat" id="etat">
                <option value="">-- Sélectionner --</option>
                <option value="1">Bon</option>
                <option value="0">Mauvais</option>
            </select>
            <div id="etatError" class="error"></div>
        </div>

        <button type="submit" class="btn btn-submit">Ajouter</button>
        <a href="../backOffice/index.php" class="btn-back">⬅ Retour</a>
    </form>
</div>

                  
            </div>
        </div>
    </div>
    <script>
    function validateForm() {
        let isValid = true;
        let messages = [];

        // Champ : Matricule
        let matricule = document.getElementById("matricule");
        let matriculeError = document.getElementById("matriculeError");
        if (matricule.value === "" || isNaN(matricule.value) || parseInt(matricule.value) <= 0 || matricule.value.length < 4) {
            matriculeError.textContent = "Le matricule doit être un nombre de 4 chiffres minimum.";
            messages.push("→ Matricule invalide");
            isValid = false;
        } else {
            matriculeError.textContent = "";
        }

        // Champ : Marque
        let brand = document.getElementById("brand");
        let brandError = document.getElementById("brandError");
        let brandRegex = /^[a-zA-Z\s\-]+$/;
        if (brand.value.trim().length < 2) {
            brandError.textContent = "La marque doit contenir au moins 2 caractères.";
            messages.push("→ Marque trop courte");
            isValid = false;
        } else if (!brandRegex.test(brand.value.trim())) {
            brandError.textContent = "La marque ne doit contenir que des lettres, espaces ou tirets.";
            messages.push("→ Marque invalide");
            isValid = false;
        } else {
            brandError.textContent = "";
        }

        // Champ : Type
        let type = document.getElementById("type_t");
        let typeError = document.getElementById("typeError");
        if (type.value === "") {
            typeError.textContent = "Veuillez sélectionner un type.";
            messages.push("→ Type non sélectionné");
            isValid = false;
        } else {
            typeError.textContent = "";
        }

        // Champ : Disponible
        let dispo = document.getElementById("dispo");
        let dispoError = document.getElementById("dispoError");
        if (dispo.value !== "0" && dispo.value !== "1") {
            dispoError.textContent = "Veuillez indiquer la disponibilité.";
            messages.push("→ Disponibilité non sélectionnée");
            isValid = false;
        } else {
            dispoError.textContent = "";
        }

        // Champ : État
        let etat = document.getElementById("etat");
        let etatError = document.getElementById("etatError");
        if (etat.value !== "0" && etat.value !== "1") {
            etatError.textContent = "Veuillez indiquer l’état.";
            messages.push("→ État non sélectionné");
            isValid = false;
        } else {
            etatError.textContent = "";
        }

        // Si erreurs → alert globale
       

        return isValid;
    }
</script>


    <!-- JS -->
    <script src="../backOffice/assets/js/jquery-1.10.2.js"></script>
    <script src="../backOffice/assets/js/bootstrap.min.js"></script>
    <script src="../backOffice/assets/js/custom.js"></script>
    
</body>
</html>
