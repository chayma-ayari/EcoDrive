<?php
// Activation du débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure clean session start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include required files using absolute paths
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';
require_once __DIR__ . '/../../model/User.php';

// Vérification si l'utilisateur est déjà connecté
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['is_admin']) {
        header('Location: /user/view/back/user_dashboard.php');
    } else {
        header('Location: /user/view/back/user_dashboard.php');
    }
    exit();
}

// Initialiser la connexion et le contrôleur
try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    if (!$pdo) {
        throw new Exception("Database connection failed");
    }
    
    $controller = new AuthController($pdo);
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    $_SESSION['login_error'] = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
}

// Initialiser les variables
$email = '';
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$success = isset($_SESSION['signup_success']) ? $_SESSION['signup_success'] : '';
unset($_SESSION['login_error']); // Clear the error after displaying
unset($_SESSION['signup_success']); // Clear the success message after displaying

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($controller)) {
    // Sécuriser les entrées
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    try {
        $controller->login($email, $password);
    } catch (Exception $e) {
        error_log("Login processing error: " . $e->getMessage());
        $error = "Une erreur est survenue lors de la connexion. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4CAF50, #1E88E5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
        }
        .login-title {
            margin-bottom: 1.5rem;
            text-align: center;
            color: #333;
        }
        .alert {
            margin-bottom: 1rem;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }
        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }
        .input-group-text {
            background-color: transparent;
            border-right: none;
        }
        .form-control.password {
            border-left: none;
        }
        .password-toggle {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">
            <i class="fas fa-user-circle mb-3" style="font-size: 3rem; display: block;"></i>
            Connexion
        </h2>

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

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>
                    Adresse Email
                </label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($email) ?>" required
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                <div class="invalid-feedback">
                    Veuillez entrer une adresse email valide.
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Mot de passe
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-key"></i>
                    </span>
                    <input type="password" class="form-control password" id="password" name="password" required>
                    <span class="input-group-text password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>
                <div class="invalid-feedback">
                    Veuillez entrer votre mot de passe.
                </div>
            </div>

            <div class="mb-3">
                <a href="forgot_password.php" class="text-decoration-none">Mot de passe oublié ?</a>
            </div>

            <button type="submit" name="submit_login" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt"></i>
                Connexion
            </button>
        </form>
        
        <div class="mt-3 text-center">
            <p>Pas encore de compte ? <a href="signup.php">Inscrivez-vous ici</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Form validation
    (function() {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();

    // Password toggle
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>
