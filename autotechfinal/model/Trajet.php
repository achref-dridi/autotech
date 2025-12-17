<?php

class Trajet {
    private $id_trajet;
    private $id_utilisateur;
    private $lieu_depart;
    private $lieu_arrivee;
    private $date_depart;
    private $duree_minutes;
    private $budget;
    private $description;
    private $places_demandees;
    private $statut;
    private $date_creation;
    private $date_modification;

    public function __construct(
        $id_utilisateur,
        $lieu_depart,
        $lieu_arrivee,
        $date_depart,
        $duree_minutes,
        $budget,
        $description = '',
        $places_demandees = 1
    ) {
        $this->id_utilisateur = $id_utilisateur;
        $this->lieu_depart = $lieu_depart;
        $this->lieu_arrivee = $lieu_arrivee;
        $this->date_depart = $date_depart;
        $this->duree_minutes = $duree_minutes;
        $this->budget = $budget;
        $this->description = $description;
        $this->places_demandees = $places_demandees;
        $this->statut = 'disponible';
    }

    // Getters
    public function getIdTrajet() { return $this->id_trajet; }
    public function getIdUtilisateur() { return $this->id_utilisateur; }
    public function getLieuDepart() { return $this->lieu_depart; }
    public function getLieuArrivee() { return $this->lieu_arrivee; }
    public function getDateDepart() { return $this->date_depart; }
    public function getDureeMinutes() { return $this->duree_minutes; }
    public function getBudget() { return $this->budget; }
    public function getDescription() { return $this->description; }
    public function getPlacesDemandees() { return $this->places_demandees; }
    public function getStatut() { return $this->statut; }
    public function getDateCreation() { return $this->date_creation; }
    public function getDateModification() { return $this->date_modification; }

    // Setters
    public function setIdTrajet($id_trajet) { $this->id_trajet = $id_trajet; }
    public function setLieuDepart($lieu_depart) { $this->lieu_depart = $lieu_depart; }
    public function setLieuArrivee($lieu_arrivee) { $this->lieu_arrivee = $lieu_arrivee; }
    public function setDateDepart($date_depart) { $this->date_depart = $date_depart; }
    public function setDureeMinutes($duree_minutes) { $this->duree_minutes = $duree_minutes; }
    public function setBudget($budget) { $this->budget = $budget; }
    public function setDescription($description) { $this->description = $description; }
    public function setPlacesDemandees($places_demandees) { $this->places_demandees = $places_demandees; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setDateCreation($date_creation) { $this->date_creation = $date_creation; }
    public function setDateModification($date_modification) { $this->date_modification = $date_modification; }
}
