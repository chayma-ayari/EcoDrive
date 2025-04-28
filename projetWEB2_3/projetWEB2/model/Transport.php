<?php
class Transport
{
    private ?int $id_t = null;  // Auto-incremented primary key, not directly handled
    private ?int $matricule = null;  // Regular attribute
    private ?string $brand = null;
    private ?string $type_t = null;
    private ?int $dispo = null;
    private ?int $etat = null;

    public function __construct($matricule = null, $brand = null, $type_t = null, $dispo = null, $etat = null)
    {
        $this->matricule = $matricule;
        $this->brand = $brand;
        $this->type_t = $type_t;
        $this->dispo = $dispo;
        $this->etat = $etat;
    }

    // Getters
    public function getMatricule() { return $this->matricule; }
    public function getBrand() { return $this->brand; }
    public function getType_t() { return $this->type_t; }
    public function getDispo() { return $this->dispo; }
    public function getEtat() { return $this->etat; }

    // Setters
    public function setMatricule($matricule) { $this->matricule = $matricule; return $this; }
    public function setBrand($brand) { $this->brand = $brand; return $this; }
    public function setType_t($type_t) { $this->type_t = $type_t; return $this; }
    public function setDispo($dispo) { $this->dispo = $dispo; return $this; }
    public function setEtat($etat) { $this->etat = $etat; return $this; }


    public function getId() {
        return $this->id_t;
    }
    
    public function setId($id_t) {
        $this->id_t = $id_t;
        return $this;
    }
    
}
