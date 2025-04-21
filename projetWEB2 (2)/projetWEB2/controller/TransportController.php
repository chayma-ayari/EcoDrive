<?php

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/Transport.php';



class TransportController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    public function getAllTransports()
    {
        try {
            $query = "SELECT * FROM transport";
            $stmt = $this->pdo->query($query);
            $transports = [];

            while ($row = $stmt->fetch()) {
                $transport = new Transport(
                    $row['matricule'],
                    $row['brand'],
                    $row['type_t'],
                    $row['dispo'],
                    $row['etat']
                );
                $transports[] = $transport;
            }

            return $transports;
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des transports : " . $e->getMessage();
            return [];
        }
    }

    public function addTransport(Transport $transport)
{
    try {
        $sql = "INSERT INTO transport (matricule, brand, type_t, dispo, etat) 
                VALUES (:matricule, :brand, :type_t, :dispo, :etat)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'matricule' => $transport->getMatricule(),
            'brand'     => $transport->getBrand(),
            'type_t'    => $transport->getType_t(),
            'dispo'     => $transport->getDispo(),
            'etat'      => $transport->getEtat()
        ]);
    } catch (PDOException $e) {
        echo "Erreur lors de l'ajout : " . $e->getMessage();
    }
}









    public function deleteTransport($matricule)
    {
        $sql = "DELETE FROM transport WHERE matricule = :matricule";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':matricule', $matricule);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    public function updateTransport($transport, $oldMatricule) {
        try {
            $db = config::getConnexion();
    
            $query = $db->prepare('
                UPDATE transport SET
                    matricule = :newMatricule,
                    brand = :brand,
                    type_t = :type_t,
                    dispo = :dispo,
                    etat = :etat
                WHERE matricule = :oldMatricule
            ');
    
            $query->execute([
                'newMatricule' => $transport->getMatricule(),
                'brand' => $transport->getBrand(),
                'type_t' => $transport->getType_t(),
                'dispo' => $transport->getDispo(),
                'etat' => $transport->getEtat(),
                'oldMatricule' => $oldMatricule
            ]);
    
            // Vérification debug
            // echo $query->rowCount() . " lignes modifiées.";
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
    
    public function getTransportByMatricule($matricule)
    {
        $db = config::getConnexion();
        $query = $db->prepare('SELECT * FROM transport WHERE matricule = :matricule');
        $query->execute(['matricule' => $matricule]);
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return new Transport($data['matricule'], $data['brand'], $data['type_t'], $data['dispo'], $data['etat']);
        } else {
            return null;
        }
    }


    
    
// In TransportController.php
public function getAvailableTransportsByType() {
    // Get all available transports (dispo = 1 and etat = 1)
    $sql = "SELECT type_t, COUNT(*) AS count FROM transport WHERE dispo = 1 AND etat = 1 GROUP BY type_t";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}


