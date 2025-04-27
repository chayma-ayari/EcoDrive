<?php

include('../../controller/eventC.php');

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

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Free Bootstrap Admin Template : Two Page</title>
	<!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
     
           
          
    <div id="wrapper">
         <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="adjust-nav">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><i class="fa fa-square-o "></i>&nbsp;TWO PAGE</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#">See Website</a></li>
                        <li><a href="#">Open Ticket</a></li>
                        <li><a href="#">Report Bug</a></li>
                    </ul>
                </div>

            </div>
        </div>
        <!-- /. NAV TOP  -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li class="text-center user-image-back">
                        <img src="assets/img/find_user.png" class="img-responsive" />
                     
                    </li>


                    <li>
                        <a href="index.html"><i class="fa fa-desktop "></i>Dashboard</a>
                    </li>
                    
                    <li>
                        <a href="events.php"><i class="fa fa-event "></i>Gerer les Evenements</a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-edit "></i>UI Elements<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="#">Notifications</a>
                            </li>
                            <li>
                                <a href="#">Elements</a>
                            </li>
                            <li>
                                <a href="#">Free Link</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="#"><i class="fa fa-table "></i>Table Examples</a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-edit "></i>Forms </a>
                    </li>


                    <li>
                        <a href="#"><i class="fa fa-sitemap "></i>Multi-Level Dropdown<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="#">Second Level Link</a>
                            </li>
                            <li>
                                <a href="#">Second Level Link</a>
                            </li>
                            <li>
                                <a href="#">Second Level Link<span class="fa arrow"></span></a>
                                <ul class="nav nav-third-level">
                                    <li>
                                        <a href="#">Third Level Link</a>
                                    </li>
                                    <li>
                                        <a href="#">Third Level Link</a>
                                    </li>
                                    <li>
                                        <a href="#">Third Level Link</a>
                                    </li>

                                </ul>

                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-qrcode "></i>Tabs & Panels</a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-bar-chart-o"></i>Mettis Charts</a>
                    </li>

                    <li>
                        <a href="#"><i class="fa fa-edit "></i>Last Link </a>
                    </li>
                    <li>
                        <a href="blank.html"><i class="fa fa-table "></i>Blank Page</a>
                    </li>
                </ul>

            </div>

        </nav>
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                     <h2>Ajouter un événement</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
          <div class="card-body">
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
                 <!-- /. ROW  -->           
    </div>
             <!-- /. PAGE INNER  -->
            </div>
         <!-- /. PAGE WRAPPER  -->
        </div>
     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="assets/js/jquery.metisMenu.js"></script>
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
    
   
</body>
</html>
