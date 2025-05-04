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

    /* Popup Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
      animation: fadeIn 0.5s;
    }

    .modal-content {
      background-color: white;
      margin: 15% auto;
      padding: 20px;
      border-radius: 5px;
      width: 80%;
      max-width: 400px;
    }

    .stars {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    .stars i {
      font-size: 40px;
      color: #ccc;
      cursor: pointer;
      margin: 0 5px;
      transition: color 0.3s;
    }

    .stars i.selected {
      color: #f39c12;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    /* Custom Button Styling */
    .button-container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      height: 100vh;
      margin-top: 150px;
    }

    .btn-return {
      padding: 12px 24px;
      font-size: 18px;
      font-weight: bold;
      background: linear-gradient(145deg, #4CAF50, #388E3C);
      color: white;
      border: none;
      border-radius: 50px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
    }

    .btn-return:hover {
      background: linear-gradient(145deg, #388E3C, #4CAF50);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      transform: scale(1.05);
    }

    .btn-return:focus {
      outline: none;
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
          <th>Type de Transport</th>
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

<!-- Modal de Notation -->
<div id="ratingModal" class="modal">
  <div class="modal-content">
    <h2>Évaluez votre expérience</h2>
    <div class="stars">
      <i class="fa fa-star" data-value="1"></i>
      <i class="fa fa-star" data-value="2"></i>
      <i class="fa fa-star" data-value="3"></i>
      <i class="fa fa-star" data-value="4"></i>
      <i class="fa fa-star" data-value="5"></i>
    </div>
    <button id="validateRating" class="btn btn-success">Valider</button>
    <button id="cancelRating" class="btn btn-danger">Annuler</button>
  </div>
</div>

<!-- Scripts -->
<script src="../../vendor/jquery/jquery.min.js"></script>
<script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
  // Afficher la pop-up à chaque chargement de page
  document.getElementById('ratingModal').style.display = 'block';

  // Gestion des étoiles
  const stars = document.querySelectorAll('.stars i');
  let selectedRating = 0;

  stars.forEach(star => {
    star.addEventListener('click', () => {
      selectedRating = parseInt(star.getAttribute('data-value'));
      stars.forEach(s => s.classList.remove('selected'));
      for (let i = 0; i < selectedRating; i++) {
        stars[i].classList.add('selected');
      }
    });
  });
 /* document.getElementById('validateRating').addEventListener('click', () => {
  if (selectedRating > 0) {
    // Redirige vers afficherReservationBack.php avec paramètre ?rated=1
    window.location.href = '../../view/backOffice/afficherReservationBack.php?rated=1';
  } else {
    alert("Veuillez sélectionner une note.");
  } 
}); */


  // Valider la notation
  // Envoi AJAX de la note
function envoyerNote(note) {
  fetch('noter.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ rating: note })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert("Merci pour votre note !");
      document.getElementById('ratingModal').style.display = 'none';
    } else {
      alert("Erreur lors de l'envoi de la note.");
    }
  });
}

// Valider la notation
document.getElementById('validateRating').addEventListener('click', () => {
  if (selectedRating > 0) {
    envoyerNote(selectedRating);
  } else {
    alert("Veuillez sélectionner une note.");
  }
});


  // Annuler la notation
  document.getElementById('cancelRating').addEventListener('click', () => {
    document.getElementById('ratingModal').style.display = 'none';
  });
</script>


</body>
</html>
