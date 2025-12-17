<?php
require_once __DIR__ . '/../model/Trajet.php';

class TrajetController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    /**
     * Add new trajet request
     */
    public function addTrajet($trajet) {
        try {
            $query = $this->pdo->prepare('
                INSERT INTO trajet (
                    id_utilisateur, lieu_depart, lieu_arrivee, date_depart, 
                    duree_minutes, budget, description, places_demandees
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');

            $result = $query->execute([
                $trajet->getIdUtilisateur(),
                $trajet->getLieuDepart(),
                $trajet->getLieuArrivee(),
                $trajet->getDateDepart(),
                $trajet->getDureeMinutes(),
                $trajet->getBudget(),
                $trajet->getDescription(),
                $trajet->getPlacesDemandees()
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Demande de trajet créée avec succès'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la création'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    /**
     * Get all available trajets (requests)
     */
    public function getAllTrajets() {
        $query = $this->pdo->prepare('
            SELECT t.*, 
                   u.prenom, u.nom, u.email, u.telephone
            FROM trajet t
            JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
            WHERE t.date_depart > NOW()
            ORDER BY t.date_depart ASC
        ');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user's trajet requests
     */
    public function getTrajetsByUtilisateur($idUtilisateur) {
        $query = $this->pdo->prepare('
            SELECT * FROM trajet
            WHERE id_utilisateur = ?
            ORDER BY date_depart DESC
        ');
        $query->execute([$idUtilisateur]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get trajet by ID
     */
    public function getTrajetById($idTrajet) {
        $query = $this->pdo->prepare('
            SELECT t.*, 
                   u.prenom, u.nom, u.email, u.telephone
            FROM trajet t
            JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
            WHERE t.id_trajet = ?
        ');
        $query->execute([$idTrajet]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update trajet request
     */
    public function updateTrajet($idTrajet, $trajet) {
        try {
            $query = $this->pdo->prepare('
                UPDATE trajet SET
                    lieu_depart = ?,
                    lieu_arrivee = ?,
                    date_depart = ?,
                    duree_minutes = ?,
                    budget = ?,
                    description = ?,
                    places_demandees = ?
                WHERE id_trajet = ?
            ');

            $result = $query->execute([
                $trajet->getLieuDepart(),
                $trajet->getLieuArrivee(),
                $trajet->getDateDepart(),
                $trajet->getDureeMinutes(),
                $trajet->getBudget(),
                $trajet->getDescription(),
                $trajet->getPlacesDemandees(),
                $idTrajet
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Trajet modifié avec succès'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la modification'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    /**
     * Delete trajet
     */
    public function deleteTrajet($idTrajet) {
        try {
            $query = $this->pdo->prepare('DELETE FROM trajet WHERE id_trajet = ?');
            $result = $query->execute([$idTrajet]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Trajet supprimé avec succès'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la suppression'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    /**
     * Check if user is owner of trajet
     */
    public function estProprietaire($idTrajet, $idUtilisateur) {
        $query = $this->pdo->prepare('SELECT id_utilisateur FROM trajet WHERE id_trajet = ?');
        $query->execute([$idTrajet]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result && $result['id_utilisateur'] == $idUtilisateur;
    }
}
