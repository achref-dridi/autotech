<?php
require_once __DIR__ . '/../config/config.php';

class Utilisateur {
    private $id_utilisateur;
    private $nom;
    private $prenom;
    private $email;
    private $telephone;
    private $mot_de_passe;
    private $adresse;
    private $ville;
    private $code_postal;
    private $photo_profil;
    private $statut;
    private $role;
    
    // Constructeur
    public function __construct($nom = '', $prenom = '', $email = '', $mot_de_passe = '') {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
    }
    
    // Getters
    public function getIdUtilisateur() { return $this->id_utilisateur; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getTelephone() { return $this->telephone; }
    public function getMotDePasse() { return $this->mot_de_passe; }
    public function getAdresse() { return $this->adresse; }
    public function getVille() { return $this->ville; }
    public function getCodePostal() { return $this->code_postal; }
    public function getPhotoProfil() { return $this->photo_profil; }
    public function getStatut() { return $this->statut; }
    public function getRole() { return $this->role; }
    
    // Setters
    public function setIdUtilisateur($id) { $this->id_utilisateur = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setMotDePasse($mot_de_passe) { $this->mot_de_passe = $mot_de_passe; }
    public function setAdresse($adresse) { $this->adresse = $adresse; }
    public function setVille($ville) { $this->ville = $ville; }
    public function setCodePostal($code_postal) { $this->code_postal = $code_postal; }
    public function setPhotoProfil($photo_profil) { $this->photo_profil = $photo_profil; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setRole($role) { $this->role = $role; }
    
    /**
     * Obtenir le nom complet de l'utilisateur
     * @return string
     */
    public function getNomComplet() {
        return $this->prenom . ' ' . $this->nom;
    }
}
?>
