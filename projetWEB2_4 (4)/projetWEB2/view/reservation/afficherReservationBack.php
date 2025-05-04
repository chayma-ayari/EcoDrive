<?php
session_start();

require_once '../../model/Reservation.php';
require_once '../../controller/ReservationController.php';
require_once '../../model/config.php';

$reservationC = new ReservationController(); 
$listeReservations = $reservationC->afficherReservation();

// Statistiques des destinations
$destinations = [];
foreach ($listeReservations as $reservation) {
    $lieuArrive = $reservation['lieu_arrive'];
    if (isset($destinations[$lieuArrive])) {
        $destinations[$lieuArrive]++;
    } else {
        $destinations[$lieuArrive] = 1;
    }
}

$destinationLabels = json_encode(array_keys($destinations));
$destinationData = json_encode(array_values($destinations));
$destinationCounts = json_encode($destinations);

// ✅ Notification d'une nouvelle évaluation
$isRated = false;
if (isset($_SESSION['new_rating']) && $_SESSION['new_rating'] === true) {
    $isRated = true;
    unset($_SESSION['new_rating']);
}
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
        .table-container { background-color: #fff; padding: 30px; border-radius: 20px; margin: 50px auto; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); }
        .table-container h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .search-filter-container { display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; }
        .alert { text-align: center; margin-bottom: 20px; }
        th.sortable { cursor: pointer; }
        th.sortable:after { content: " ⇅"; font-size: 12px; color: #aaa; }
        .chart-container { width: 70%; margin: 50px auto; text-align: center; }
        .stats-list { list-style-type: none; padding: 0; text-align: left; margin-top: 30px; }
        .stats-list li { font-size: 1.1em; margin-bottom: 10px; }
        .stats-list span { font-weight: bold; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

                <!-- ✅ Notification -->
                <?php if ($isRated): ?>
                    <div class="alert alert-info">Un utilisateur a soumis une évaluation.</div>
                <?php elseif (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Réservation supprimée avec succès!</div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">Erreur lors de la suppression de la réservation.</div>
                <?php endif; ?>

                <div class="search-filter-container">
                    <input type="text" id="searchInput" class="form-control" placeholder="🔍 Rechercher...">
                    <select id="filterType" class="form-control">
                        <option value="">🚘 Filtrer par Type</option>
                        <option value="car">Car</option>
                        <option value="scooter">Scooter</option>
                        <option value="bike">Bike</option>
                    </select>
                    <select id="sortDate" class="form-control">
                        <option value="">📅 Trier par Date</option>
                        <option value="asc">Date croissante</option>
                        <option value="desc">Date décroissante</option>
                    </select>
                </div>

                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Matricule</th>
                            <th>Date</th>
                            <th>Lieu Départ</th>
                            <th>Lieu Arrivée</th>
                            <th>Type de Transport</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reservationTable">
                        <?php foreach($listeReservations as $reservation): ?>
                            <tr>
                                <td><?= $reservation['id_r']; ?></td>
                                <td><?= $reservation['matricule']; ?></td>
                                <td><?= $reservation['date_t']; ?></td>
                                <td><?= $reservation['lieu_depart']; ?></td>
                                <td><?= $reservation['lieu_arrive']; ?></td>
                                <td><?= strtolower($reservation['type_t']); ?></td>
                                <td>
                                    <a href="supprimerReservation.php?id_r=<?= $reservation['id_r']; ?>" class="btn btn-danger btn-sm">Supprimer</a>
                                    <a href="modifierReservation.php?id_r=<?= $reservation['id_r']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="chart-container">
                <h3>Statistiques sur les destinations les plus demandées</h3>
                <canvas id="destinationChart"></canvas>
            </div>

            <div class="chart-container">
                <h4>Nombre de réservations par destination:</h4>
                <ul class="stats-list" id="destinationStats"></ul>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="../backOffice/assets/js/jquery-1.10.2.js"></script>
<script src="../backOffice/assets/js/bootstrap.min.js"></script>
<script src="../backOffice/assets/js/custom.js"></script>

<!-- JS Filtres + Statistiques -->
<script>
    const searchInput = document.getElementById('searchInput');
    const filterType = document.getElementById('filterType');
    const sortDate = document.getElementById('sortDate');
    const tableBody = document.getElementById('reservationTable');
    const rows = Array.from(tableBody.querySelectorAll('tr'));

    function applyFiltersAndSort() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = filterType.value.toLowerCase();
        const dateSort = sortDate.value;

        let filteredRows = rows.filter(row => {
            const text = row.innerText.toLowerCase();
            const type = row.cells[5].innerText.toLowerCase();
            return text.includes(searchTerm) && (selectedType === "" || type === selectedType);
        });

        if (dateSort !== "") {
            filteredRows.sort((a, b) => {
                const dateA = new Date(a.cells[2].innerText);
                const dateB = new Date(b.cells[2].innerText);
                return dateSort === "asc" ? dateA - dateB : dateB - dateA;
            });
        }

        tableBody.innerHTML = '';
        filteredRows.forEach(row => tableBody.appendChild(row));
    }

    searchInput.addEventListener('input', applyFiltersAndSort);
    filterType.addEventListener('change', applyFiltersAndSort);
    sortDate.addEventListener('change', applyFiltersAndSort);

    const ctx = document.getElementById('destinationChart').getContext('2d');
    const destinationChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= $destinationLabels ?>,
            datasets: [{
                label: 'Destinations les plus demandées',
                data: <?= $destinationData ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40', '#9966FF'],
                borderColor: '#fff',
                borderWidth: 1
            }]
        }
    });

    const destinationStatsList = document.getElementById('destinationStats');
    const destinationCounts = <?= $destinationCounts ?>;
    for (const [destination, count] of Object.entries(destinationCounts)) {
        const listItem = document.createElement('li');
        listItem.innerHTML = `<span>${destination}:</span> ${count} réservations`;
        destinationStatsList.appendChild(listItem);
    }
</script>
</body>
</html>
