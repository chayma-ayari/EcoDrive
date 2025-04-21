<?php
require_once '../../model/config.php';
require_once '../../model/Reservation.php';
require_once '../../controller/ReservationController.php';
require_once '../../model/Transport.php';



$pdo = config::getConnexion();
$success = false;
$errorMessage = null;

// Fetch transport types
$stmt = $pdo->query("SELECT DISTINCT type_t FROM transport WHERE dispo = 1 AND etat = 1");
$types = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $date_t = $_POST['date_t'];
        $lieu_depart = $_POST['lieu_depart'];
        $lieu_arrive = $_POST['lieu_arrive'];
        $type_t = $_POST['type_t'];

        // Étape 1 : récupérer tous les transports valides de ce type
        $stmt1 = $pdo->prepare("SELECT matricule FROM transport WHERE type_t = :type_t AND dispo = 1 AND etat = 1");
        $stmt1->execute(['type_t' => $type_t]);
        $transports = $stmt1->fetchAll(PDO::FETCH_COLUMN);

        $id_t_dispo = null;

        // Étape 2 : vérifier s’il y a un transport non encore réservé
        foreach ($transports as $matricule) {
            $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM reservation WHERE id_t = :id_t");
            $stmt2->execute(['id_t' => $matricule]);
            $isAlreadyReserved = $stmt2->fetchColumn() > 0;

            if (!$isAlreadyReserved) {
                $id_t_dispo = $matricule;
                break;
            }
        }

        if ($id_t_dispo) {
            // Étape 3 : insérer la réservation
            $stmt3 = $pdo->prepare("INSERT INTO reservation (date_t, lieu_depart, lieu_arrive, id_t)
                                    VALUES (:date_t, :lieu_depart, :lieu_arrive, :id_t)");
            $stmt3->execute([
                'date_t' => $date_t,
                'lieu_depart' => $lieu_depart,
                'lieu_arrive' => $lieu_arrive,
                'id_t' => $id_t_dispo
            ]);

            $success = true;
        } else {
            $errorMessage = "Erreur : tous les moyens de transport de ce type sont déjà réservés.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Erreur SQL : " . $e->getMessage();
    }
}
?>

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Request a Reservation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {

          
    


            background: #fff;
            font-family: 'Poppins', sans-serif;
        }

        .callback-form {
            padding: 60px 0;
            background-color: #a4cc2c;
            border-radius: 8px;
            margin: 50px auto;
            max-width: 95%;
        }

        .section-heading {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-heading h2 {
            font-size: 32px;
            font-weight: 700;
        }

        .section-heading em {
            color: #4f8a10;
        }

        .section-heading span {
            font-size: 16px;
            color: #555;
        }

        .form-control {
            border-radius: 25px;
            padding: 14px 24px;
            border: none;
            margin-bottom: 20px;
            width: 100%;
            font-size: 16px;
            line-height: 1.5;
            min-height: 56px;
        }

        select.form-control {
            background-color: #fff;
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
        }

        .border-button {
            background-color: #fff;
            color: #a4cc2c;
            border: 2px solid #fff;
            border-radius: 25px;
            padding: 10px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .border-button:hover {
            background-color: #88b321;
            color: #fff;
            border: 2px solid #88b321;
        }

        .success-msg {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .error-msg {
            text-align: center;
            color: red;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .contact-form .row > div {
            padding: 0 15px;
            margin-bottom: 20px;
        }

        @media (min-width: 768px) {
            .contact-form .row > div {
                flex: 1 1 25%;
                max-width: 25%;
            }
            .contact-form .col-lg-12 {
                max-width: 100%;
                flex: 1 1 100%;
            }
        }

        @media (max-width: 767px) {
            .contact-form .row > div {
                max-width: 100%;
            }
        }
    </style>
    <header class="header-area header-sticky bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav d-flex justify-content-between align-items-center py-3">
                    <!-- Logo -->
                    <a href="../../index.html" class="logo text-success font-weight-bold" style="font-size: 28px;">
                        Eco Drive
                    </a>
                    <!-- Menu -->
                    <ul class="nav">
                        <li class="nav-item"><a href="../../index.html" class="nav-link">Accueil</a></li>
                        <li class="nav-item"><a href="../frontOffice/template/services.php" class="nav-link">Services</a></li>


                        <li class="nav-item"><a href="#" class="nav-link">Contact</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Réclamations</a></li>
                        <li class="nav-item"><a href="afficherReservation.php" class="nav-link active">Mes Réservations</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>

</head>
<body>

<div class="callback-form callback-services">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="section-heading">
                    <h2>Request a <em>Reservation</em></h2>
                    <span>Remplissez les détails pour réserver votre transport</span>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="col-md-12">
                    <div class="success-msg">Réservation ajoutée avec succès !</div>
                </div>
            <?php elseif (!empty($errorMessage)): ?>
                <div class="col-md-12">
                    <div class="error-msg"><?= htmlspecialchars($errorMessage) ?></div>
                </div>
            <?php endif; ?>

            <div class="col-md-12">
                <form id="reservation-form" action="" method="post" novalidate>
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <input name="date_t" type="date" class="form-control" id="date_t" required>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <select name="lieu_depart" class="form-control" required>
                                <option value="">Lieu de départ</option>
                                <option value="tunis">Tunis</option>
                                <option value="bardou">Bardou</option>
                                <option value="marsa">Marsa</option>
                                <option value="ghazela">Ghazela</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <select name="lieu_arrive" class="form-control" required>
                                <option value="">Lieu d’arrivée</option>
                                <option value="tunis">Tunis</option>
                                <option value="bardou">Bardou</option>
                                <option value="marsa">Marsa</option>
                                <option value="ghazela">Ghazela</option>
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <select name="type_t" class="form-control" required>
                                <option value="">Moyen de transport</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-12 text-center">
                            <button type="submit" class="border-button">Réserver</button>
                        </div>
                    </div>
                </form>
                <div class="col-lg-12 text-center mt-4">
    <a href="afficherReservation.php" class="btn btn-lg btn-success" style="
        background-color: #4CAF50;
        color: white;
        padding: 15px 40px;
        font-size: 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        transition: background-color 0.3s ease;
    " onmouseover="this.style.backgroundColor='#45a049'" onmouseout="this.style.backgroundColor='#4CAF50'">
        Mes Réservations
    </a>
</div>

            </div>

        </div>
    </div>
</div>

<script src="../../public/reservation.js"></script>
</body>
</html>
