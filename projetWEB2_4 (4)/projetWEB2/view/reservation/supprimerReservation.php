<?php
// Inclure les fichiers nécessaires
require_once '../../controller/ReservationController.php'; // Le contrôleur
require_once '../../model/Reservation.php'; // Le modèle Reservation

// Vérifier si l'ID est passé dans l'URL
if (isset($_GET['id_r']) && !empty($_GET['id_r'])) {
    $id_r = $_GET['id_r'];

    // Créer une instance du contrôleur
    $reservationController = new ReservationController();

    // Appeler la méthode pour supprimer la réservation
    $result = $reservationController->supprimerReservation($id_r);

    // Vérifier si la suppression a réussi
    if ($result) {
        // Rediriger vers la page d'affichage des réservations
        header('Location: afficherReservation.php?success=true');
    } else {
        // Si la suppression a échoué, rediriger avec un message d'erreur
        header('Location: afficherReservation.php?error=true');
    }
} else {
    // Si l'ID n'est pas défini, rediriger vers la page des réservations
    header('Location: afficherReservation.php');
}
exit();
?>
