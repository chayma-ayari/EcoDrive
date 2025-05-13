<?php
session_start();
// Load PHPMailer
$vendor_path = __DIR__ . '/../../vendor/autoload.php'; // Points to C:\xampp\htdocs\user\vendor
if (!file_exists($vendor_path)) {
    die("Erreur : vendor/autoload.php introuvable à " . $vendor_path . ". Assurez-vous que PHPMailer est installé via Composer dans C:\\xampp\\htdocs\\user.");
}
require $vendor_path;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';

$error = '';
$success = false;

if (!isset($_SESSION['reset_data']) || time() - $_SESSION['reset_data']['time'] > 15 * 60) {
    $error = "Session expirée. Veuillez redemander un code.";
    unset($_SESSION['reset_data']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');

    if (empty($otp) || empty($new_password)) {
        $error = "Veuillez remplir tous les champs.";
    } elseif ($otp !== $_SESSION['reset_data']['code']) {
        $error = "Code OTP incorrect.";
    } elseif (strlen($new_password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        $database = new Database();
        $db = $database->getConnection();
        $authController = new AuthController($db);

        $email = $_SESSION['reset_data']['email'];
        $user = $authController->getUserByEmail($email);

        if ($user) {
            $updateSuccess = $authController->updatePassword($user['id'], $new_password);
            if ($updateSuccess) {
                $success = true;
                unset($_SESSION['reset_data']);
            } else {
                $error = "Erreur lors de la réinitialisation du mot de passe.";
            }
        } else {
            $error = "Utilisateur introuvable.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .auth-card {
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #2ecc71;
            border: none;
            padding: 10px;
        }
        .btn-primary:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card card p-4">
            <div class="text-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="#2ecc71" viewBox="0 0 16 16">
                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                </svg>
                <h2 class="mt-3">Vérification du code</h2>
                <p class="text-muted">Entrez le code reçu et votre nouveau mot de passe</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Mot de passe réinitialisé avec succès !
                </div>
                <a href="login.php" class="btn btn-primary w-100">Se connecter</a>
            <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Code OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" 
                               placeholder="123456" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" 
                               placeholder="Minimum 8 caractères" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Réinitialiser</button>
                </form>
            <?php endif; ?>
            
            <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-decoration-none">← Redemander un code</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>