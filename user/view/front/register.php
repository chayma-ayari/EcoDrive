<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controller/UserController.php';

$controller = new UserController($pdo);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $adresse = htmlspecialchars(trim($_POST['adresse']));
    $password = trim($_POST['password']); // garder brut pour le hashage

    // Hasher le mot de passe de manière sécurisée
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Appeler la méthode register du controller
    $controller->register($nom, $prenom, $email, $telephone, $adresse, $hashed_password);
}

// Préparer les données pour la vue
$view_data = [
    'error_message' => '',
    'success_message' => ''
];

// Ajouter un message d'erreur s'il existe
if (isset($_SESSION['error_message'])) {
    $view_data['error_message'] = '<div class="message error-message">' . 
                                   htmlspecialchars($_SESSION['error_message']) . 
                                   '</div>';
    unset($_SESSION['error_message']);
}

// Ajouter un message de succès s'il existe
if (isset($_SESSION['success_message'])) {
    $view_data['success_message'] = '<div class="message success-message">' . 
                                     htmlspecialchars($_SESSION['success_message']) . 
                                     '</div>';
    unset($_SESSION['success_message']);
}

// Afficher le formulaire
$view = new View(__DIR__ . '/../view/front-office/');
$view->display('register.html', $view_data);
?>
