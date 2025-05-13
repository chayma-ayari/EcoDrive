<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: /user/view/front/login.php");
    exit();
}

$database = new Database();
$pdo = $database->getConnection();
$auth = new AuthController($pdo);

// Vérifier si l'ID de l'utilisateur à supprimer est fourni
if (!isset($_GET['id'])) {
    header("Location: /user/view/back/user_dashboard.php");
    exit();
}

$userId = $_GET['id'];

// Empêcher l'auto-suppression
if ($userId == $_SESSION['user']['id']) {
    $_SESSION['error'] = "Vous ne pouvez pas vous supprimer vous-même.";
    header("Location: /user/view/back/user_dashboard.php");
    exit();
}

try {
    if ($auth->deleteUser($userId)) {
        $_SESSION['success'] = "Utilisateur supprimé avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Une erreur est survenue lors de la suppression.";
}

header("Location: /user/view/back/user_dashboard.php");
exit();
?>