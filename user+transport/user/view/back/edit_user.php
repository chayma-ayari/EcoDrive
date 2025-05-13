<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: /user/view/front/login.php");
    exit();
}

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID de l'utilisateur non spécifié.";
    header("Location: user_dashboard.php");
    exit();
}

$database = new Database();
$pdo = $database->getConnection();
$auth = new AuthController($pdo);

$userId = (int)$_GET['id'];
$userToEdit = $auth->findUserById($userId);

if (!$userToEdit) {
    $_SESSION['error'] = "Utilisateur non trouvé.";
    header("Location: user_dashboard.php");
    exit();
}

$errors = [];
$success = '';

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validation du nom
    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire";
    } elseif (strlen($nom) < 2 || strlen($nom) > 50) {
        $errors[] = "Le nom doit contenir entre 2 et 50 caractères";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/", $nom)) {
        $errors[] = "Le nom contient des caractères non autorisés";
    }

    // Validation du prénom
    if (empty($prenom)) {
        $errors[] = "Le prénom est obligatoire";
    } elseif (strlen($prenom) < 2 || strlen($prenom) > 50) {
        $errors[] = "Le prénom doit contenir entre 2 et 50 caractères";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/", $prenom)) {
        $errors[] = "Le prénom contient des caractères non autorisés";
    }

    // Validation de l'email
    if (empty($email)) {
        $errors[] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide";
    } else {
        // Vérifier si l'email existe déjà pour un autre utilisateur
        $existingUser = $auth->getUserByEmail($email);
        if ($existingUser && $existingUser['id'] != $userId) {
            $errors[] = "Cet email est déjà utilisé par un autre utilisateur";
        }
    }

    // Validation du téléphone
    if (!empty($telephone)) {
        $telephone = preg_replace('/[^0-9]/', '', $telephone);
        if (strlen($telephone) !== 8) {
            $errors[] = "Le numéro de téléphone doit contenir exactement 8 chiffres";
        }
    }

    // Validation de l'adresse
    if (!empty($adresse)) {
        if (strlen($adresse) > 255) {
            $errors[] = "L'adresse ne doit pas dépasser 255 caractères";
        }
    }

    // Validation du mot de passe
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
        } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre";
        }
    }

    // Si aucune erreur, procéder à la mise à jour
    if (empty($errors)) {
        $userData = [
            'id' => $userId,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'adresse' => $adresse
        ];

        if (!empty($password)) {
            $userData['pw'] = $password;
        }

        if ($auth->updateUser($userData)) {
            $_SESSION['success'] = "Utilisateur modifié avec succès";
            header('Location: user_dashboard.php');
            exit();
        } else {
            $errors[] = "Erreur lors de la modification de l'utilisateur";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'utilisateur</title>
    <link href="/user/view/back/assets/css/bootstrap.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="/user/view/back/assets/css/custom.css" rel="stylesheet" />
    <style>
        .edit-form {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .error-feedback {
            display: none;
            color: #dc3545;
            font-size: 80%;
            margin-top: 0.25rem;
        }
        .was-validated .form-control:invalid ~ .error-feedback {
            display: block;
        }
        .was-validated .form-control:invalid {
            border-color: #dc3545;
        }
        /* Masquer les messages de validation par défaut du navigateur */
        input:invalid {
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-form">
            <h2><i class="fa fa-user-edit"></i> Modifier l'utilisateur</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" class="form-control" 
                                   id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? $userToEdit['nom']) ?>" required>
                            <div class="error-feedback">
                                Entrez un nom valide (2-50 caractères, lettres uniquement)
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" class="form-control" 
                                   id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? $userToEdit['prenom']) ?>" required>
                            <div class="error-feedback">
                                Entrez un prénom valide (2-50 caractères, lettres uniquement)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" class="form-control" 
                           id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $userToEdit['email']) ?>" required>
                    <div class="error-feedback">
                        Entrez une adresse email valide
                    </div>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" class="form-control" 
                           id="telephone" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? $userToEdit['telephone']) ?>"
                           pattern="[0-9]{8}">
                    <div class="error-feedback">
                        Le numéro doit contenir exactement 8 chiffres
                    </div>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <textarea class="form-control" 
                              id="adresse" name="adresse" rows="3"><?= htmlspecialchars($_POST['adresse'] ?? $userToEdit['adresse']) ?></textarea>
                    <div class="error-feedback">
                        L'adresse ne doit pas dépasser 255 caractères
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                    <input type="password" class="form-control" 
                           id="password" name="password">
                    <div class="error-feedback">
                        Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Enregistrer les modifications
                    </button>
                    <a href="user_dashboard.php" class="btn btn-secondary">
                        <i class="fa fa-times"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="/user/view/back/assets/js/jquery-1.10.2.js"></script>
    <script src="/user/view/back/assets/js/bootstrap.min.js"></script>
    <script>
        // Validation côté client
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    // Supprimer la classe was-validated au chargement
                    form.classList.remove('was-validated');
                    
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        // Ajouter la classe was-validated seulement après la soumission
                        form.classList.add('was-validated');
                    }, false);
                });
            });
        })();
    </script>
</body>
</html>