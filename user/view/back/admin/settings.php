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
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    $error = "Erreur de connexion à la base de données.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle different settings updates here
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_password':
                if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
                    $result = $controller->updateAdminPassword(
                        $_SESSION['user']['id'],
                        $_POST['current_password'],
                        $_POST['new_password'],
                        $_POST['confirm_password']
                    );
                    if ($result) {
                        $success = "Mot de passe mis à jour avec succès.";
                    } else {
                        $error = "Erreur lors de la mise à jour du mot de passe.";
                    }
                }
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .settings-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .settings-section {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4"><i class="fas fa-cog me-2"></i>Paramètres</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card settings-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-lock me-2"></i>
                                    Sécurité
                                </h5>
                                <form method="POST" class="settings-section">
                                    <input type="hidden" name="action" value="update_password">
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        Mettre à jour le mot de passe
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card settings-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Informations système
                                </h5>
                                <div class="settings-section">
                                    <p><strong>Version PHP:</strong> <?= phpversion() ?></p>
                                    <p><strong>Version MySQL:</strong> <?= $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) ?></p>
                                    <p><strong>Dernière mise à jour:</strong> <?= date('d/m/Y H:i:s') ?></p>
                                </div>
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