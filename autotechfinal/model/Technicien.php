<?php

class Technicien {
    private $id_technicien;
    private $nom;
    private $specialite;
    private $telephone;
    private $email;
    private $disponibilite;
    private $date_creation;

    public function __construct($nom = '', $specialite = '', $telephone = '', $email = '') {
        $this->nom = $nom;
        $this->specialite = $specialite;
        $this->telephone = $telephone;
        $this->email = $email;
    }

    // Getters
    public function getIdTechnicien() { return $this->id_technicien; }
    public function getNom() { return $this->nom; }
    public function getSpecialite() { return $this->specialite; }
    public function getTelephone() { return $this->telephone; }
    public function getEmail() { return $this->email; }
    public function getDisponibilite() { return $this->disponibilite; }
    public function getDateCreation() { return $this->date_creation; }

    // Setters
    public function setIdTechnicien($id) { $this->id_technicien = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setSpecialite($specialite) { $this->specialite = $specialite; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setEmail($email) { $this->email = $email; }
    public function setDisponibilite($disponibilite) { $this->disponibilite = $disponibilite; }
    public function setDateCreation($date) { $this->date_creation = $date; }
}
?>
