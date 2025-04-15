<?php

include('../../../../controller/ActiviteC.php');

$error = "";

// Create an instance of the controller
$activiteC = new ActiviteC();

// Get activite by id
$id = $_GET['updateactivite'];
$activite = $activiteC->findOne($id);

// Update activite
if (
  isset($_POST["name"]) 
) {
  if (
    !empty($_POST["name"]) 
  ) {
    // PHP validation: Check if the name contains only letters and spaces
    if (!preg_match("/^[a-zA-Z\s]+$/", $_POST["name"])) {
      $error = "Le nom de l'activité peut uniquement contenir des lettres et des espaces.";
    } else {
      // Create new Activite object and update in the database
      $activite = new Activite(
        $_POST["name"],
        1
      );
      $activiteC->update($activite, $id);
    }
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
              <form action="" id="activiteForm" method="post">
                <div class="mb-3">
                  <label class="form-label">Nom de l'activité :</label>
                  <input type="text" value="<?= $activite['name'] ?>" class="form-control" id="name" name="name">
                  <span id="namer"></span>
                </div>
                <button type="submit" class="btn btn-primary">Modifier Activité</button>
              </form>

              <script>
                let form = document.getElementById('activiteForm');
                form.addEventListener('submit', function (e) {
                  const regex = /^[a-zA-Z\s]+$/; // Regex for name validation (letters and spaces)

                  let name = document.getElementById('name');
                  let event = document.getElementById('event');

                  let valid = true;

                  // Name validation (only letters and spaces)
                  if (name.value === '' || !regex.test(name.value)) {
                    document.getElementById('namer').innerHTML = "Le nom d'activité peut uniquement contenir des lettres et des espaces.";
                    document.getElementById('namer').style.color = 'red';
                    valid = false;
                  } else {
                    document.getElementById('namer').innerHTML = "";
                  }

                  
                  // Prevent form submission if not valid
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