<?php
class Colis
{
    private $id_colis;
    private $poids;
    private $contenu;
    private $statut;
    private $date_envoi;
    private $date_livraison;

    // Constructor
    public function __construct($id_colis, $poids, $contenu, $statut, $date_envoi, $date_livraison)
    {
        $this->id_colis = $id_colis;
        $this->poids = $poids;
        $this->contenu = $contenu;
        $this->statut = $statut;
        $this->date_envoi = $date_envoi;
        $this->date_livraison = $date_livraison;
    }
    // Getters
    public function getId()
    {
        return $this->id_colis;
    }
    public function getPoids()
    {
        return $this->poids;
    }
    public function getContenu()
    {
        return $this->contenu;
    }
    public function getStatut()
    {
        return $this->statut;
    }
    public function getDateEnvoi()
    {
        return $this->date_envoi;
    }
    public function getDateLivraison()
    {
        return $this->date_livraison;
    }

    // Setters
    public function setId($id_colis)
    {
        $this->id_colis = $id_colis;
    }
    public function setPoids($poids)
    {
        $this->poids = $poids;
    }
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }
    public function setDateEnvoi($date_envoi)
    {
        $this->date_envoi = $date_envoi;
    }
    public function setDateLivraison($date_livraison)
    {
        $this->date_livraison = $date_livraison;
    }

    // CRUD Operations
    public function create($conn)
    {
        $sql = "INSERT INTO colis (id_colis, poids, contenu, statut, date_envoi, date_livraison) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $this->id_colis,
            $this->poids,
            $this->contenu,
            $this->statut,
            $this->date_envoi,
            $this->date_livraison
        ]);
    }

    public function output($conn, $id)
    {
        $sql = "SELECT * FROM colis WHERE id_colis = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($conn)
    {
        $sql = "UPDATE colis 
                SET poids = ?, contenu = ?, statut = ?, date_envoi = ?, date_livraison = ? 
                WHERE id_colis = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([
            $this->poids,
            $this->contenu,
            $this->statut,
            $this->date_envoi,
            $this->date_livraison,
            $this->id_colis
        ]);
    }

    public function delete($conn)
    {
        $sql = "DELETE FROM colis WHERE id_colis = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$this->id_colis]);
    }

    public static function getAll($conn)
    {
        $sql = "SELECT * FROM colis";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getById($conn, $id)
    {
        $sql = "SELECT * FROM colis WHERE id_colis = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
