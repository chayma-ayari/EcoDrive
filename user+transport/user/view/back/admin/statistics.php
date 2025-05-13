<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../controller/AuthController.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header('Location: /user/view/front/login.php');
    exit();
}

// Initialize database connection and controller
try {
    $database = new Database();
    $pdo = $database->getConnection();
    $controller = new AuthController($pdo);
    
    // Get statistics
    $totalUsers = $controller->getTotalUsers();
    $totalAdmins = $controller->getTotalAdmins();
    $usersWithPhone = $controller->getUsersWithPhone();
    $usersWithAddress = $controller->getUsersWithAddress();
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    $error = "Erreur de connexion à la base de données.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .stat-card {
            transition: transform 0.2s;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Statistiques</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-users stat-icon"></i>
                                <h3><?= $totalUsers ?></h3>
                                <p class="mb-0">Total Utilisateurs</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-user-shield stat-icon"></i>
                                <h3><?= $totalAdmins ?></h3>
                                <p class="mb-0">Administrateurs</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-phone stat-icon"></i>
                                <h3><?= $usersWithPhone ?></h3>
                                <p class="mb-0">Utilisateurs avec Téléphone</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-map-marker-alt stat-icon"></i>
                                <h3><?= $usersWithAddress ?></h3>
                                <p class="mb-0">Utilisateurs avec Adresse</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 