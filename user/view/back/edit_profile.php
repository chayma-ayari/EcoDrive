<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user'])) {
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
    $_SESSION['profile_error'] = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
}

// Get user data
$user = $_SESSION['user'];
$error = isset($_SESSION['profile_error']) ? $_SESSION['profile_error'] : '';
$success = isset($_SESSION['profile_success']) ? $_SESSION['profile_success'] : '';
unset($_SESSION['profile_error']);
unset($_SESSION['profile_success']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($controller)) {
    // Sanitize inputs
    $nom = filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_STRING);
    $prenom = filter_var($_POST['prenom'] ?? '', FILTER_SANITIZE_STRING);
    $telephone = filter_var($_POST['telephone'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $adresse = filter_var($_POST['adresse'] ?? '', FILTER_SANITIZE_STRING);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($nom) || empty($prenom) || empty($email)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        try {
            // Check if email is already used by another user
            $existingUser = $controller->getUserByEmail($email);
            if ($existingUser && $existingUser['id'] != $user['id']) {
                $error = "Cette adresse email est déjà utilisée.";
            } else {
                // If password change is requested
                if (!empty($current_password)) {
                    // Verify current password
                    $currentUser = $controller->getUserByEmail($user['email']);
                    if (!password_verify($current_password, $currentUser['pw'])) {
                        $error = "Mot de passe actuel incorrect.";
                    } elseif (empty($new_password) || empty($confirm_password)) {
                        $error = "Veuillez remplir les champs de nouveau mot de passe.";
                    } elseif ($new_password !== $confirm_password) {
                        $error = "Les nouveaux mots de passe ne correspondent pas.";
                    } elseif (strlen($new_password) < 8) {
                        $error = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
                    }
                }

                if (empty($error)) {
                    // Update user data
                    $result = $controller->editUser(
                        $user['id'],
                        $nom,
                        $prenom,
                        $telephone,
                        $email,
                        $adresse,
                        !empty($new_password) ? $new_password : null
                    );

                    if ($result) {
                        // Update session data
                        $_SESSION['user']['nom'] = $nom;
                        $_SESSION['user']['prenom'] = $prenom;
                        $_SESSION['user']['email'] = $email;
                        
                        $_SESSION['profile_success'] = "Profil mis à jour avec succès !";
                        header('Location: /user/view/back/user_dashboard.php');
                        exit();
                    } else {
                        $error = "Erreur lors de la mise à jour du profil.";
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            $error = "Une erreur est survenue lors de la mise à jour du profil.";
        }
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Modifier Profil</title>
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
                        <i class="fa fa-user"></i>&nbsp;MON COMPTE
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
                        <a href="user_dashboard.php"><i class="fa fa-tachometer-alt"></i> Tableau de Bord</a>
                    </li>
                    <li>
                        <a href="edit_profile.php" class="active-menu"><i class="fa fa-user-edit"></i> Modifier Profil</a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-cog"></i> Paramètres</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Contenu Principal -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h2><i class="fa fa-user-edit"></i> Modifier Profil</h2>
                        <hr>
                    </div>
                </div>

                <!-- Formulaire de Modification -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="user-info">
                            <form method="POST" class="form-horizontal">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Nom</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Prénom</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Email</label>
                                    <div class="col-md-10">
                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Téléphone</label>
                                    <div class="col-md-10">
                                        <input type="tel" class="form-control" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Adresse</label>
                                    <div class="col-md-10">
                                        <textarea class="form-control" name="adresse" rows="3"><?= htmlspecialchars($user['adresse'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-2 col-md-10">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Enregistrer les modifications
                                        </button>
                                        <a href="user_dashboard.php" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Retour
                                        </a>
                                    </div>
                                </div>
                            </form>
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