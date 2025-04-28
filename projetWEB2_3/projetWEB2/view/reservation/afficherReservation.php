<?php
// Inclure le fichier de connexion à la base de données
require_once '../../model/Reservation.php';
require_once '../../controller/ReservationController.php';
require_once '../../model/config.php';

$reservationC = new ReservationController(); 
$listeReservations = $reservationC->afficherReservation();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="TemplateMo">
  <link href="http://localhost/projetWEB2/view/frontOffice/template/assets/css/fontawesome.css" rel="stylesheet">
<link href="http://localhost/projetWEB2/view/frontOffice/template/assets/css/templatemo-finance-business.css" rel="stylesheet">
<link href="http://localhost/projetWEB2/view/frontOffice/template/assets/css/owl.css" rel="stylesheet">
<link href="http://localhost/projetWEB2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <title>Liste des Réservations</title>
  <style>
    /* Centrer le tableau */
    .table-container {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 50px;
    }

    table {
      width: 80%;
      margin: 0 auto;
      border-collapse: collapse;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    thead {
      background-color: #4CAF50;
      color: white;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border: 1px solid #ddd;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    tr:hover {
      background-color: #ddd;
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

    .btn-danger:hover {
      background-color: #ff3333;
    }

    .btn-warning {
      background-color: #ffcc00;
      color: white;
    }

    .btn-warning:hover {
      background-color: #e6b800;
    }
  </style>
</head>
<body>

<!-- HEADER / NAVBAR -->
<div class="sub-header">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-xs-12">
        <ul class="left-info">
          <li><a href="#"><i class="fa fa-clock-o"></i>Mon-Fri 09:00-17:00</a></li>
          <li><a href="#"><i class="fa fa-phone"></i>25-043-450</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <ul class="right-icons">
          <li><a href="#"><i class="fa fa-facebook"></i></a></li>
          <li><a href="#"><i class="fa fa-twitter"></i></a></li>
          <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
          <li><a href="#"><i class="fa fa-behance"></i></a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<header class="">
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="#"><h2>Finance Business</h2></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
          <li class="nav-item"><a class="nav-link" href="formreservation.php">Réserver</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<!-- AFFICHAGE DES RESERVATIONS -->
<div class="container mt-5 mb-5">
  <div class="section-heading">
    <h2>Liste des <em>Réservations</em></h2>
    <span>Affichage des réservations disponibles</span>
  </div>
  <?php
  if (isset($_GET['success'])) {
      echo '<div class="alert alert-success">Réservation supprimée avec succès!</div>';
  } elseif (isset($_GET['error'])) {
      echo '<div class="alert alert-danger">Erreur lors de la suppression de la réservation.</div>';
  }
  ?>

  <div class="table-container">
    <table class="table table-bordered table-striped table-hover">
      <thead class="thead-dark">
        <tr>
          <th>ID</th>
          <th>Date</th>
          <th>Lieu Départ</th>
          <th>Lieu Arrivée</th>
          <th>Type de Transport</th> <!-- ✅ MODIFIÉ -->
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($listeReservations as $reservation) { ?>
          <tr>
            <td><?= $reservation['id_r']; ?></td>
            <td><?= $reservation['date_t']; ?></td>
            <td><?= $reservation['lieu_depart']; ?></td>
            <td><?= $reservation['lieu_arrive']; ?></td>
            <td><?= $reservation['type_t']; ?></td> <!-- ✅ MODIFIÉ -->
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
<!-- Retour Button with Enhanced Design -->
<!-- Retour Button with Enhanced Design -->
<div class="button-container">
  <a href="ajouterReservation.php" class="btn btn-return">Retour à la réservation</a>
</div>

<!-- Custom Button Styling -->
<style>
  /* Center the button in the middle of the page, but lower */
  .button-container {
    display: flex;
    justify-content: center;  /* Horizontally center */
    align-items: flex-start;  /* Align to the top */
    height: 100vh;  /* Full viewport height */
    margin-top: 150px;  /* Space from the top to lower the button */
  }

  .btn-return {
    padding: 12px 24px;  /* Larger padding for a bigger button */
    font-size: 18px;  /* Increase font size */
    font-weight: bold;  /* Make the text bold */
    background: linear-gradient(145deg, #4CAF50, #388E3C);  /* Green gradient background */
    color: white;  /* White text */
    border: none;
    border-radius: 50px;  /* Rounded corners */
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);  /* Subtle shadow effect */
    transition: all 0.3s ease;  /* Smooth transition on hover */
  }

  .btn-return:hover {
    background: linear-gradient(145deg, #388E3C, #4CAF50);  /* Reverse gradient on hover */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);  /* Stronger shadow on hover */
    transform: scale(1.05);  /* Slightly enlarge the button on hover */
  }

  .btn-return:focus {
    outline: none;  /* Remove focus outline */
  }
</style>



<!-- SCRIPTS -->
<script src="../../vendor/jquery/jquery.min.js"></script>
<script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/custom.js"></script>
<script src="../../assets/js/owl.js"></script>
<script src="../../assets/js/slick.js"></script>
<script src="../../assets/js/isotope.js"></script>
<script src="../../assets/js/accordions.js"></script>

</body>
</html>
