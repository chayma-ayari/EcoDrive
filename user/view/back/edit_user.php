<?php
session_start();
require_once __DIR__ . '/../../controller/AuthController.php';

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    $_SESSION['error'] = "Vous devez être connecté en tant qu'administrateur pour accéder à cette page.";
    header("Location: /user/view/front/login.php");
    exit();
}

$database = new Database();
$pdo = $database->getConnection();
$auth = new AuthController($pdo);

// Vérifier si l'ID de l'utilisateur à modifier est fourni
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Aucun utilisateur sélectionné.";
    header("Location: /user/view/back/user_dashboard.php");
    exit();
}

$userId = $_GET['id'];
$userToEdit = $auth->getUserById($userId);

if (!$userToEdit) {
    $_SESSION['error'] = "Utilisateur non trouvé.";
    header("Location: /user/view/back/user_dashboard.php");
    exit();
}

$error = '';
$success = '';

// Récupérer les messages de session
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $errors = [];

    // Validation des champs obligatoires
    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire.";
    }
    if (empty($prenom)) {
        $errors[] = "Le prénom est obligatoire.";
    }
    if (empty($email)) {
        $errors[] = "L'email est obligatoire.";
    }

    // Validation du format de l'email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Le format de l'email n'est pas valide.";
    }

    // Validation du numéro de téléphone
    if (!empty($telephone)) {
        // Supprimer tous les caractères non numériques
        $telephone = preg_replace('/[^0-9]/', '', $telephone);
        if (strlen($telephone) != 8) {
            $errors[] = "Le numéro de téléphone doit contenir exactement 8 chiffres.";
        }
    }

    // Vérification si l'email est déjà utilisé par un autre utilisateur
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $existingUser = $auth->getUserByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            $errors[] = "Cette adresse email est déjà utilisée par un autre utilisateur.";
        }
    }

    // Validation du mot de passe si fourni
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
    }

    // Si aucune erreur, procéder à la mise à jour
    if (empty($errors)) {
        try {
            if ($auth->editUser($userId, $nom, $prenom, $telephone, $email, $adresse, $password)) {
                $_SESSION['success'] = "Utilisateur mis à jour avec succès.";
                header("Location: /user/view/back/user_dashboard.php");
                exit();
            } else {
                $errors[] = "Erreur lors de la mise à jour de l'utilisateur. Veuillez vérifier les données saisies.";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = "Une erreur de base de données est survenue. Veuillez réessayer plus tard.";
        } catch (Exception $e) {
            error_log("Edit user error: " . $e->getMessage());
            $errors[] = "Une erreur est survenue lors de la mise à jour : " . $e->getMessage();
        }
    }

    // Si des erreurs, les afficher
    if (!empty($errors)) {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Modifier Utilisateur</title>
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
                        <i class="fa fa-user"></i>&nbsp;PANEL ADMIN
                    </a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#"><i class="fa fa-user"></i> <?= htmlspecialchars($_SESSION['user']['prenom']) ?></a></li>
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
                        <p class="text-white mt-2"><?= htmlspecialchars($_SESSION['user']['nom'] . ' ' . $_SESSION['user']['prenom']) ?></p>
                    </li>
                    <li>
                        <a href="user_dashboard.php"><i class="fa fa-tachometer-alt"></i> Tableau de Bord</a>
                    </li>
                    <li>
                        <a href="edit_profile.php"><i class="fa fa-user-edit"></i> Modifier Profil</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Contenu Principal -->
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h2><i class="fa fa-user-edit"></i> Modifier Utilisateur</h2>
                        <hr>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire de Modification -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="user-info">
                            <form method="POST" class="form-horizontal" onsubmit="return validateForm()">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Nom <span class="text-danger">*</span></label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="nom" 
                                               value="<?= htmlspecialchars($userToEdit['nom']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Prénom <span class="text-danger">*</span></label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="prenom" 
                                               value="<?= htmlspecialchars($userToEdit['prenom']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Email <span class="text-danger">*</span></label>
                                    <div class="col-md-10">
                                        <input type="email" class="form-control" name="email" 
                                               value="<?= htmlspecialchars($userToEdit['email']) ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Téléphone</label>
                                    <div class="col-md-10">
                                        <input type="tel" class="form-control" name="telephone" 
                                               value="<?= htmlspecialchars($userToEdit['telephone'] ?? '') ?>"
                                               pattern="[0-9]{8}" title="8 chiffres requis">
                                        <small class="text-muted">Format: 8 chiffres</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Adresse</label>
                                    <div class="col-md-10">
                                        <textarea class="form-control" name="adresse" rows="3"><?= htmlspecialchars($userToEdit['adresse'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Mot de passe</label>
                                    <div class="col-md-10">
                                        <input type="password" class="form-control" name="password" 
                                               placeholder="Laisser vide pour ne pas changer"
                                               minlength="8">
                                        <small class="text-muted">Minimum 8 caractères</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-2 col-md-10">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Enregistrer les modifications
                                        </button>
                                        <a href="/user/view/back/user_dashboard.php" class="btn btn-default">
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
    <script>
    function validateForm() {
        var nom = document.forms[0]["nom"].value;
        var prenom = document.forms[0]["prenom"].value;
        var email = document.forms[0]["email"].value;
        var telephone = document.forms[0]["telephone"].value;
        var password = document.forms[0]["password"].value;

        if (nom == "") {
            alert("Le nom est obligatoire.");
            return false;
        }
        if (prenom == "") {
            alert("Le prénom est obligatoire.");
            return false;
        }
        if (email == "") {
            alert("L'email est obligatoire.");
            return false;
        }
        if (telephone != "" && !/^\d{8}$/.test(telephone)) {
            alert("Le numéro de téléphone doit contenir exactement 8 chiffres.");
            return false;
        }
        if (password != "" && password.length < 8) {
            alert("Le mot de passe doit contenir au moins 8 caractères.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
