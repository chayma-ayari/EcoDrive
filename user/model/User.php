<?php
class User {
    private $id;
    private $nom;
    private $prenom;
    private $telephone;
    private $email;
    private $adresse;
    private $password;
    private $is_admin;

    public function __construct($nom, $prenom, $telephone, $email, $adresse, $password, $id = null, $is_admin = false) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->adresse = $adresse;
        $this->password = $password;
        $this->id = $id;
        $this->is_admin = $is_admin;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getTelephone() { return $this->telephone; }
    public function getEmail() { return $this->email; }
    public function getAdresse() { return $this->adresse; }
    public function getPassword() { return $this->password; }
    public function isAdmin() { return $this->is_admin; }

    // Password handling
    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

   // public function verifyPassword($password) {
        //return password_verify($password, $this->password);
   // }
    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }
    public function getPasswordHash() {
        return $this->password;
    }

    public function verifyPassword($inputPassword) {
        return password_verify($inputPassword, $this->password);
    }
    
    // Mise à jour des informations de l'utilisateur
    public function update($nom, $prenom, $telephone, $email, $adresse) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->email = $email;
        $this->adresse = $adresse;
    }
}
?>
