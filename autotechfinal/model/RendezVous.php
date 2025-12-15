<?php

class RendezVous {
    private $id_rdv;
    private $id_technicien;
    private $id_utilisateur;
    private $date_rdv;
    private $type_intervention;
    private $commentaire;
    private $statut;
    private $date_creation;
    private $google_event_id;

    public function __construct($id_technicien = '', $id_utilisateur = '', $date_rdv = '', $type_intervention = '') {
        $this->id_technicien = $id_technicien;
        $this->id_utilisateur = $id_utilisateur;
        $this->date_rdv = $date_rdv;
        $this->type_intervention = $type_intervention;
        $this->statut = 'en attente';
    }

    // Getters
    public function getIdRdv() { return $this->id_rdv; }
    public function getIdTechnicien() { return $this->id_technicien; }
    public function getIdUtilisateur() { return $this->id_utilisateur; }
    public function getDateRdv() { return $this->date_rdv; }
    public function getTypeIntervention() { return $this->type_intervention; }
    public function getCommentaire() { return $this->commentaire; }
    public function getStatut() { return $this->statut; }
    public function getDateCreation() { return $this->date_creation; }
    public function getGoogleEventId() { return $this->google_event_id; }

    // Setters
    public function setIdRdv($id) { $this->id_rdv = $id; }
    public function setIdTechnicien($id) { $this->id_technicien = $id; }
    public function setIdUtilisateur($id) { $this->id_utilisateur = $id; }
    public function setDateRdv($date) { $this->date_rdv = $date; }
    public function setTypeIntervention($type) { $this->type_intervention = $type; }
    public function setCommentaire($commentaire) { $this->commentaire = $commentaire; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setDateCreation($date) { $this->date_creation = $date; }
    public function setGoogleEventId($id) { $this->google_event_id = $id; }
}
?>
