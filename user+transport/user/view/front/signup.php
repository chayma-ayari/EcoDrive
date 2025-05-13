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
        header('Location: /user/view/back/admin_dashboard.php');
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
    $_SESSION['signup_error'] = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
}

// Initialiser les variables
$nom = '';
$prenom = '';
$telephone = '';
$email = '';
$adresse = '';
$error = isset($_SESSION['signup_error']) ? $_SESSION['signup_error'] : '';
unset($_SESSION['signup_error']); // Clear the error after displaying

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($controller)) {
    // Sécuriser les entrées
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation des champs
    $errors = [];

    // Vérification des champs obligatoires
    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire.";
    }
    if (empty($prenom)) {
        $errors[] = "Le prénom est obligatoire.";
    }
    if (empty($email)) {
        $errors[] = "L'email est obligatoire.";
    }
    if (empty($password)) {
        $errors[] = "Le mot de passe est obligatoire.";
    }

    // Vérification du format de l'email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Le format de l'email n'est pas valide.";
    }

    // Vérification du numéro de téléphone
    if (!empty($telephone)) {
        // Supprimer tous les caractères non numériques
        $telephone = preg_replace('/[^0-9]/', '', $telephone);
        if (strlen($telephone) != 8) {
            $errors[] = "Le numéro de téléphone doit contenir exactement 8 chiffres.";
        }
    }

    // Vérification de la correspondance des mots de passe
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Vérification de la longueur du mot de passe
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    // Vérification si l'email existe déjà
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $existingUser = $controller->readUserByEmail($email);
        if ($existingUser) {
            $errors[] = "Cette adresse email est déjà utilisée.";
        }
    }

    // Si aucune erreur, procéder à l'inscription
    if (empty($errors)) {
        try {
            // Créer le nouvel utilisateur
            $userData = [
                'nom' => $nom,
                'prenom' => $prenom,
                'telephone' => $telephone,
                'email' => $email,
                'adresse' => $adresse,
                'pw' => $password
            ];

            $controller->register($userData);
            
            // Rediriger vers la page de connexion avec un message de succès
            $_SESSION['signup_success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header('Location: /user/view/front/login.php');
            exit();
        } catch (Exception $e) {
            error_log("Signup error: " . $e->getMessage());
            $errors[] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
        }
    }

    // Si des erreurs, les afficher
    if (!empty($errors)) {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #4CAF50, #1E88E5);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .signup-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 600px;
        }
        .signup-title {
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
        .password-toggle {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2 class="signup-title">
            <i class="fas fa-user-plus mb-3" style="font-size: 3rem; display: block;"></i>
            Créer un compte
        </h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nom" class="form-label">
                        <i class="fas fa-user"></i>
                        Nom *
                    </label>
                    <input type="text" class="form-control" id="nom" name="nom" 
                           value="<?= htmlspecialchars($nom) ?>" required>
                    <div class="invalid-feedback">
                        Veuillez entrer votre nom.
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="prenom" class="form-label">
                        <i class="fas fa-user"></i>
                        Prénom *
                    </label>
                    <input type="text" class="form-control" id="prenom" name="prenom" 
                           value="<?= htmlspecialchars($prenom) ?>" required>
                    <div class="invalid-feedback">
                        Veuillez entrer votre prénom.
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i>
                    Adresse Email *
                </label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($email) ?>" required
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                <div class="invalid-feedback">
                    Veuillez entrer une adresse email valide.
                </div>
            </div>

            <div class="mb-3">
                <label for="telephone" class="form-label">
                    <i class="fas fa-phone"></i>
                    Téléphone
                </label>
                <input type="tel" class="form-control" id="telephone" name="telephone" 
                       value="<?= htmlspecialchars($telephone) ?>">
            </div>

            <div class="mb-3">
                <label for="adresse" class="form-label">
                    <i class="fas fa-map-marker-alt"></i>
                    Adresse
                </label>
                <textarea class="form-control" id="adresse" name="adresse" rows="2"><?= htmlspecialchars($adresse) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Mot de passe *
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required
                           minlength="8">
                    <span class="input-group-text password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="togglePassword"></i>
                    </span>
                </div>
                <div class="invalid-feedback">
                    Le mot de passe doit contenir au moins 8 caractères.
                </div>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">
                    <i class="fas fa-lock"></i>
                    Confirmer le mot de passe *
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                           minlength="8">
                    <span class="input-group-text password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                    </span>
                </div>
                <div class="invalid-feedback">
                    Les mots de passe doivent correspondre.
                </div>
            </div>

            <button type="submit" name="submit_signup" class="btn btn-primary w-100">
                <i class="fas fa-user-plus"></i>
                S'inscrire
            </button>
        </form>
        
        <div class="mt-3 text-center">
            <p>Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
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
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById('toggle' + (inputId === 'password' ? 'Password' : 'ConfirmPassword'));
        
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

    // Password match validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');

    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Les mots de passe ne correspondent pas");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }

    password.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
    </script>
</body>
</html>