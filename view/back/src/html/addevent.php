<?php

include('../../../../controller/eventC.php');

$error = "";

// create event object
$event = null;

// create an instance of the controller
$eventC = new eventC();

if (
  isset($_POST["name"]) &&
  isset($_POST["description"]) &&
  isset($_POST["capacity"]) &&
  isset($_POST["location"]) &&
  isset($_POST["date"]) &&
  isset($_POST["time"])
) {
  if (
    !empty($_POST["name"]) &&
    !empty($_POST["description"]) &&
    !empty($_POST["capacity"]) &&
    !empty($_POST["location"]) &&
    !empty($_POST["date"]) &&
    !empty($_POST["time"])
  ) {
    $event = new Event(
      $_POST["name"],
      $_POST["description"],
      $_POST["capacity"],
      $_POST["location"],
      $_POST["date"],
      $_POST["time"]
    );

    $eventC->create($event);
  } else {
    $error = "Informations manquantes";
  }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modernize Free</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href=# class="text-nowrap logo-img">
            <img src="../assets/images/logos/dark-logo.svg" width="180" alt="" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <?php include("side.php") ?>
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link nav-icon-hover" href="javascript:void(0)">
                <i class="ti ti-bell-ringing"></i>
                <div class="notification bg-primary rounded-circle"></div>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <a href="https://adminmart.com/product/modernize-free-bootstrap-admin-dashboard/" target="_blank" class="btn btn-primary">Download Free</a>
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
                  <img src="../assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-mail fs-6"></i>
                      <p class="mb-0 fs-3">My Account</p>
                    </a>
                    <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-list-check fs-6"></i>
                      <p class="mb-0 fs-3">My Task</p>
                    </a>
                    <a href="./authentication-login.html" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->
      <div class="container-fluid">
        <!--  Row 1 -->
        <div class="row">
          <div class="card-body">
            <h5 class="card-title mt-5 fw-semibold mb-4">Ajouter un événement</h5>
            <div class="mt-5 card">
              <div class="card-body">
                <form action="" id="eventForm" method="post">
                  <div class="mb-3">
                    <label class="form-label">Nom :</label>
                    <input type="text" class="form-control" id="name" name="name">
                    <span id="namer"></span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Description :</label>
                    <input type="text" class="form-control" id="description" name="description">
                    <span id="descriptionr"></span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Capacité :</label>
                    <input type="number" class="form-control" id="capacity" name="capacity" min="1">
                    <span id="capacityr"></span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Lieu :</label>
                    <input type="text" class="form-control" id="location" name="location">
                    <span id="locationr"></span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Date :</label>
                    <input type="date" class="form-control" id="date" name="date">
                    <span id="dater"></span>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Heure :</label>
                    <input type="time" class="form-control" id="time" name="time">
                    <span id="timer"></span>
                  </div>
                  <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>

                <script>
                  let form = document.getElementById('eventForm');
                  form.addEventListener('submit', function (e) {
                    const regex = /^[a-zA-Z-\s]+$/;

                    let name = document.getElementById('name');
                    let description = document.getElementById('description');
                    let location = document.getElementById('location');
                    let capacity = document.getElementById('capacity');
                    let date = document.getElementById('date');
                    let time = document.getElementById('time');

                    let valid = true;

                    if (name.value === '' || !regex.test(name.value)) {
                      document.getElementById('namer').innerHTML = "Nom invalide.";
                      document.getElementById('namer').style.color = 'red';
                      valid = false;
                    } else {
                      document.getElementById('namer').innerHTML = "";
                    }

                    if (description.value === '') {
                      document.getElementById('descriptionr').innerHTML = "Description requise.";
                      document.getElementById('descriptionr').style.color = 'red';
                      valid = false;
                    } else {
                      document.getElementById('descriptionr').innerHTML = "";
                    }

                    if (capacity.value <= 0 || capacity.value === '') {
                      document.getElementById('capacityr').innerHTML = "Capacité invalide.";
                      document.getElementById('capacityr').style.color = 'red';
                      valid = false;
                    } else {
                      document.getElementById('capacityr').innerHTML = "";
                    }

                    if (location.value === '') {
                      document.getElementById('locationr').innerHTML = "Lieu requis.";
                      document.getElementById('locationr').style.color = 'red';
                      valid = false;
                    } else {
                      document.getElementById('locationr').innerHTML = "";
                    }

                    if (date.value === '') {
                      document.getElementById('dater').innerHTML = "Date requise.";
                      document.getElementById('dater').style.color = 'red';
                      valid = false;
                    } else {
                      document.getElementById('dater').innerHTML = "";
                    }

                    if (time.value === '') {
                      document.getElementById('timer').innerHTML = "Heure requise.";
                      document.getElementById('timer').style.color = 'red';
                      valid = false;
                    } else {
                      document.getElementById('timer').innerHTML = "";
                    }

                    if (!valid) {
                      e.preventDefault();
                    }
                  });
                </script>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../assets/js/dashboard.js"></script>
</body>

</html>