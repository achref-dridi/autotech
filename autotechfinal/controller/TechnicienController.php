<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/Technicien.php';

class TechnicienController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function addTechnicien($technicien) {
        try {
            $sql = "INSERT INTO technicien (nom, specialite, telephone, email, disponibilite)
                    VALUES (:nom, :specialite, :telephone, :email, :disponibilite)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $technicien->getNom(),
                ':specialite' => $technicien->getSpecialite(),
                ':telephone' => $technicien->getTelephone(),
                ':email' => $technicien->getEmail(),
                ':disponibilite' => $technicien->getDisponibilite() ?? 'actif'
            ]);
            return ['success' => true, 'message' => 'Technicien ajouté avec succès.', 'id' => $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()];
        }
    }

    public function getTechnicienById($id) {
        try {
            $sql = "SELECT * FROM technicien WHERE id_technicien = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getAllTechniciens() {
        try {
            $sql = "SELECT * FROM technicien WHERE disponibilite = 'actif' ORDER BY nom ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateTechnicien($id, $technicien) {
        try {
            $sql = "UPDATE technicien SET nom = :nom, specialite = :specialite, telephone = :telephone, email = :email
                    WHERE id_technicien = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $technicien->getNom(),
                ':specialite' => $technicien->getSpecialite(),
                ':telephone' => $technicien->getTelephone(),
                ':email' => $technicien->getEmail(),
                ':id' => $id
            ]);
            return ['success' => true, 'message' => 'Technicien mis à jour avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()];
        }
    }

    public function deleteTechnicien($id) {
        try {
            $sql = "DELETE FROM technicien WHERE id_technicien = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return ['success' => true, 'message' => 'Technicien supprimé avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()];
        }
    }

    public function checkAvailability($id_technicien, $date_rdv) {
        try {
            $sql = "SELECT COUNT(*) as count FROM rendez_vous 
                    WHERE id_technicien = :id AND DATE(date_rdv) = DATE(:date) AND statut != 'annule'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id_technicien, ':date' => $date_rdv]);
            $result = $stmt->fetch();
            return $result['count'] < 5; // Max 5 appointments per day per technician
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
