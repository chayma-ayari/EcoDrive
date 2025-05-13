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

// Vérifier si l'utilisateur existe
$user = $auth->findUserById($userId);
if (!$user) {
    $_SESSION['error'] = "Utilisateur non trouvé.";
    header("Location: user_dashboard.php");
    exit();
}

// Supprimer l'utilisateur
if ($auth->deleteUser($userId)) {
    $_SESSION['success'] = "Utilisateur supprimé avec succès.";
} else {
    $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur.";
}

header("Location: user_dashboard.php");
exit();
?>

<br><br>
<a href="/user/view/back/user_dashboard.php">Retour au tableau de bord</a>