<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    header('Location: /user/view/front/login.php');
    exit();
}

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../controller/AuthController.php';

// Initialize database connection and controller
try {
    $database = new Database();
    $pdo = $database->getConnection();
    $controller = new AuthController($pdo);
    
    // Get all users
    $users = $controller->listUser();
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    $error = "Erreur de connexion à la base de données.";
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    if ($controller->deleteUser($userId)) {
        $_SESSION['admin_success'] = "Utilisateur supprimé avec succès.";
        header('Location: /user/view/back/admin_dashboard.php?page=users');
        exit();
    } else {
        $error = "Erreur lors de la suppression de l'utilisateur.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .user-card {
            transition: transform 0.2s;
        }
        .user-card:hover {
            transform: translateY(-5px);
        }
        .action-buttons {
            margin-top: 1rem;
        }
        .btn-edit {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }
        .btn-edit:hover {
            background-color: #45a049;
            border-color: #45a049;
        }
        .btn-delete {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-delete:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4"><i class="fas fa-users me-2"></i>Liste des Utilisateurs</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php foreach ($users as $user): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card user-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-user me-2"></i>
                                        <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?>
                                    </h5>
                                    <p class="card-text">
                                        <i class="fas fa-envelope me-2"></i>
                                        <?= htmlspecialchars($user['email']) ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-phone me-2"></i>
                                        <?= htmlspecialchars($user['telephone'] ?? 'Non renseigné') ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <?= htmlspecialchars($user['adresse'] ?? 'Non renseignée') ?>
                                    </p>
                                    <p class="card-text">
                                        <i class="fas fa-user-shield me-2"></i>
                                        <?= $user['is_admin'] ? 'Administrateur' : 'Utilisateur' ?>
                                    </p>
                                    
                                    <div class="action-buttons">
                                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-edit text-white">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                        <button type="button" class="btn btn-delete text-white" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal<?= $user['id'] ?>">
                                            <i class="fas fa-trash me-2"></i>Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $user['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel<?= $user['id'] ?>">Confirmer la suppression</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Êtes-vous sûr de vouloir supprimer l'utilisateur <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?> ?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger">
                                                <i class="fas fa-trash me-2"></i>Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 