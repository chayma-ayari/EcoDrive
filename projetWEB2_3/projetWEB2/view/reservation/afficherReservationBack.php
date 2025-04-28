<?php
require_once '../../model/Reservation.php';
require_once '../../controller/ReservationController.php';
require_once '../../model/config.php';

$reservationC = new ReservationController(); 
$listeReservations = $reservationC->afficherReservation();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Liste des Réservations</title>
    <link href="../backOffice/assets/css/bootstrap.css" rel="stylesheet" />
    <link href="../backOffice/assets/css/font-awesome.css" rel="stylesheet" />
    <link href="../backOffice/assets/css/custom.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .table-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 20px;
            margin: 50px auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .table-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #00b894;
            color: white;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 4px;
        }

        .btn-danger {
            background-color: #ff4d4d;
            color: white;
        }

        .btn-warning {
            background-color: #ffc107;
            color: white;
        }

        .alert {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="adjust-nav">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#"><i class="fa fa-square-o "></i>&nbsp;Gestion des Réservations</a>
                </div>
            </div>
        </div>

        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li><a href="../backOffice/index.php"><i class="fa fa-desktop "></i>Dashboard</a></li>
                    <li><a href="#"><i class="fa fa-table "></i>Utilisateurs</a></li>
                    <li><a href="#"><i class="fa fa-edit "></i>Livraison</a></li>
                    <li><a href="afficherReservation.php" class="active-link"><i class="fa fa-qrcode "></i>Réservations</a></li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">
            <div id="page-inner">
                <div class="table-container">
                    <h2>Liste des <em>Réservations</em></h2>

                    <?php
                    if (isset($_GET['success'])) {
                        echo '<div class="alert alert-success">Réservation supprimée avec succès!</div>';
                    } elseif (isset($_GET['error'])) {
                        echo '<div class="alert alert-danger">Erreur lors de la suppression de la réservation.</div>';
                    }
                    ?>

                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>matricule</th>
                                <th>Date</th>
                                <th>Lieu Départ</th>
                                <th>Lieu Arrivée</th>
                                <th>Type de Transport</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($listeReservations as $reservation) { ?>
                                <tr>
                                    <td><?= $reservation['id_r']; ?></td>
                                    <td><?= $reservation['matricule']; ?></td> 
                                    <td><?= $reservation['date_t']; ?></td>
                                    <td><?= $reservation['lieu_depart']; ?></td>
                                    <td><?= $reservation['lieu_arrive']; ?></td>
                                    <td><?= $reservation['type_t']; ?></td>
                                    <td>
                                        <a href="supprimerReservation.php?id_r=<?= $reservation['id_r']; ?>" class="btn btn-danger btn-sm">Supprimer</a>
                                        <a href="modifierReservation.php?id_r=<?= $reservation['id_r']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="../backOffice/assets/js/jquery-1.10.2.js"></script>
    <script src="../backOffice/assets/js/bootstrap.min.js"></script>
    <script src="../backOffice/assets/js/custom.js"></script>
</body>
</html>
