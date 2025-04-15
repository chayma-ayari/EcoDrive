<?php
require_once '../../controller/TransportController.php';
require_once '../../model/Transport.php';

$controller = new TransportController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['matricule'], $_POST['brand'], $_POST['type_t'], $_POST['dispo'], $_POST['etat'])
    ) {
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
    <meta charset="UTF-8">
    <title>Ajouter un transport</title>
    <link href="../../assets/css/bootstrap.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #d4f0f0);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            width: 600px;
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
            transition: background-color 0.3s;
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

        select.form-control {
            cursor: pointer;
        }

        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><i class="fas fa-plus-circle"></i> Ajouter un Transport</h2>
        <form method="post" onsubmit="return validateForm();">
            <div class="form-group mb-3">
                <label for="matricule"><i class="fas fa-id-badge"></i> Matricule :</label>
                <input type="text" class="form-control" id="matricule" name="matricule" required>
                <div id="matriculeError" class="error"></div>
            </div>

            <div class="form-group mb-3">
                <label for="brand"><i class="fas fa-car"></i> Marque :</label>
                <input type="text" class="form-control" id="brand" name="brand" required>
                <div id="brandError" class="error"></div>
            </div>

            <div class="form-group mb-3">
                <label><i class="fas fa-shapes"></i> Type :</label>
                <select class="form-control" name="type_t">
                    <option value="car">🚘 Car</option>
                    <option value="scooter">🛵 Scooter</option>
                    <option value="bike">🚲 Bike</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label><i class="fas fa-check-circle"></i> Disponible :</label>
                <select class="form-control" name="dispo">
                    <option value="1">✅ Oui</option>
                    <option value="0">❌ Non</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label><i class="fas fa-tools"></i> État :</label>
                <select class="form-control" name="etat">
                    <option value="1">👍 Bon</option>
                    <option value="0">👎 Mauvais</option>
                </select>
            </div>

            <button type="submit" class="btn btn-submit">Ajouter</button>
            <a href="../backOffice/index.php" class="btn-back">⬅ Retour à la liste</a>
        </form>
    </div>

    <script>
        function validateForm() {
            let isValid = true;

            // Validate Matricule
            let matricule = document.getElementById("matricule").value;
            let matriculeError = document.getElementById("matriculeError");
            if (matricule === "" || isNaN(matricule) || matricule.length < 4) {
                matriculeError.textContent = "Le matricule doit être un nombre de 4 chiffres minimum.";
                isValid = false;
            } else {
                matriculeError.textContent = "";
            }

            // Validate Brand
            let brand = document.getElementById("brand").value;
            let brandError = document.getElementById("brandError");
            if (brand.trim() === "" || brand.length < 2) {
                brandError.textContent = "La marque doit contenir au moins 2 caractères.";
                isValid = false;
            } else {
                brandError.textContent = "";
            }

            return isValid;
        }
    </script>
</body>
</html>
