<?php
session_start();
require_once __DIR__ . '/../../controller/AuthController.php';

// Redirection si non authentifié
if (!isset($_SESSION['user'])) {
    header("Location: /user/view/front/login.php");
    exit();
}

$database = new Database();
$pdo = $database->getConnection();
$auth = new AuthController($pdo);
$user = $_SESSION['user'];

// Récupérer tous les utilisateurs
$users = $auth->listUser();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tableau de Bord <?= $user['is_admin'] == 1 ? 'Administrateur' : 'Utilisateur' ?></title>
    <!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <style>
        .user-info {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .action-buttons .btn {
            margin-right: 5px;
            min-width: 80px;
        }
        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Navigation Top -->
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="adjust-nav">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                        <i class="fa fa-user"></i>&nbsp;<?= $user['is_admin'] == 1 ? 'PANEL ADMIN' : 'MON COMPTE' ?>
                    </a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#"><i class="fa fa-user"></i> <?= htmlspecialchars($user['prenom']) ?></a></li>
                        <li><a href="/user/view/front/logout.php"><i class="fa fa-sign-out-alt"></i> Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Navigation Latérale -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li class="text-center user-image-back">
                        <img src="assets/img/user-avatar.png" class="img-responsive user-avatar" />
                        <p class="text-white mt-2"><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></p>
                    </li>
                    <li>
                        <a href="user_dashboard.php" class="active-menu"><i class="fa fa-tachometer-alt"></i> Tableau de Bord</a>
                    </li>
                    <li>
                        <a href="edit_profile.php"><i class="fa fa-user-edit"></i> Modifier Profil</a>
                    </li>
                    <?php if ($user['is_admin'] == 1): ?>
                    <li>
                        <a href="admin/users_list.php"><i class="fa fa-users"></i> Gestion Utilisateurs</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>

        <!-- Contenu Principal -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h2><i class="fa fa-tachometer-alt"></i> Tableau de Bord <?= $user['is_admin'] == 1 ? 'Administrateur' : 'Utilisateur' ?></h2>
                        <hr>
                    </div>
                </div>

                <!-- Tableau des Utilisateurs -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="user-info">
                            <h3><i class="fa fa-users"></i> Liste des Utilisateurs</h3>
                            <div class="table-responsive">
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
                                        <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($u['id']) ?></td>
                                            <td><?= htmlspecialchars($u['nom']) ?></td>
                                            <td><?= htmlspecialchars($u['prenom']) ?></td>
                                            <td><?= htmlspecialchars($u['email']) ?></td>
                                            <td><?= htmlspecialchars($u['telephone'] ?? 'Non renseigné') ?></td>
                                            <td><?= htmlspecialchars($u['adresse'] ?? 'Non renseignée') ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="/user/view/back/edit_user.php?id=<?= $u['id'] ?>" class="btn btn-edit btn-sm">
                                                        <i class="fa fa-edit"></i> Modifier
                                                    </a>
                                                    <a href="/user/view/back/delete_user.php?id=<?= $u['id'] ?>" class="btn btn-delete btn-sm" 
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                        <i class="fa fa-trash"></i> Supprimer
                                                    </a>
                                                </div>
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

    <!-- Scripts -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html> 