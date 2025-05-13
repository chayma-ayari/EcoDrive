<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';

// Redirect if not authenticated
if (!isset($_SESSION['user'])) {
    header("Location: /user/view/front/login.php");
    exit();
}

$database = new Database();
$pdo = $database->getConnection();
$auth = new AuthController($pdo);
$user = $_SESSION['user'];

// Récupérer et effacer les messages
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error']);
unset($_SESSION['success']);

// Get users list with search and sort
$users = [];
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'id_desc';

if (!empty($searchTerm)) {
    $users = $auth->searchUser($searchTerm);
} else {
    switch ($sortBy) {
        case 'id_asc':
            $users = $auth->listUserByIdAsc();
            break;
        case 'nom_asc':
            $users = $auth->listUserByName('ASC');
            break;
        case 'nom_desc':
           $users = $auth->listUserByName('DESC');
            break;
        default:
            $users = $auth->listUser();
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tableau de Bord <?= $user['is_admin'] == 1 ? 'Administrateur' : 'Utilisateur' ?></title>
    <link href="/user/view/back/assets/css/bootstrap.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="/user/view/back/assets/css/custom.css" rel="stylesheet" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        :root {
            --primary-color:rgb(71, 98, 214);
            --secondary-color:rgb(69, 114, 203);
            --sidebar-bg: #ffffff;
            --sidebar-active:rgb(52, 83, 239);
            --text-light:rgb(118, 107, 240);
            --text-dark:rgb(108, 53, 249);
            --success-color:rgb(71, 178, 245);
            --danger-color: #c0392b;
            --warning-color: #f39c12;
            --info-color: #00c6ff;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(135deg, #f0f0f5, #d6d6e0);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }
        
        #wrapper {
            padding-left: 0;
            transition: all 0.3s ease;
        }
        
        .navbar-default.navbar-side {
            background-color: var(--sidebar-bg);
            position: fixed;
            width: 280px;
            height: 100%;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(177,156,217,0.3);
        }
        
        .sidebar-collapse {
            padding: 0;
            height: 100%;
            overflow-y: auto;
        }
        
        #main-menu {
            margin: 0;
            padding: 0;
        }
        
        #main-menu > li {
            list-style: none;
        }
        
        #main-menu > li > a {
            display: block;
            padding: 18px 25px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 5px solid transparent;
            font-weight: 600;
            font-size: 1rem;
        }
        
        #main-menu > li > a:hover,
        #main-menu > li > a.active-menu {
            background-color: rgba(255, 255, 255, 0.15);
            border-left-color: var(--sidebar-active);
            color: var(--sidebar-active);
        }
        
        #main-menu > li > a.stats-link {
            color: var(--sidebar-active);
        }
        
        #main-menu > li > a.stats-link:hover {
            color: var(--text-light);
        }
        
        .user-image-back {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(177,156,217,0.3);
            border-radius: 0 15px 15px 0;
        }
        
        .user-image-back img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 10px rgba(255,255,255,0.3);
            transition: transform 0.3s ease;
        }
        
        .user-image-back img:hover {
            transform: scale(1.1);
        }
        
        .user-image-back h4 {
            margin-top: 20px;
            font-weight: 700;
            color: white;
            letter-spacing: 1px;
            text-shadow: 0 0 5px rgba(0,0,0,0.3);
        }
        
        .nav-second-level {
            padding-left: 0;
            list-style: none;
            background-color: rgba(0, 0, 0, 0.15);
            border-radius: 0 0 0 10px;
        }
        
        .nav-second-level li a {
            display: block;
            padding: 14px 25px 14px 50px;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .nav-second-level li a:hover {
            background-color: rgba(255, 255, 255, 0.15);
            padding-left: 55px;
            color: var(--sidebar-active);
        }
        
        .fa.arrow {
            float: right;
            margin-top: 5px;
            transition: transform 0.3s;
        }
        
        #page-wrapper {
            margin-left: 280px;
            padding: 30px 40px;
            min-height: 100vh;
            transition: all 0.3s ease;
            background: #f0f0f5;
            box-shadow: inset 0 0 20px rgba(0,0,0,0.02);
            border-radius: 15px 0 0 15px;
        }
        
        .navbar-inverse {
            background-color: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 0 0 15px 15px;
        }
        
        .navbar-brand img {
            height: 35px;
        }
        
        .navbar-nav > li > a {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 1rem;
        }
        
        .panel {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: none;
            background-color: #fff;
            transition: box-shadow 0.3s ease;
        }
        
        .panel:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .panel-heading {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
            padding: 25px 30px;
            border-radius: 15px 15px 0 0 !important;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .panel-title {
            font-weight: 700;
            letter-spacing: 1px;
        }
        
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background-color: white;
        }
        
        .table {
            margin-bottom: 0;
            background-color: white;
        }
        
        .table > thead > tr > th {
            border-bottom: none;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.7px;
            color: #555;
            background-color:rgb(245, 243, 243);
        }
        
        .table > tbody > tr > td {
            border-top: none;
            vertical-align: middle;
            font-size: 1rem;
            color: #444;
            padding: 15px 10px;
        }
        
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f9f9ff;
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 600;
            padding: 10px 18px;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
        }
        
        .input-group .form-control {
            border-radius: 6px 0 0 6px;
        }
        
        .input-group-btn .btn {
            border-radius: 0 6px 6px 0;
        }
        
        .alert {
            border-radius: 6px;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .alert-danger {
            background-color: rgba(192, 57, 43, 0.1);
            color: var(--danger-color);
            border-left: 5px solid var(--danger-color);
        }
        
        .alert-success {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
            border-left: 5px solid var(--success-color);
        }
        
        @media(max-width: 768px) {
            #page-wrapper {
                margin-left: 0;
                padding: 20px;
                border-radius: 0;
            }
            
            .navbar-side {
                width: 260px;
                left: -260px;
                border-radius: 0;
            }
            
            #wrapper.active #page-wrapper {
                margin-left: 260px;
                border-radius: 0;
            }
        }
        
        /* Animation pour les lignes du tableau */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .table > tbody > tr {
            animation: fadeIn 0.3s ease forwards;
        }
        
        .table > tbody > tr:nth-child(1) { animation-delay: 0.1s; }
        .table > tbody > tr:nth-child(2) { animation-delay: 0.2s; }
        .table > tbody > tr:nth-child(3) { animation-delay: 0.3s; }
        .table > tbody > tr:nth-child(4) { animation-delay: 0.4s; }
        .table > tbody > tr:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li class="text-center user-image-back">
                        <?php if (!empty($user['profile_picture'])): ?>
                            <img src="<?= htmlspecialchars($user['profile_picture']) ?>" class="img-responsive rounded-circle" style="width:110px; height:110px; object-fit:cover;" />
                        <?php else: ?>
                            <img src="/user/view/back/assets/img/find_user.png" class="img-responsive" />
                        <?php endif; ?>
                        <h4><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h4>
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
                    <li>
                        <a href="history.php" class="stats-link">
                            <i class="fa fa-history"></i> Historique des connexions
                        </a>
                    </li>

                    <?php if ($user['is_admin']): ?>
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
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
        
        <div id="page-wrapper">
            <div class="navbar navbar-inverse navbar-fixed-top">
                <div class="adjust-nav">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">
                            <img src="/user/view/back/assets/img/logo.png" />
                        </a>
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="#"><i class="fa fa-user-circle"></i> <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></a></li>
                            <li><a href="/user/view/front/logout.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div id="page-inner">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-users"></i> Gestion des Utilisateurs
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <form method="GET" class="form-inline">
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?= htmlspecialchars($searchTerm) ?>">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-4">
                                        <form method="GET" class="form-inline">
                                            <select name="sort" class="form-control" onchange="this.form.submit()">
                                                <option value="id_desc" <?= $sortBy == 'id_desc' ? 'selected' : '' ?>>ID (Décroissant)</option>
                                                <option value="id_asc" <?= $sortBy == 'id_asc' ? 'selected' : '' ?>>ID (Croissant)</option>
                                                <option value="nom_asc" <?= $sortBy == 'nom_asc' ? 'selected' : '' ?>>Nom (A-Z)</option>
                                                <option value="nom_desc" <?= $sortBy == 'nom_desc' ? 'selected' : '' ?>>Nom (Z-A)</option>
                                            </select>
                                        </form>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <a href="add_user.php" class="btn btn-success">
                                            <i class="fa fa-user-plus"></i> Ajouter un utilisateur
                                        </a>
                                        <a href="export_pdf.php" class="btn btn-primary">
                                            <i class="fa fa-file-pdf"></i> PDF
                                        </a>
                                    </div>
                                </div>

                                <div class="table-responsive" style="margin-top: 20px;">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom</th>
                                                <th>Prénom</th>
                                                <th>Email</th>
                                                <th>Téléphone</th>
                                                <th>Adresse</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($users as $u): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($u['id']) ?></td>
                                                    <td><?= htmlspecialchars($u['nom']) ?></td>
                                                    <td><?= htmlspecialchars($u['prenom']) ?></td>
                                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                                    <td><?= htmlspecialchars($u['telephone']) ?></td>
                                                    <td><?= htmlspecialchars($u['adresse']) ?></td>
                                                    <td>
                                                        <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="delete_user.php?id=<?= $u['id'] ?>" 
                                                           class="btn btn-sm btn-danger"
                                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
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
        </div>
    </div>

    <script src="/user/view/back/assets/js/jquery-1.10.2.js"></script>
    <script src="/user/view/back/assets/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Toggle sidebar on mobile
            $('.navbar-toggle').click(function() {
                $('#wrapper').toggleClass('active');
            });
            
            // Toggle submenu
            $('.fa.arrow').click(function(e) {
                e.preventDefault();
                $(this).parent().next('.nav-second-level').slideToggle();
                $(this).toggleClass('fa-chevron-down fa-chevron-up');
            });
        });
    </script>
</body>
</html>
