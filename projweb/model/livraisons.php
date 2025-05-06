<?php
class Livraison
{
    private $id_liv;
    private $date_liv;
    private $lieu;
    private $tel;
    private $moy_transport;
    private $colis_id;

    // Constructor
    public function __construct($date_liv, $lieu, $tel, $moy_transport, $colis_id)
    {
        $this->date_liv = $date_liv;
        $this->lieu = $lieu;
        $this->tel = $tel;
        $this->moy_transport = $moy_transport;
        $this->colis_id = $colis_id;
    }
    // Getters
    public function getId()
    {
        return $this->id_liv;
    }
    public function getDateLiv()
    {
        return $this->date_liv;
    }
    public function getLieu()
    {
        return $this->lieu;
    }
    public function getTel()
    {
        return $this->tel;
    }
    public function getMoyTransport()
    {
        return $this->moy_transport;
    }
    public function getColisId()
    {
        return $this->colis_id;
    }

    // Setters
    public function setId($id_liv)
    {
        $this->id_liv = $id_liv;
    }
    public function setDateLiv($date_liv)
    {
        $this->date_liv = $date_liv;
    }
    public function setLieu($lieu)
    {
        $this->lieu = $lieu;
    }
    public function setTel($tel)
    {
        $this->tel = $tel;
    }
    public function setMoyTransport($moy_transport)
    {
        $this->moy_transport = $moy_transport;
    }
    public function setColisId($colis_id)
    {
        $this->colis_id = $colis_id;
    }

    // CRUD Operations
    public function create($conn)
    {
        $sql = "INSERT INTO livraisons (date_liv,colis_id, lieu, tel, moy_transport ) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $this->date_liv,
            $this->lieu,
            $this->tel,
            $this->moy_transport,
            $this->colis_id
        ]);
    }

    public static function getAll($conn)
    {
        $sql = "SELECT * FROM livraisons";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function output($conn, $id)
    {
        $sql = "SELECT * FROM livraisons WHERE id_liv = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($conn)
    {
        $sql = "UPDATE livraisons 
                SET date_liv = ?, lieu = ?, tel = ?, moy_transport = ? 
                WHERE id_liv = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $this->date_liv,
            $this->lieu,
            $this->tel,
            $this->moy_transport,
            $this->id_liv
        ]);
    }

    public function delete($conn)
    {
        $sql = "DELETE FROM livraisons WHERE id_liv = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$this->id_liv]);
    }
}
