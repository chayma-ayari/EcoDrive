<?php

include('../../controller/eventC.php');

$error = "";

// Create an instance of the controller
$eventC = new eventC();

// Get event by id
$id = $_GET['updateevent'];
$event = $eventC->findone($id);

// Update 
if (
  isset($_POST["nom"]) &&
  isset($_POST["description"]) &&
  isset($_POST["date"]) &&
  isset($_POST["heure"]) &&
  isset($_POST["lieu"]) &&
  isset($_POST["capacite"])
) {
  if (
    !empty($_POST["nom"]) &&
    !empty($_POST["description"]) &&
    !empty($_POST["date"]) &&
    !empty($_POST["heure"]) &&
    !empty($_POST["lieu"]) &&
    !empty($_POST["capacite"])
  ) {
    $event = new event(
      $_POST['nom'],
      $_POST['description'],
      $_POST['capacite'],
      $_POST['lieu'],
      $_POST['date'],
      $_POST['heure']
    );
    $eventC->update($event, $id);
  } else {
    $error = "Missing information";
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
                     <h2>Modifier un événement</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
          <div class="card-body">
          <div class="card-body">
                <h5 class="card-title mt-5 fw-semibold mb-4">Modifier un événement</h5>
                <div class="mt-5 card">
                <div class="card-body">
                    <form action="" id="formr" method="post">
                    <div class="mb-3">
                        <label class="form-label">Nom :</label>
                        <input type="text" value="<?= $event['name'] ?>" class="form-control" id="nom" name="nom">
                        <span id="nomr"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description :</label>
                        <input type="text" value="<?= $event['description'] ?>" class="form-control" id="description" name="description">
                        <span id="descriptionr"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date :</label>
                        <input type="date" value="<?= $event['date'] ?>" class="form-control" name="date">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Heure :</label>
                        <input type="time" value="<?= $event['time'] ?>" class="form-control" name="heure">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lieu :</label>
                        <input type="text" value="<?= $event['location'] ?>" class="form-control" name="lieu">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Capacité :</label>
                        <input type="number" value="<?= $event['capacity'] ?>" class="form-control" name="capacite" min="1">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                </div>
            </div>
            </div>
            </div>
            </div>

            <script>
            let myform = document.getElementById('formr');
            myform.addEventListener('submit', function(e) {
                let nameinput = document.getElementById('nom');
                let description = document.getElementById('description');
                const regex = /^[a-zA-Z-\s]+$/;

                if (description.value === '') {
                let descriptionr = document.getElementById('descriptionr');
                descriptionr.innerHTML = "Le champ description est vide.";
                descriptionr.style.color = 'red';
                e.preventDefault();
                } else if (!regex.test(description.value)) {
                let descriptionr = document.getElementById('descriptionr');
                descriptionr.innerHTML = "La description doit comporter uniquement des lettres et des tirets.";
                descriptionr.style.color = 'red';
                e.preventDefault();
                }

                if (nameinput.value === '') {
                let nameer = document.getElementById('nomr');
                nameer.innerHTML = "Le champ nom est vide.";
                nameer.style.color = 'red';
                e.preventDefault();
                } else if (!regex.test(nameinput.value)) {
                let nameer = document.getElementById('nomr');
                nameer.innerHTML = "Le nom doit comporter uniquement des lettres et des tirets.";
                nameer.style.color = 'red';
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
