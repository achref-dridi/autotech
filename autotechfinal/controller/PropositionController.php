<?php
require_once __DIR__ . '/../model/Proposition.php';

class PropositionController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    /**
     * Add new proposition (Driver offer)
     */
    public function addProposition($proposition) {
        try {
            // Check if user already proposed for this trajet
            $check = $this->pdo->prepare('SELECT id_proposition FROM proposition WHERE id_trajet = ? AND id_conducteur = ?');
            $check->execute([$proposition->getIdTrajet(), $proposition->getIdConducteur()]);
            if ($check->fetch()) {
                return ['success' => false, 'message' => 'Vous avez déjà fait une proposition pour ce trajet.'];
            }

            $query = $this->pdo->prepare('
                INSERT INTO proposition (id_trajet, id_conducteur, prix, message)
                VALUES (?, ?, ?, ?)
            ');

            $result = $query->execute([
                $proposition->getIdTrajet(),
                $proposition->getIdConducteur(),
                $proposition->getPrix(),
                $proposition->getMessage()
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Proposition envoyée avec succès'];
            }
            return ['success' => false, 'message' => 'Erreur lors de l\'envoi de la proposition'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    /**
     * Get propositions for a specific trajet (for Passenger/Admin)
     */
    public function getPropositionsByTrajet($idTrajet) {
        $query = $this->pdo->prepare('
            SELECT p.*, 
                   u.prenom, u.nom, u.email, u.telephone, u.photo_profil,
                   MAX(v.marque) as marque, MAX(v.modele) as modele, 
                   MAX(v.annee) as annee, MAX(v.image_principale) as image_principale
            FROM proposition p
            JOIN utilisateur u ON p.id_conducteur = u.id_utilisateur
            -- Driver might have a vehicle, but it\'s not strictly linked to proposition in table yet
            -- For now we just join user info. Typically we might want to know WHICH car. 
            -- Assuming driver has one main car or we assume generic driver info. 
            LEFT JOIN vehicule v ON v.id_utilisateur = u.id_utilisateur AND v.statut_disponibilite = "disponible" -- Simple heuristic
            WHERE p.id_trajet = ?
            GROUP BY p.id_proposition -- Avoid duplicates if multiple cars
            ORDER BY p.date_proposition DESC
        ');
        $query->execute([$idTrajet]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get propositions made by a driver
     */
    public function getPropositionsByConducteur($idConducteur) {
        $query = $this->pdo->prepare('
            SELECT p.*, t.lieu_depart, t.lieu_arrivee, t.date_depart
            FROM proposition p
            JOIN trajet t ON p.id_trajet = t.id_trajet
            WHERE p.id_conducteur = ?
            ORDER BY p.date_proposition DESC
        ');
        $query->execute([$idConducteur]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete/Cancel proposition
     */
    public function deleteProposition($idProposition) {
        try {
            $query = $this->pdo->prepare('DELETE FROM proposition WHERE id_proposition = ?');
            $result = $query->execute([$idProposition]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Proposition supprimée'];
            }
            return ['success' => false, 'message' => 'Erreur lors de la suppression'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    /**
     * Accept a proposition (Passenger)
     */
    public function acceptProposition($idProposition) {
        // Logic might involve setting status to 'acceptee' and maybe reject others?
        // Simple version: just toggle status
        try {
            $query = $this->pdo->prepare('UPDATE proposition SET statut = "acceptee" WHERE id_proposition = ?');
            $result = $query->execute([$idProposition]);
            return ['success' => true, 'message' => 'Proposition acceptée'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données'];
        }
    }
    /**
     * Reject a proposition (Passenger)
     */
    public function rejectProposition($idProposition) {
        try {
            $query = $this->pdo->prepare('UPDATE proposition SET statut = "refusee" WHERE id_proposition = ?');
            $result = $query->execute([$idProposition]);
            return ['success' => true, 'message' => 'Proposition refusée'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données'];
        }
    }
}
