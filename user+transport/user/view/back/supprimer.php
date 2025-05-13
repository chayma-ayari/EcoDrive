<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/AuthController.php';

// Vérifier si l'ID est présent
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID d'utilisateur non spécifié";
    header('Location: user_dashboard.php');
    exit();
}

$id = $_GET['id'];

// Connexion à la base de données
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Supprimer directement avec une requête SQL
    $query = "DELETE FROM user WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Utilisateur supprimé avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression";
    }
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données: " . $e->getMessage();
}

// Redirection vers le tableau de bord
header('Location: user_dashboard.php');
exit();
?> 