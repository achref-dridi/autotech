<?php
require_once __DIR__ . '/../model/Trajet.php';

class TrajetController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    /**
     * Add new trajet (ride)
     */
    public function addTrajet($trajet) {
        try {
            $query = $this->pdo->prepare('
                INSERT INTO trajet (
                    id_utilisateur, lieu_depart, lieu_arrivee, date_depart, 
                    duree_minutes, prix, description, places_disponibles
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');

            $result = $query->execute([
                $trajet->getIdUtilisateur(),
                $trajet->getLieuDepart(),
                $trajet->getLieuArrivee(),
                $trajet->getDateDepart(),
                $trajet->getDureeMinutes(),
                $trajet->getPrix(),
                $trajet->getDescription(),
                $trajet->getPlacesDisponibles()
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Trajet créé avec succès'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la création'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    /**
     * Get all available trajets
     */
    public function getAllTrajets() {
        $query = $this->pdo->prepare('
            SELECT t.*, 
                   u.prenom, u.nom, u.email, u.telephone
            FROM trajet t
            JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
            WHERE t.statut = "disponible" AND t.date_depart > NOW()
            ORDER BY t.date_depart ASC
        ');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user's trajets
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
     * Update trajet
     */
    public function updateTrajet($idTrajet, $trajet) {
        try {
            $query = $this->pdo->prepare('
                UPDATE trajet SET
                    lieu_depart = ?,
                    lieu_arrivee = ?,
                    date_depart = ?,
                    duree_minutes = ?,
                    prix = ?,
                    description = ?,
                    places_disponibles = ?
                WHERE id_trajet = ?
            ');

            $result = $query->execute([
                $trajet->getLieuDepart(),
                $trajet->getLieuArrivee(),
                $trajet->getDateDepart(),
                $trajet->getDureeMinutes(),
                $trajet->getPrix(),
                $trajet->getDescription(),
                $trajet->getPlacesDisponibles(),
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

    /**
     * Reserve a trajet
     */
    public function reserverTrajet($idTrajet, $idUtilisateur, $nombrePlaces = 1) {
        try {
            // Check if trajet exists and user is not owner
            $trajet = $this->getTrajetById($idTrajet);
            if (!$trajet) {
                return ['success' => false, 'message' => 'Trajet introuvable'];
            }

            if ($trajet['id_utilisateur'] == $idUtilisateur) {
                return ['success' => false, 'message' => 'Vous ne pouvez pas réserver votre propre trajet'];
            }

            // Check if already reserved
            $checkQuery = $this->pdo->prepare('
                SELECT * FROM reservation_trajet
                WHERE id_trajet = ? AND id_utilisateur = ? AND statut != "rejetee"
            ');
            $checkQuery->execute([$idTrajet, $idUtilisateur]);
            if ($checkQuery->fetch()) {
                return ['success' => false, 'message' => 'Vous avez déjà réservé ce trajet'];
            }

            // Insert reservation
            $query = $this->pdo->prepare('
                INSERT INTO reservation_trajet (id_trajet, id_utilisateur, nombre_places, statut)
                VALUES (?, ?, ?, "en attente")
            ');

            $result = $query->execute([$idTrajet, $idUtilisateur, $nombrePlaces]);

            if ($result) {
                return ['success' => true, 'message' => 'Réservation créée avec succès'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la réservation'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    /**
     * Get user's trajet reservations
     */
    public function getReservationsTrajet($idUtilisateur) {
        $query = $this->pdo->prepare('
            SELECT rt.*, t.lieu_depart, t.lieu_arrivee, t.date_depart, t.prix, t.duree_minutes,
                   u.prenom, u.nom, u.email, u.telephone
            FROM reservation_trajet rt
            JOIN trajet t ON rt.id_trajet = t.id_trajet
            JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
            WHERE rt.id_utilisateur = ?
            ORDER BY t.date_depart DESC
        ');
        $query->execute([$idUtilisateur]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get trajet reservations (for owner)
     */
    public function getReservationsByTrajet($idTrajet) {
        $query = $this->pdo->prepare('
            SELECT rt.*, u.prenom, u.nom, u.email, u.telephone
            FROM reservation_trajet rt
            JOIN utilisateur u ON rt.id_utilisateur = u.id_utilisateur
            WHERE rt.id_trajet = ?
            ORDER BY rt.date_creation DESC
        ');
        $query->execute([$idTrajet]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cancel reservation
     */
    public function cancelReservation($idReservation, $idUtilisateur) {
        try {
            $query = $this->pdo->prepare('
                UPDATE reservation_trajet SET statut = "annulee"
                WHERE id_reservation_trajet = ? AND id_utilisateur = ?
            ');

            $result = $query->execute([$idReservation, $idUtilisateur]);

            if ($result) {
                return ['success' => true, 'message' => 'Réservation annulée'];
            }
            return ['success' => false, 'message' => 'Erreur lors de l\'annulation'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    /**
     * Confirm reservation (for owner)
     */
    public function confirmReservation($idReservation, $idTrajet) {
        try {
            // Check if user is owner of trajet
            $trajet = $this->getTrajetById($idTrajet);
            if (!$trajet || $trajet['id_utilisateur'] != $_SESSION['user_id']) {
                return ['success' => false, 'message' => 'Non autorisé'];
            }

            $query = $this->pdo->prepare('
                UPDATE reservation_trajet SET statut = "confirmee"
                WHERE id_reservation_trajet = ? AND id_trajet = ?
            ');

            $result = $query->execute([$idReservation, $idTrajet]);

            if ($result) {
                return ['success' => true, 'message' => 'Réservation confirmée'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la confirmation'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    /**
     * Reject reservation (for owner)
     */
    public function rejectReservation($idReservation, $idTrajet) {
        try {
            $trajet = $this->getTrajetById($idTrajet);
            if (!$trajet || $trajet['id_utilisateur'] != $_SESSION['user_id']) {
                return ['success' => false, 'message' => 'Non autorisé'];
            }

            $query = $this->pdo->prepare('
                UPDATE reservation_trajet SET statut = "rejetee"
                WHERE id_reservation_trajet = ? AND id_trajet = ?
            ');

            $result = $query->execute([$idReservation, $idTrajet]);

            if ($result) {
                return ['success' => true, 'message' => 'Réservation rejetée'];
            }
            return ['success' => false, 'message' => 'Erreur lors du rejet'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }
}
