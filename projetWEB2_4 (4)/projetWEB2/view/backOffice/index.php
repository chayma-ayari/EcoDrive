<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des Transports</title>
    <!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .table td, .table th {
            vertical-align: middle;
        }
        .action-btns a {
            margin-right: 10px;
            color: #fff;
            padding: 6px 12px;
            text-decoration: none;
        }
        .action-btns .btn-edit {
            background-color: #5bc0de;
        }
        .action-btns .btn-delete {
            background-color: #d9534f;
        }
        .action-btns .btn:hover {
            opacity: 0.8;
        }
    </style>
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
                    <a class="navbar-brand" href="#"><i class="fa fa-square-o "></i>&nbsp;Gestion des Transports</a>
                </div>
            </div>
        </div>

        <!-- NAV SIDE -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li><a href="index.html"><i class="fa fa-desktop "></i>Transports</a></li>
                    <li><a href="#"><i class="fa fa-table "></i>Utilisateurs</a></li>
                    <li><a href="#"><i class="fa fa-edit "></i>Livraison</a></li>
                    <li><a href="../reservation/afficherReservationBack.php"><i class="fa fa-qrcode"></i>Réservations</a></li>
                </ul>
            </div>
        </nav>

        <!-- PAGE WRAPPER -->
        <div id="page-wrapper">
            <div id="page-inner">

                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success">Transport supprimé avec succès !</div>
                <?php endif; ?>

                <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
                    <div class="alert alert-success">Transport ajouté avec succès !</div>
                <?php endif; ?>
                
                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success">Transport modifié avec succès !</div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label>Ajouter un nouveau transport</label>
                        </div>
                        <a href="../transport/add.php" class="btn btn-danger btn-lg btn-block">Ajouter Transport</a>
                    </div>
                </div>

                <!-- Filtres de recherche et tri -->
                <div class="row" style="margin-top: 20px; margin-bottom: 20px;">
                    <div class="col-md-6">
                        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Rechercher...">
                    </div>
                    <div class="col-md-6">
                        <select id="filterType" class="form-control">
                            <option value="">🚘 Filtrer par Type</option>
                            <option value="car">Car</option>
                            <option value="scooter">Scooter</option>
                            <option value="bike">Bike</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h5>Tableau des Transports</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Matricule</th>
                                        <th>Marque</th>
                                        <th>Type</th>
                                        <th>Disponible</th>
                                        <th>État</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="transportTable">
                                    <?php
                                    require_once '../../controller/TransportController.php';
                                    require_once '../../model/Transport.php';
                                    $controller = new TransportController();
                                    $transports = $controller->getAllTransports();

                                    foreach ($transports as $t):
                                    ?>
                                        <tr>
                                            <td><?= $t->getMatricule(); ?></td>
                                            <td><?= $t->getBrand(); ?></td>
                                            <td><?= strtolower($t->getType_t()); ?></td>
                                            <td><?= $t->getDispo() ? 'Oui' : 'Non'; ?></td>
                                            <td><?= $t->getEtat() ? 'Bon' : 'Mauvais'; ?></td>
                                            <td class="action-btns">
                                                <a href="../transport/edit.php?id_t=<?= $t->getId(); ?>" class="btn btn-edit">Modifier</a>
                                                <a href="../transport/delete.php?id_t=<?= $t->getId(); ?>" class="btn btn-delete" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/custom.js"></script>

    <!-- JS Recherche + Filtrage -->
    <script>
        const searchInput = document.getElementById('searchInput');
        const filterType = document.getElementById('filterType');
        const rows = document.querySelectorAll("#transportTable tr");

        function applyFilters() {
            const searchValue = searchInput.value.toLowerCase();
            const selectedType = filterType.value;

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const type = row.cells[2].innerText.toLowerCase();
                const matchSearch = text.includes(searchValue);
                const matchType = selectedType === "" || type === selectedType;
                row.style.display = (matchSearch && matchType) ? "" : "none";
            });
        }

        searchInput.addEventListener('keyup', applyFilters);
        filterType.addEventListener('change', applyFilters);
    </script>
</body>
</html>
