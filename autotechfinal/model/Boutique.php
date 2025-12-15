<?php
class Boutique {
    private $id_boutique;
    private $nom_boutique;
    private $adresse;
    private $telephone;
    private $logo;
    private $id_utilisateur;

    public function __construct($nom_boutique = "", $adresse = "", $telephone = "", $logo = "", $id_utilisateur = null) {
        $this->nom_boutique = $nom_boutique;
        $this->adresse = $adresse;
        $this->telephone = $telephone;
        $this->logo = $logo;
        $this->id_utilisateur = $id_utilisateur;
    }

    public function getIdBoutique() { return $this->id_boutique; }
    public function getNomBoutique() { return $this->nom_boutique; }
    public function getAdresse() { return $this->adresse; }
    public function getTelephone() { return $this->telephone; }
    public function getLogo() { return $this->logo; }
    public function getIdUtilisateur() { return $this->id_utilisateur; }

    public function setIdBoutique($id) { $this->id_boutique = $id; }
    public function setNomBoutique($nom) { $this->nom_boutique = $nom; }
    public function setAdresse($adresse) { $this->adresse = $adresse; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setLogo($logo) { $this->logo = $logo; }
    public function setIdUtilisateur($id_utilisateur) { $this->id_utilisateur = $id_utilisateur; }
}
?>
