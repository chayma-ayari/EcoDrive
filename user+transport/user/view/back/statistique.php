<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'C:/xampp/htdocs/user/config/database.php';
require_once 'C:/xampp/htdocs/user/controller/AuthController.php';

if (!isset($_SESSION['user'])) {
    header("Location: /user/view/front/login.php");
    exit();
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // 1. Total users
    $sqlTotal = "SELECT COUNT(*) as total FROM user";
    $stmtTotal = $pdo->query($sqlTotal);
    $totalUsers = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // 2. Users by city
    $sqlByAddress = "SELECT 
                        TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(adresse, ',', 2), ',', -1)) AS ville,
                        COUNT(*) as count 
                     FROM user 
                     WHERE adresse IS NOT NULL AND adresse != ''
                     GROUP BY ville
                     ORDER BY count DESC";
    $stmtByAddress = $pdo->query($sqlByAddress);
    $usersByAddress = $stmtByAddress->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Admin stats
    $sqlAdmins = "SELECT 
                     SUM(CASE WHEN is_admin = 1 THEN 1 ELSE 0 END) as admins,
                     SUM(CASE WHEN is_admin = 0 THEN 1 ELSE 0 END) as non_admins
                  FROM user";
    $stmtAdmins = $pdo->query($sqlAdmins);
    $adminStats = $stmtAdmins->fetch(PDO::FETCH_ASSOC);
    $admins = $adminStats['admins'] ?? 0;
    $nonAdmins = $adminStats['non_admins'] ?? 0;
    
} catch (Exception $e) {
    $error = "Une erreur est survenue : " . $e->getMessage();
}

function safe_html($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des utilisateurs | Admin Panel</title>
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" />
    <link href="/user/view/back/assets/css/bootstrap.css" rel="stylesheet" />
    <link href="/user/view/back/assets/css/custom.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Keep existing styles unchanged */
        
        /* Add improved styles for the main content area */
        #page-wrapper {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea,rgb(33, 69, 229));
            color: white;
            border-radius: 15px;
            padding: 25px 20px;
            margin-bottom: 25px;
            box-shadow: 0 6px 15px rgba(102, 126, 234, 0.4);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(102, 126, 234, 0.6);
        }
        
        .stat-card-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        
        .stat-card-success {
            background: linear-gradient(135deg, #43cea2, #185a9d);
        }
        
        .stat-card-info {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
        }
        
        .stat-card .icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.85;
        }
        
        .stat-card h3 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: 1px;
        }
        
        .additional-stats {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
        }
        
        .additional-stats-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .additional-stats-item .label {
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .additional-stats-item .value {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .panel {
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: none;
        }
        
        .panel-heading {
            background: linear-gradient(135deg, #667eea,rgb(33, 69, 229));
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            padding: 20px 25px;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .panel-title i {
            margin-right: 10px;
        }
        
        .panel-body {
            padding: 25px;
            background-color: #f9f9ff;
        }
        
        .chart-container {
            position: relative;
            height: 320px;
            width: 100%;
        }
        
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        
        .table {
            margin-bottom: 0;
            background-color: white;
        }
        
        .table > thead > tr > th {
            border-bottom: none;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: #555;
            background-color: #f0f0ff;
        }
        
        .table > tbody > tr > td {
            border-top: none;
            vertical-align: middle;
            font-size: 0.95rem;
            color: #333;
        }
        
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f9f9ff;
        }
        
        /* Keep existing button styles */
        .btn {
            border-radius: 4px;
            font-weight: 500;
            padding: 8px 15px;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                        <i class="fas fa-chart-line"></i> <span>Tableau de Bord</span>
                    </a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#" id="sidebar-toggle"><i class="fas fa-bars"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li class="text-center user-image-back">
                        <img src="/user/view/back/assets/img/find_user.png" class="img-responsive" />
                        <h4><?= htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) ?></h4>
                    </li>
                   
                    
                    

                    <li>
                        <a class="active-menu" href="user_dashboard.php">
                            <i class="fa fa-dashboard"></i> Tableau de Bord
                        </a>
                    </li>

                    <li>
                        <a href="statistique.php" class="stats-link">
                            <i class="fa fa-bar-chart-o"></i> Statistiques
                        </a>
                    </li>

                    <?php if ($_SESSION['user']['is_admin'] == 1): ?>
                    <li>
                        <a href="#">
                            <i class="fa fa-sitemap"></i> Administration<span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level">
                            
                            <li> 
                                <a href="edit_profile.php">
                                    <i class="fa fa-user"></i> Gérer mon profil
                                </a>
                            </li>
                            
                        </ul>
                        <li class="user-sidebar-menu">
                        <a href="/user/view/front/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </li>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row page-heading">
                    <div class="col-md-8">
                        <h2><i class="fas fa-chart-pie"></i> Statistiques des Utilisateurs</h2>
                        <p class="subtitle">Analyse et visualisation des données utilisateurs</p>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                        </div>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="error-message fade-in">
                        <i class="fas fa-exclamation-circle"></i> <strong>Erreur :</strong> <?= safe_html($error) ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card stat-card-primary fade-in">
                            <i class="fas fa-users icon"></i>
                            <h3>Utilisateurs Totaux</h3>
                            <div class="number"><?= safe_html($totalUsers) ?></div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card stat-card-success fade-in delay-1">
                            <i class="fas fa-user-shield icon"></i>
                            <h3>Administrateurs</h3>
                            <div class="number"><?= safe_html($admins) ?></div>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $totalUsers > 0 ? ($admins/$totalUsers)*100 : 0 ?>%"></div>
                            </div>
                            <div class="additional-stats">
                                <div class="additional-stats-item">
                                    <span class="label">% du total</span>
                                    <span class="value"><?= $totalUsers > 0 ? round(($admins/$totalUsers)*100, 1) : 0 ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 col-sm-6">
                        <div class="stat-card stat-card-info fade-in delay-2">
                            <i class="fas fa-user icon"></i>
                            <h3>Utilisateurs Standard</h3>
                            <div class="number"><?= safe_html($nonAdmins) ?></div>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= $totalUsers > 0 ? ($nonAdmins/$totalUsers)*100 : 0 ?>%"></div>
                            </div>
                            <div class="additional-stats">
                                <div class="additional-stats-item">
                                    <span class="label">% du total</span>
                                    <span class="value"><?= $totalUsers > 0 ? round(($nonAdmins/$totalUsers)*100, 1) : 0 ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default fade-in">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fas fa-chart-pie"></i> Répartition par Ville</h3>
                            </div>
                            <div class="panel-body">
                                <div class="chart-container">
                                    <canvas id="cityChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="panel panel-default fade-in delay-1">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fas fa-chart-bar"></i> Types d'Utilisateurs</h3>
                            </div>
                            <div class="panel-body">
                                <div class="chart-container">
                                    <canvas id="roleChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row fade-in delay-2">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fas fa-table"></i> Répartition par Ville</h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Ville</th>
                                                <th>Utilisateurs</th>
                                                <th>% du total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($usersByAddress)): ?>
                                                <tr>
                                                    <td colspan="3" class="text-center">Aucune donnée disponible.</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($usersByAddress as $entry): ?>
                                                    <tr>
                                                        <td><?= safe_html($entry['ville']) ?></td>
                                                        <td><?= safe_html($entry['count']) ?></td>
                                                        <td><?= $totalUsers > 0 ? round(($entry['count']/$totalUsers)*100, 1) : 0 ?>%</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <footer class="text-center fade-in delay-3">
                    <hr>
                    <p class="text-muted small">
                        &copy; <?= date('Y') ?> Admin Panel. Tous droits réservés.
                    </p>
                </footer>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/custom.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#sidebar-toggle').click(function(e) {
                e.preventDefault();
                $('#wrapper').toggleClass('toggled');
            });
            
            const ctxCity = document.getElementById('cityChart').getContext('2d');
            const cityLabels = [<?php foreach ($usersByAddress as $entry) { echo "'" . addslashes($entry['ville']) . "',"; } ?>];
            const cityData = [<?php foreach ($usersByAddress as $entry) { echo $entry['count'] . ","; } ?>];
            
            const generateColors = (count) => {
                const colors = [];
                const hueStep = 360 / count;
                for (let i = 0; i < count; i++) {
                    const hue = i * hueStep;
                    colors.push(`hsl(${hue}, 70%, 60%)`);
                }
                return colors;
            };
            
            new Chart(ctxCity, {
                type: 'doughnut',
                data: {
                    labels: cityLabels,
                    datasets: [{
                        data: cityData,
                        backgroundColor: generateColors(cityLabels.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 20,
                                font: {
                                    family: 'Poppins',
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '70%',
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });

            const ctxRole = document.getElementById('roleChart').getContext('2d');
            new Chart(ctxRole, {
                type: 'bar',
                data: {
                    labels: ['Administrateurs', 'Utilisateurs Standard'],
                    datasets: [{
                        label: 'Nombre d\'utilisateurs',
                        data: [<?= $admins ?>, <?= $nonAdmins ?>],
                        backgroundColor: [
                            'rgba(67, 97, 238, 0.7)',
                            'rgba(76, 201, 240, 0.7)'
                        ],
                        borderColor: [
                            'rgba(67, 97, 238, 1)',
                            'rgba(76, 201, 240, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.raw || 0;
                                    const total = <?= $totalUsers ?>;
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500
                    }
                }
            });
        });
    </script>
</body>
</html>