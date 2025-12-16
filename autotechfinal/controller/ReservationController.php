<?php
require_once __DIR__ . '/../model/Reservation.php';

class ReservationController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    /**
     * Add new reservation
     */
    public function addReservation($reservation) {
        try {
            // Check for date conflicts
            $conflicts = $this->checkDateConflict(
                $reservation->getIdVehicule(),
                $reservation->getDateDebut(),
                $reservation->getDateFin()
            );

            if (!empty($conflicts)) {
                return [
                    'success' => false,
                    'message' => 'Ce véhicule est déjà réservé pendant cette période.'
                ];
            }

            // Calculate price
            $vehicule = $this->pdo->prepare('SELECT prix_journalier FROM vehicule WHERE id_vehicule = ?');
            $vehicule->execute([$reservation->getIdVehicule()]);
            $vehiculeData = $vehicule->fetch(PDO::FETCH_ASSOC);

            if (!$vehiculeData) {
                return ['success' => false, 'message' => 'Véhicule introuvable.'];
            }

            $dateDebut = new DateTime($reservation->getDateDebut());
            $dateFin = new DateTime($reservation->getDateFin());
            $interval = $dateDebut->diff($dateFin);
            $days = $interval->days + 1;
            $prixTotal = $vehiculeData['prix_journalier'] * $days;

            $query = $this->pdo->prepare('
                INSERT INTO reservation (id_vehicule, id_utilisateur, date_debut, date_fin, prix_total, statut)
                VALUES (?, ?, ?, ?, ?, ?)
            ');

            $result = $query->execute([
                $reservation->getIdVehicule(),
                $reservation->getIdUtilisateur(),
                $reservation->getDateDebut(),
                $reservation->getDateFin(),
                $prixTotal,
                'en attente'
            ]);

            return [
                'success' => $result,
                'message' => $result ? 'Réservation créée avec succès.' : 'Erreur lors de la création de la réservation.'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Check for date conflicts
     */
    public function checkDateConflict($idVehicule, $dateDebut, $dateFin) {
        $query = $this->pdo->prepare('
            SELECT * FROM reservation
            WHERE id_vehicule = ?
            AND statut != "annulée"
            AND (
                (date_debut <= ? AND date_fin >= ?)
                OR (date_debut <= ? AND date_fin >= ?)
                OR (date_debut >= ? AND date_fin <= ?)
            )
        ');

        $query->execute([
            $idVehicule,
            $dateFin,
            $dateDebut,
            $dateFin,
            $dateDebut,
            $dateDebut,
            $dateFin
        ]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get reservation by ID
     */
    public function getReservationById($id) {
        $query = $this->pdo->prepare('SELECT * FROM reservation WHERE id_reservation = ?');
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user's reservations
     */
    public function getReservationsByUser($idUtilisateur) {
        $query = $this->pdo->prepare('
            SELECT r.*, v.marque, v.modele, v.annee, v.image_principale, v.prix_journalier
            FROM reservation r
            JOIN vehicule v ON r.id_vehicule = v.id_vehicule
            WHERE r.id_utilisateur = ?
            ORDER BY r.date_debut DESC
        ');
        $query->execute([$idUtilisateur]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get vehicle's reservations
     */
    public function getReservationsByVehicle($idVehicule) {
        $query = $this->pdo->prepare('
            SELECT * FROM reservation
            WHERE id_vehicule = ?
            ORDER BY date_debut DESC
        ');
        $query->execute([$idVehicule]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update reservation
     */
    public function updateReservation($id, $reservation) {
        try {
            $current = $this->getReservationById($id);

            if (!$current) {
                return ['success' => false, 'message' => 'Réservation introuvable.'];
            }

            // Check for date conflicts (exclude current reservation)
            $query = $this->pdo->prepare('
                SELECT * FROM reservation
                WHERE id_vehicule = ? AND id_reservation != ?
                AND statut != "annulée"
                AND (
                    (date_debut <= ? AND date_fin >= ?)
                    OR (date_debut <= ? AND date_fin >= ?)
                    OR (date_debut >= ? AND date_fin <= ?)
                )
            ');

            $query->execute([
                $reservation->getIdVehicule(),
                $id,
                $reservation->getDateFin(),
                $reservation->getDateDebut(),
                $reservation->getDateFin(),
                $reservation->getDateDebut(),
                $reservation->getDateDebut(),
                $reservation->getDateFin()
            ]);

            if ($query->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'Ce véhicule est déjà réservé pendant cette période.'
                ];
            }

            // Calculate price
            $vehicule = $this->pdo->prepare('SELECT prix_journalier FROM vehicule WHERE id_vehicule = ?');
            $vehicule->execute([$reservation->getIdVehicule()]);
            $vehiculeData = $vehicule->fetch(PDO::FETCH_ASSOC);

            $dateDebut = new DateTime($reservation->getDateDebut());
            $dateFin = new DateTime($reservation->getDateFin());
            $interval = $dateDebut->diff($dateFin);
            $days = $interval->days + 1;
            $prixTotal = $vehiculeData['prix_journalier'] * $days;

            $query = $this->pdo->prepare('
                UPDATE reservation SET
                    id_vehicule = ?,
                    date_debut = ?,
                    date_fin = ?,
                    prix_total = ?,
                    date_modification = NOW()
                WHERE id_reservation = ?
            ');

            $result = $query->execute([
                $reservation->getIdVehicule(),
                $reservation->getDateDebut(),
                $reservation->getDateFin(),
                $prixTotal,
                $id
            ]);

            return [
                'success' => $result,
                'message' => $result ? 'Réservation modifiée avec succès.' : 'Erreur lors de la modification.'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cancel reservation
     */
    public function cancelReservation($id, $idUtilisateur) {
        try {
            $reservation = $this->getReservationById($id);

            if (!$reservation || $reservation['id_utilisateur'] != $idUtilisateur) {
                return ['success' => false, 'message' => 'Réservation introuvable ou non autorisée.'];
            }

            $query = $this->pdo->prepare('
                UPDATE reservation SET statut = "annulée" WHERE id_reservation = ?
            ');

            $result = $query->execute([$id]);

            return [
                'success' => $result,
                'message' => $result ? 'Réservation annulée.' : 'Erreur lors de l\'annulation.'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Confirm reservation
     */
    public function confirmReservation($id) {
        try {
            $query = $this->pdo->prepare('
                UPDATE reservation SET statut = "confirmée" WHERE id_reservation = ?
            ');

            $result = $query->execute([$id]);

            return [
                'success' => $result,
                'message' => $result ? 'Réservation confirmée.' : 'Erreur lors de la confirmation.'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
