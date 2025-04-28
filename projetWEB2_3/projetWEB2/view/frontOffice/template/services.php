<?php
require_once '../../../controller/TransportController.php';
require_once '../../../model/Transport.php';

$controller = new TransportController();
$availableTransports = $controller->getAllTransports();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
  <title>Finance Business - Services</title>
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/fontawesome.css" />
  <link rel="stylesheet" href="assets/css/templatemo-finance-business.css" />
  <link rel="stylesheet" href="assets/css/owl.css" />
  <style>
    .table-container {
      margin-top: 20px;
      border: 1px solid #ddd;
      padding: 20px;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .table-container table {
      width: 100%;
      border-collapse: collapse;
    }

    .table-container th {
      background-color: #1dc5a3;
      color: white;
      padding: 10px;
    }

    .table-container td {
      background-color: #e6f7f3;
      padding: 10px;
      text-align: center;
    }

    .button-container {
      list-style: none;
      padding: 0;
    }

    .button-container li {
      margin-bottom: 10px;
    }

    .filled-button {
      display: block;
      width: 100%;
      padding: 10px;
      text-align: center;
      background-color: #1dc5a3;
      color: white;
      border-radius: 5px;
      text-decoration: none;
      transition: background-color 0.3s;
    }

    .filled-button:hover {
      background-color: #17ab8f;
      color: white;
      text-decoration: none;
    }
  </style>
</head>

<body>
  <div id="preloader">
    <div class="jumper"><div></div><div></div><div></div></div>
  </div>

  <!-- Header -->
  <div class="sub-header">
    <div class="container">
      <div class="row">
        <div class="col-md-8 col-xs-12">
          <ul class="left-info">
            <li><a href="#"><i class="fa fa-clock-o"></i>Mon-Fri 09:00-17:00</a></li>
            <li><a href="#"><i class="fa fa-phone"></i>090-080-0760</a></li>
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

  <header class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="index.html"><h2>Finance Business</h2></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.html">About Us</a></li>
          <li class="nav-item active"><a class="nav-link" href="services.html">Our Services</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.html">Contact Us</a></li>
          <li class="nav-item"><a class="nav-link" href="one-page.html">One Page</a></li>
        </ul>
      </div>
    </div>
  </header>

  <!-- Page Content -->
  <div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1>Our Services</h1>
          <span>We are over 20 years of experience</span>
        </div>
      </div>
    </div>
  </div>

  <div class="single-services">
    <div class="container">
      <div class="row" id="tabs">
        <!-- Colonne des boutons -->
        <div class="col-md-4">
          <ul class="button-container">
            <li><a href="#" class="filled-button" onclick="window.location.href='../../reservation/ajouterReservation.php'">
    Reservation
  </a></li>
            <li><a href="#tabs-2" class="filled-button">Reclamation</a></li>
            <li><a href="#tabs-3" class="filled-button">Event</a></li>
            <li><a href="#tabs-4" class="filled-button">Overall Evaluation</a></li>
          </ul>
        </div>

        <!-- Colonne de la table -->
        <div class="col-md-8">
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>Type de transport</th>
                  <th>Nombre disponible</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $transportCounts = [];
                foreach ($availableTransports as $t) {
                  if ($t->getDispo() == 1 && $t->getEtat() == 1) {
                    $type = $t->getType_t();
                    if (!isset($transportCounts[$type])) {
                      $transportCounts[$type] = 0;
                    }
                    $transportCounts[$type]++;
                  }
                }

                foreach ($transportCounts as $type => $count) {
                  echo '<tr>
                          <td>' . htmlspecialchars($type) . '</td>
                          <td>' . $count . '</td>
                        </tr>';
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="container"><div class="row"></div></div>
  </footer>

  <div class="sub-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <p>Copyright &copy; 2020 Financial Business Co., Ltd.
            - Design: <a rel="nofollow noopener" href="https://templatemo.com" target="_blank">TemplateMo</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- JS scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/owl.js"></script>
  <script src="assets/js/slick.js"></script>
  <script src="assets/js/accordions.js"></script>
</body>
</html>
