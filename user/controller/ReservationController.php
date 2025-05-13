<?php
require_once '../../model/config.php';
require_once '../../model/Reservation.php';

class ReservationController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    public function getAllReservations()
    {
        try {
            $query = "SELECT * FROM reservation";
            $stmt = $this->pdo->query($query);
            $reservations = [];

            while ($row = $stmt->fetch()) {
                $reservation = new Reservation(
                    $row['id_r'],
                    $row['date_t'],
                    $row['lieu_depart'],
                    $row['lieu_arrive'],
                    $row['id_t']
                    

                );
                $reservations[] = $reservation;
            }

            return $reservations;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des réservations : " . $e->getMessage();
            return [];
        }
    }

    public function ajouterReservation(Reservation $reservation)
    {
        try {
            $sql = "INSERT INTO reservation (date_t, lieu_depart, lieu_arrive, id_t) 
                    VALUES (:date_t, :lieu_depart, :lieu_arrive, :id_t)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'date_t'       => $reservation->getDate_t(),
                'lieu_depart'  => $reservation->getLieu_depart(),
                'lieu_arrive'  => $reservation->getLieu_arrive(),
                'id_t'         => $reservation->getId_t()
            ]);
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout de la réservation : " . $e->getMessage();
        }
    }
    public function afficherReservation() {
        $sql = "SELECT r.*, t.type_t ,t.id_t,t.matricule
                FROM reservation r
                JOIN transport t ON r.id_t = t.id_t
                WHERE t.etat = 1 AND t.dispo = 1"; // ✅ Filtre ajouté ici
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    // Dans ReservationController.php

    public function supprimerReservation($id_r)
    {
        // Créer une instance de la classe Reservation
        $reservation = new Reservation();

        // Appeler la méthode supprimer() du modèle
        $result = $reservation->supprimer($id_r);

        // Retourner le résultat (true si la suppression a réussi, false sinon)
        return $result;
    }
    public function recupererReservation($id_r) {
        $sql = "SELECT * FROM reservation WHERE id_r = :id_r";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_r', $id_r);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    
    public function modifierReservation($reservation) {
        $sql = "UPDATE reservation SET 
                    date_t = :date_t, 
                    lieu_depart = :lieu_depart, 
                    lieu_arrive = :lieu_arrive, 
                    id_t = :id_t 
                WHERE id_r = :id_r";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'date_t' => $reservation->getDate_t(),
                'lieu_depart' => $reservation->getLieu_depart(),
                'lieu_arrive' => $reservation->getLieu_arrive(),
                'id_t' => $reservation->getId_t(),
                'id_r' => $reservation->getId_r()
            ]);
        } catch (PDOException $e) {
            die('Erreur: ' . $e->getMessage());
        } }



        public function traiterModification() {
            if (
                isset($_POST['id_r']) && isset($_POST['date_t']) &&
                isset($_POST['lieu_depart']) && isset($_POST['lieu_arrive']) &&
                isset($_POST['id_t'])
            ) {
                $reservation = new Reservation(
                    $_POST['id_r'],
                    $_POST['date_t'],
                    $_POST['lieu_depart'],
                    $_POST['lieu_arrive'],
                    $_POST['id_t']
                );
        
                $this->modifierReservation($reservation);
                header("Location: ../../view/reservation/afficherReservation.php?success=updated");
                exit;
            } else {
                header("Location: ../../view/reservation/afficherReservation.php?error=missing_data");
                exit;
            }
        }
        
    
    

    
}
?>
