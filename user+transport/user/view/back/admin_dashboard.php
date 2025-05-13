<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header('Location: /user/view/front/login.php');
    exit();
}

// Get user data
$user = $_SESSION['user'];
$error = isset($_SESSION['admin_error']) ? $_SESSION['admin_error'] : '';
$success = isset($_SESSION['admin_success']) ? $_SESSION['admin_success'] : '';
unset($_SESSION['admin_error']);
unset($_SESSION['admin_success']);

// Get current page
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();
$auth = new AuthController($pdo);
$users = $auth->listUser();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #333;
            color: white;
        }
        .nav-link {
            color: white;
        }
        .nav-link:hover {
            background: #444;
            color: #fff;
        }
        .active {
            background: #4CAF50;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .welcome-section {
            background: linear-gradient(135deg, #4CAF50, #1E88E5);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .action-buttons .btn {
            margin-right: 5px;
            min-width: 80px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column p-3">
                    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 text-white text-decoration-none">
                        <i class="fas fa-user-shield me-2"></i>
                        <span class="fs-4">Admin Panel</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="?page=dashboard" class="nav-link <?= $current_page === 'dashboard' ? 'active' : 'text-white' ?>">
                                <i class="fas fa-home me-2"></i>
                                Accueil
                            </a>
                        </li>
                        <li>
                            <a href="?page=users" class="nav-link <?= $current_page === 'users' ? 'active' : 'text-white' ?>">
                                <i class="fas fa-users me-2"></i>
                                Gestion des Utilisateurs
                            </a>
                        </li>
                    <li>
                        <a href="?page=settings" class="nav-link <?= $current_page === 'settings' ? 'active' : 'text-white' ?>">
                            <i class="fas fa-cog me-2"></i>
                            Paramètres
                        </a>
                    </li>
                    <li>
                        <a href="history.php" class="nav-link <?= $current_page === 'history' ? 'active' : 'text-white' ?>">
                            <i class="fa fa-history me-2"></i>
                            Historique des connexions
                        </a>
                    </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2"></i>
                            <strong><?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="?page=settings"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/user/view/front/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 main-content">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php
                // Include the appropriate page content
                switch ($current_page) {
                    case 'users':
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-users me-2"></i>Gestion des Utilisateurs</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Liste des Utilisateurs</h5>
                                    <a href="../front/signup.php" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Ajouter un utilisateur
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
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
                                            <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['id']) ?></td>
                                                <td><?= htmlspecialchars($user['nom']) ?></td>
                                                <td><?= htmlspecialchars($user['prenom']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td><?= htmlspecialchars($user['telephone'] ?? 'Non renseigné') ?></td>
                                                <td><?= htmlspecialchars($user['adresse'] ?? 'Non renseignée') ?></td>
                                                <td class="action-buttons">
                                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                    <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
                                                        <i class="fas fa-trash"></i> Supprimer
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php
                        break;

                    case 'settings':
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <h4><i class="fas fa-cog me-2"></i>Paramètres</h4>
                            </div>
                            <div class="card-body">
                                <h5>Paramètres du Système</h5>
                                <p>Ici vous pouvez configurer les paramètres du système.</p>
                                <!-- Add your settings form here -->
                            </div>
                        </div>
                        <?php
                        break;

                    default:
                        // Dashboard content
                        ?>
                        <div class="welcome-section">
                            <h1><i class="fas fa-tachometer-alt me-2"></i>Dashboard Administrateur</h1>
                            <p>Bienvenue, <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?></p>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4><i class="fas fa-users me-2"></i>Liste des Utilisateurs</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5>Gestion des Utilisateurs</h5>
                                            <a href="../front/signup.php" class="btn btn-success">
                                                <i class="fas fa-plus"></i> Ajouter un utilisateur
                                            </a>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
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
                                                    <?php foreach ($users as $user): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($user['id']) ?></td>
                                                        <td><?= htmlspecialchars($user['nom']) ?></td>
                                                        <td><?= htmlspecialchars($user['prenom']) ?></td>
                                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                                        <td><?= htmlspecialchars($user['telephone'] ?? 'Non renseigné') ?></td>
                                                        <td><?= htmlspecialchars($user['adresse'] ?? 'Non renseignée') ?></td>
                                                        <td class="action-buttons">
                                                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i> Modifier
                                                            </a>
                                                            <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" 
                                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
                                                                <i class="fas fa-trash"></i> Supprimer
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
                        <?php
                        break;
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 