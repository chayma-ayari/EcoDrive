<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: /user/view/front/login.php");
    exit();
}

$userId = null;
// Vérifier si un ID est fourni, sinon utiliser l'ID de l'utilisateur connecté
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $userId = (int)$_GET['id'];
} elseif (isset($_SESSION['user']['id'])) {
    $userId = (int)$_SESSION['user']['id'];
} else {
    $_SESSION['error'] = "ID d'utilisateur invalide.";
    header("Location: user_dashboard.php");
    exit();
}

try {
    $database = new Database();
    $pdo = $database->getConnection();
    $auth = new AuthController($pdo);
    
    // Récupérer les informations de l'utilisateur
    $userToEdit = $auth->getUserById($userId);
    
    if (!$userToEdit) {
        $_SESSION['error'] = "Utilisateur non trouvé.";
        header("Location: user_dashboard.php");
        exit();
    }
    
    // Traitement du formulaire de modification
    $errors = [];
    $fieldErrors = []; // Pour associer les erreurs à des champs spécifiques
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');
        
        // Validation des champs
        // Nom
        if (empty($nom)) {
            $fieldErrors['nom'] = "Le nom est obligatoire.";
        } elseif (strlen($nom) > 50) {
            $fieldErrors['nom'] = "Le nom ne peut pas dépasser 50 caractères.";
        } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s-]+$/", $nom)) {
            $fieldErrors['nom'] = "Le nom ne doit contenir que des lettres, espaces ou tirets.";
        }
        
        // Prénom
        if (empty($prenom)) {
            $fieldErrors['prenom'] = "Le prénom est obligatoire.";
        } elseif (strlen($prenom) > 50) {
            $fieldErrors['prenom'] = "Le prénom ne peut pas dépasser 50 caractères.";
        } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s-]+$/", $prenom)) {
            $fieldErrors['prenom'] = "Le prénom ne doit contenir que des lettres, espaces ou tirets.";
        }
        
        // Email
        if (empty($email)) {
            $fieldErrors['email'] = "L'email est obligatoire.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fieldErrors['email'] = "L'email n'est pas valide.";
        } elseif (strlen($email) > 100) {
            $fieldErrors['email'] = "L'email ne peut pas dépasser 100 caractères.";
        }
        
        // Téléphone
        if (!empty($telephone)) {
            if (!preg_match("/^\+?[1-9]\d{1,14}$/", $telephone)) {
                $fieldErrors['telephone'] = "Le numéro de téléphone n'est pas valide (format international requis).";
            }
        }
        
        // Adresse
        if (!empty($adresse) && strlen($adresse) > 255) {
            $fieldErrors['adresse'] = "L'adresse ne peut pas dépasser 255 caractères.";
        }
        
        // Si aucune erreur, mettre à jour
        if (empty($fieldErrors)) {
            // Sanitisation des données
            $nom = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');
            $prenom = htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
            $telephone = htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8');
            $adresse = htmlspecialchars($adresse, ENT_QUOTES, 'UTF-8');
            
            if ($auth->editUser($userId, $nom, $prenom, $telephone, $email, $adresse)) {
                $_SESSION['success'] = "Utilisateur modifié avec succès.";
                header("Location: user_dashboard.php");
                exit();
            } else {
                $errors[] = "Erreur lors de la modification de l'utilisateur.";
            }
        }
    }
} catch (Exception $e) {
    $errors[] = "Une erreur est survenue : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-edit"></i> 
                            Modifier l'utilisateur
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3 text-center">
                                <?php if (!empty($userToEdit['profile_picture'])): ?>
                                    <img src="<?= htmlspecialchars($userToEdit['profile_picture']) ?>" alt="Photo de profil" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="/user/view/back/assets/img/default_profile.png" alt="Photo de profil par défaut" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Changer la photo de profil</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control <?= isset($fieldErrors['nom']) ? 'is-invalid' : '' ?>" 
                                       id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? $userToEdit['nom']) ?>">
                                <?php if (isset($fieldErrors['nom'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($fieldErrors['nom']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom *</label>
                                <input type="text" class="form-control <?= isset($fieldErrors['prenom']) ? 'is-invalid' : '' ?>" 
                                       id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? $userToEdit['prenom']) ?>">
                                <?php if (isset($fieldErrors['prenom'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($fieldErrors['prenom']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control <?= isset($fieldErrors['email']) ? 'is-invalid' : '' ?>" 
                                       id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $userToEdit['email']) ?>">
                                <?php if (isset($fieldErrors['email'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($fieldErrors['email']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control <?= isset($fieldErrors['telephone']) ? 'is-invalid' : '' ?>" 
                                       id="telephone" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? $userToEdit['telephone'] ?? '') ?>">
                                <?php if (isset($fieldErrors['telephone'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($fieldErrors['telephone']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <textarea class="form-control <?= isset($fieldErrors['adresse']) ? 'is-invalid' : '' ?>" 
                                          id="adresse" name="adresse" rows="3"><?= htmlspecialchars($_POST['adresse'] ?? $userToEdit['adresse'] ?? '') ?></textarea>
                                <?php if (isset($fieldErrors['adresse'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($fieldErrors['adresse']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                                <a href="user_dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour
                                                             </a>
                            </div>
                            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                                <?php
                                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                                    $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
                                    $fileName = $_FILES['profile_picture']['name'];
                                    $fileSize = $_FILES['profile_picture']['size'];
                                    $fileType = $_FILES['profile_picture']['type'];
                                    $fileNameCmps = explode(".", $fileName);
                                    $fileExtension = strtolower(end($fileNameCmps));
                                    
                                    $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                    if (in_array($fileExtension, $allowedfileExtensions)) {
                                        $uploadFileDir = __DIR__ . '/assets/img/profile_pictures/';
                                        if (!is_dir($uploadFileDir)) {
                                            mkdir($uploadFileDir, 0755, true);
                                        }
                                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                                        $dest_path = $uploadFileDir . $newFileName;
                                        
                                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                            $relativePath = '/user/view/back/assets/img/profile_pictures/' . $newFileName;
                                            $auth->updateProfilePicture($userId, $relativePath);
                                            // Update $userToEdit to reflect new picture
                                            $userToEdit['profile_picture'] = $relativePath;
                                            // Update session profile picture to reflect immediately
                                            if (isset($_SESSION['user']) && $_SESSION['user']['id'] === $userId) {
                                                $_SESSION['user']['profile_picture'] = $relativePath;
                                            }
                                        } else {
                                            error_log("Failed to move uploaded file to $dest_path");
                                            echo '<div class="alert alert-danger">Erreur lors du téléchargement de la photo de profil.</div>';
                                        }
                                    } else {
                                        echo '<div class="alert alert-danger">Type de fichier non autorisé. Seules les images JPG, JPEG, PNG et GIF sont autorisées.</div>';
                                    }
                                }
                                ?>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>