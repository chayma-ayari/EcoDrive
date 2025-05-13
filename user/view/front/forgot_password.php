<?php
session_start();

// Load PHPMailer
$vendor_path = __DIR__ . '/../../vendor/autoload.php'; // Points to C:\xampp\htdocs\user\vendor
if (!file_exists($vendor_path)) {
    die("Erreur : vendor/autoload.php introuvable à " . $vendor_path . ". Assurez-vous que PHPMailer est installé via Composer dans C:\\xampp\\htdocs\\user.");
}
require $vendor_path;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load configuration
$config_path = __DIR__ . '/../../config/email_config.php';
if (!file_exists($config_path)) {
    die("Erreur : config/email_config.php introuvable à " . $config_path . ". Assurez-vous que le fichier existe dans C:\\xampp\\htdocs\\user\\config.");
}
$config = require $config_path;

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $database = new Database();
        $db = $database->getConnection();
        $authController = new AuthController($db);

        $user = $authController->getUserByEmail($email);
        if ($user) {
            try {
                // Generate 6-digit OTP
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                // Setup PHPMailer
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $config['smtp']['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $config['smtp']['username'];
                $mail->Password = $config['smtp']['password'];
                $mail->SMTPSecure = $config['smtp']['secure'];
                $mail->Port = $config['smtp']['port'];
                $mail->SMTPDebug = $config['smtp']['debug'];

                // Sender and recipient
                $mail->setFrom($config['from']['email'], $config['from']['name']);
                $mail->addAddress($email);

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Code de réinitialisation de mot de passe';
                $mail->Body = "
                    <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;border:1px solid #eee;border-radius:5px;'>
                        <h2 style='color:#2ecc71;text-align:center;'>Réinitialisation du mot de passe</h2>
                        <p>Votre code de vérification est :</p>
                        <div style='background:#f8f9fa;padding:15px;text-align:center;font-size:24px;font-weight:bold;color:#2ecc71;margin:20px 0;border-radius:5px;'>
                            $otp
                        </div>
                        <p style='font-size:12px;color:#777;text-align:center;'>
                            Ce code est valable 15 minutes. Ignorez cet e-mail si vous n'avez pas fait cette demande.
                        </p>
                    </div>";

                // Send email
                if ($mail->send()) {
                    $_SESSION['reset_data'] = [
                        'email' => $email,
                        'code' => $otp,
                        'time' => time()
                    ];
                    $success = true;
                }
            } catch (Exception $e) {
                $error = "Échec de l'envoi de l'e-mail. Veuillez réessayer.";
                error_log("SMTP Error: " . $e->getMessage());
            }
        } else {
            $error = "Cet e-mail n'est pas enregistré.";
        }
    } else {
        $error = "Veuillez entrer une adresse e-mail valide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
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
                <h2 class="mt-3">Mot de passe oublié</h2>
                <p class="text-muted">Entrez votre e-mail pour recevoir un code</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <!-- Success message removed as per user request -->
                <a href="verify_otp.php" class="btn btn-primary w-100">Vérifier le code</a>
            <?php else: ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                               placeholder="exemple@domaine.com" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Envoyer le code</button>
                </form>
            <?php endif; ?>
            
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">← Retour</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>