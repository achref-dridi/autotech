<?php
require_once __DIR__ . '/../config/config.php';

class Vehicule {
    private $id_vehicule;
    private $id_utilisateur;
    private $marque;
    private $modele;
    private $annee;
    private $carburant;
    private $kilometrage;
    private $couleur;
    private $transmission;
    private $prix_journalier;
    private $description;
    private $image_principale;
    private $statut_disponibilite;
    
    // Constructeur
    public function __construct($id_utilisateur = null, $marque = '', $modele = '', $annee = null, 
                               $carburant = '', $kilometrage = 0) {
        $this->id_utilisateur = $id_utilisateur;
        $this->marque = $marque;
        $this->modele = $modele;
        $this->annee = $annee;
        $this->carburant = $carburant;
        $this->kilometrage = $kilometrage;
    }
    
    // Getters
    public function getIdVehicule() { return $this->id_vehicule; }
    public function getIdUtilisateur() { return $this->id_utilisateur; }
    public function getMarque() { return $this->marque; }
    public function getModele() { return $this->modele; }
    public function getAnnee() { return $this->annee; }
    public function getCarburant() { return $this->carburant; }
    public function getKilometrage() { return $this->kilometrage; }
    public function getCouleur() { return $this->couleur; }
    public function getTransmission() { return $this->transmission; }
    public function getPrixJournalier() { return $this->prix_journalier; }
    public function getDescription() { return $this->description; }
    public function getImagePrincipale() { return $this->image_principale; }
    public function getStatutDisponibilite() { return $this->statut_disponibilite; }
    
    // Setters
    public function setIdVehicule($id) { $this->id_vehicule = $id; }
    public function setIdUtilisateur($id) { $this->id_utilisateur = $id; }
    public function setMarque($marque) { $this->marque = $marque; }
    public function setModele($modele) { $this->modele = $modele; }
    public function setAnnee($annee) { $this->annee = $annee; }
    public function setCarburant($carburant) { $this->carburant = $carburant; }
    public function setKilometrage($kilometrage) { $this->kilometrage = $kilometrage; }
    public function setCouleur($couleur) { $this->couleur = $couleur; }
    public function setTransmission($transmission) { $this->transmission = $transmission; }
    public function setPrixJournalier($prix) { $this->prix_journalier = $prix; }
    public function setDescription($description) { $this->description = $description; }
    public function setImagePrincipale($image) { $this->image_principale = $image; }
    public function setStatutDisponibilite($statut) { $this->statut_disponibilite = $statut; }
    
    /**
     * Obtenir le nom complet du véhicule
     * @return string
     */
    public function getNomComplet() {
        return $this->marque . ' ' . $this->modele . ' (' . $this->annee . ')';
    }
}
?>
