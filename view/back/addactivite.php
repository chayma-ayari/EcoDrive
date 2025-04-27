<?php
include('../../controller/ActiviteC.php');

$error = "";

// create activite object
$activite = null;

// create an instance of the controller
$activiteC = new ActiviteC();

if (
  isset($_POST["name"]) 
) {
  if (
    !empty($_POST["name"]) 
  ) {
    // create new Activite object
    $activite = new Activite(
      $_POST["name"],
      $_GET["id"]
    );

    // Add the activite to the database
    $activiteC->create($activite);
    header('Location:activite.php?event='. $_GET['id']);
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
                     <h2>Ajouter une activite</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
          <div class="card-body">
          <div class="mt-5 card">
              <div class="card-body">
              <form action="" id="activiteForm" method="post">
                <div class="mb-3">
                  <label class="form-label">Nom de l'activité :</label>
                  <input type="text" class="form-control" id="name" name="name">
                  <span id="namer"></span>
                </div>    
                <br>  
                <button type="submit" class="btn btn-primary">Ajouter Activité</button>
              </form>
              <script>
                let form = document.getElementById('activiteForm');
                form.addEventListener('submit', function (e) {
                  const regex = /^[a-zA-Z\s]+$/; // Regex for activity name validation (letters and spaces)

                  let name = document.getElementById('name');

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
              </script>  </div>
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
