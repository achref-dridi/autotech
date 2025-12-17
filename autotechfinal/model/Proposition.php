<?php

class Proposition {
    private $id_proposition;
    private $id_trajet;
    private $id_conducteur;
    private $prix;
    private $message;
    private $date_proposition;
    private $statut;

    public function __construct($id_trajet, $id_conducteur, $prix, $message = '') {
        $this->id_trajet = $id_trajet;
        $this->id_conducteur = $id_conducteur;
        $this->prix = $prix;
        $this->message = $message;
        $this->statut = 'en_attente';
    }

    // Getters
    public function getIdProposition() { return $this->id_proposition; }
    public function getIdTrajet() { return $this->id_trajet; }
    public function getIdConducteur() { return $this->id_conducteur; }
    public function getPrix() { return $this->prix; }
    public function getMessage() { return $this->message; }
    public function getDateProposition() { return $this->date_proposition; }
    public function getStatut() { return $this->statut; }

    // Setters
    public function setIdProposition($id_proposition) { $this->id_proposition = $id_proposition; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setDateProposition($date) { $this->date_proposition = $date; }
}
