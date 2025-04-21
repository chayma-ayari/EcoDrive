<?php
class Reservation
{
    private ?int $id_r = null;
    private ?string $date_t = null;
    private ?string $lieu_depart = null;
    private ?string $lieu_arrive = null;
    private ?int $id_t = null;

    public function __construct($id_r = null, $date_t = null, $lieu_depart = null, $lieu_arrive = null, $id_t = null)
    {
        $this->id_r = $id_r;
        $this->date_t = $date_t;
        $this->lieu_depart = $lieu_depart;
        $this->lieu_arrive = $lieu_arrive;
        $this->id_t = $id_t;
    }

    // Getters
    public function getId_r() { return $this->id_r; }
    public function getDate_t() { return $this->date_t; }
    public function getLieu_depart() { return $this->lieu_depart; }
    public function getLieu_arrive() { return $this->lieu_arrive; }
    public function getId_t() { return $this->id_t; }

    // Setters
    public function setId_r($id_r) { $this->id_r = $id_r; return $this; }
    public function setDate_t($date_t) { $this->date_t = $date_t; return $this; }
    public function setLieu_depart($lieu_depart) { $this->lieu_depart = $lieu_depart; return $this; }
    public function setLieu_arrive($lieu_arrive) { $this->lieu_arrive = $lieu_arrive; return $this; }
    public function setId_t($id_t) { $this->id_t = $id_t; return $this; }



    public function supprimer($id_r)
    {
        // Connexion à la base de données
        $db = config::getConnexion(); // Connexion à la DB

        // Requête SQL pour supprimer une réservation
        $query = "DELETE FROM reservation WHERE id_r = :id_r";

        // Préparer la requête
        $stmt = $db->prepare($query);

        // Lier l'ID de réservation
        $stmt->bindParam(':id_r', $id_r, PDO::PARAM_INT);

        // Exécuter la requête et retourner si elle a réussi
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
