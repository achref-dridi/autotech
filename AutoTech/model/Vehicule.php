<?php
require_once __DIR__ . '/../config/config.php';

class Vehicule {
    private $id_vehicule;
    private $marque;
    private $modele;
    private $annee;
    private $carburant;
    private $kilometrage;
    private $couleur;
    private $transmission;
    private $image_principale;

    public function __construct($marque, $modele, $annee, $carburant, $kilometrage, $couleur, $transmission, $image_principale) {
        $this->marque = $marque;
        $this->modele = $modele;
        $this->annee = $annee;
        $this->carburant = $carburant;
        $this->kilometrage = $kilometrage;
        $this->couleur = $couleur;
        $this->transmission = $transmission;
        $this->image_principale = $image_principale;
    }

    
    public function getIdVehicule() { return $this->id_vehicule; }
    public function getMarque() { return $this->marque; }
    public function getModele() { return $this->modele; }
    public function getAnnee() { return $this->annee; }
    public function getCarburant() { return $this->carburant; }
    public function getKilometrage() { return $this->kilometrage; }
    public function getCouleur() { return $this->couleur; }
    public function getTransmission() { return $this->transmission; }
    public function getImagePrincipale() { return $this->image_principale; }

    public function setMarque($marque) { $this->marque = $marque; }
    public function setModele($modele) { $this->modele = $modele; }
    public function setAnnee($annee) { $this->annee = $annee; }
    public function setCarburant($carburant) { $this->carburant = $carburant; }
    public function setKilometrage($kilometrage) { $this->kilometrage = $kilometrage; }
    public function setCouleur($couleur) { $this->couleur = $couleur; }
    public function setTransmission($transmission) { $this->transmission = $transmission; }
    public function setImagePrincipale($image_principale) { $this->image_principale = $image_principale; }
}
?>
