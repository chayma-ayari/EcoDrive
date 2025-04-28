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
                $transport->setId($row['id_t']);
                
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

    public function deleteTransport($id_t)
    {
        $sql = "DELETE FROM transport WHERE id_t = :id_t";
        $req = $this->pdo->prepare($sql);
        $req->bindValue(':id_t', $id_t);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    public function updateTransport($transport)
{
    try {
        $query = $this->pdo->prepare('
            UPDATE transport SET
                matricule = :matricule,
                brand = :brand,
                type_t = :type_t,
                dispo = :dispo,
                etat = :etat
            WHERE id_t = :id_t
        ');

        $query->execute([
            'matricule' => $transport->getMatricule(),
            'brand' => $transport->getBrand(),
            'type_t' => $transport->getType_t(),
            'dispo' => $transport->getDispo(),
            'etat' => $transport->getEtat(),
            'id_t' => $transport->getId() // Utilise directement l'ID de l'objet
        ]);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}


    public function getTransportById($id_t)
    {
        $query = $this->pdo->prepare('SELECT * FROM transport WHERE id_t = :id_t');
        $query->execute(['id_t' => $id_t]);
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $transport = new Transport($data['matricule'], $data['brand'], $data['type_t'], $data['dispo'], $data['etat']);
            $transport->setId($data['id_t']); // si setter existe
            return $transport;
        } else {
            return null;
        }
    }

    public function getAvailableTransportsByType()
    {
        $sql = "SELECT type_t, COUNT(*) AS count FROM transport WHERE dispo = 1 AND etat = 1 GROUP BY type_t";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
