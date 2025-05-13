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

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header('Location: /user/view/back/admin_dashboard.php?page=users');
    exit();
}

$userId = $_GET['id'];
$error = '';
$success = '';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    $controller = new AuthController($pdo);
    
    // Get user data
    $user = $controller->getUserById($userId);
    if (!$user) {
        header('Location: /user/view/back/admin_dashboard.php?page=users');
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $adresse = $_POST['adresse'] ?? '';
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        // Validate required fields
        if (empty($nom) || empty($prenom) || empty($email)) {
            $error = "Les champs nom, prénom et email sont obligatoires.";
        } else {
            // Update user
            if ($controller->updateUser($userId, $nom, $prenom, $email, $telephone, $adresse, $is_admin)) {
                $_SESSION['admin_success'] = "Utilisateur mis à jour avec succès.";
                header('Location: /user/view/back/admin_dashboard.php?page=users');
                exit();
            } else {
                $error = "Erreur lors de la mise à jour de l'utilisateur.";
            }
        }
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Erreur de connexion à la base de données.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .back-button {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="form-container">
            <a href="/user/view/back/admin_dashboard.php?page=users" class="btn btn-secondary back-button">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            
            <h2 class="mb-4"><i class="fas fa-user-edit me-2"></i>Modifier Utilisateur</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" 
                           value="<?= htmlspecialchars($user['nom']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" 
                           value="<?= htmlspecialchars($user['prenom']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" 
                           value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <textarea class="form-control" id="adresse" name="adresse" rows="3"><?= htmlspecialchars($user['adresse'] ?? '') ?></textarea>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" 
                           <?= $user['is_admin'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_admin">Administrateur</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html> 