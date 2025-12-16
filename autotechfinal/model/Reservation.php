<?php

class Reservation {
    private $id_reservation;
    private $id_vehicule;
    private $id_utilisateur;
    private $date_debut;
    private $date_fin;
    private $statut;
    private $prix_total;
    private $date_creation;
    private $date_modification;

    public function __construct($id_vehicule = '', $id_utilisateur = '', $date_debut = '', $date_fin = '') {
        $this->id_vehicule = $id_vehicule;
        $this->id_utilisateur = $id_utilisateur;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->statut = 'en attente';
    }

    // Getters
    public function getIdReservation() { return $this->id_reservation; }
    public function getIdVehicule() { return $this->id_vehicule; }
    public function getIdUtilisateur() { return $this->id_utilisateur; }
    public function getDateDebut() { return $this->date_debut; }
    public function getDateFin() { return $this->date_fin; }
    public function getStatut() { return $this->statut; }
    public function getPrixTotal() { return $this->prix_total; }
    public function getDateCreation() { return $this->date_creation; }
    public function getDateModification() { return $this->date_modification; }

    // Setters
    public function setIdReservation($id) { $this->id_reservation = $id; }
    public function setIdVehicule($id) { $this->id_vehicule = $id; }
    public function setIdUtilisateur($id) { $this->id_utilisateur = $id; }
    public function setDateDebut($date) { $this->date_debut = $date; }
    public function setDateFin($date) { $this->date_fin = $date; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setPrixTotal($prix) { $this->prix_total = $prix; }
    public function setDateCreation($date) { $this->date_creation = $date; }
    public function setDateModification($date) { $this->date_modification = $date; }
}
?>
