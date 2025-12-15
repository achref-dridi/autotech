<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/Boutique.php';

class BoutiqueController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function addBoutique($boutique, $logoFile = null) {
        try {
            $logo = "";
            if ($logoFile && $logoFile['error'] == 0) {
                $targetDir = __DIR__ . '/../uploads/logos/';
                if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

                $logo = time() . '_' . basename($logoFile["name"]);
                $targetFile = $targetDir . $logo;
                if (move_uploaded_file($logoFile["tmp_name"], $targetFile)) {
                    $boutique->setLogo($logo);
                }
            }

            $sql = "INSERT INTO boutique (nom_boutique, adresse, telephone, logo, id_utilisateur)
                    VALUES (:nom, :adresse, :telephone, :logo, :id_utilisateur)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $boutique->getNomBoutique(),
                ':adresse' => $boutique->getAdresse(),
                ':telephone' => $boutique->getTelephone(),
                ':logo' => $boutique->getLogo(),
                ':id_utilisateur' => $boutique->getIdUtilisateur()
            ]);

            return ['success' => true, 'message' => 'Boutique ajoutée avec succès.', 'id' => $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'ajout: ' . $e->getMessage()];
        }
    }

    public function updateBoutique($boutique, $id, $logoFile = null) {
        try {
            $logo = $boutique->getLogo();
            if ($logoFile && $logoFile['error'] == 0) {
                $targetDir = __DIR__ . '/../uploads/logos/';
                $logo = time() . '_' . basename($logoFile["name"]);
                $targetFile = $targetDir . $logo;
                if (move_uploaded_file($logoFile["tmp_name"], $targetFile)) {
                    $boutique->setLogo($logo);
                }
            }

            $sql = "UPDATE boutique SET nom_boutique = :nom, adresse = :adresse, telephone = :telephone, logo = :logo
                    WHERE id_boutique = :id AND id_utilisateur = :id_utilisateur";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $boutique->getNomBoutique(),
                ':adresse' => $boutique->getAdresse(),
                ':telephone' => $boutique->getTelephone(),
                ':logo' => $logo,
                ':id' => $id,
                ':id_utilisateur' => $boutique->getIdUtilisateur()
            ]);

            return ['success' => true, 'message' => 'Boutique mise à jour avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()];
        }
    }

    public function getBoutiqueById($id) {
        try {
            $sql = "SELECT b.*, u.nom AS proprietaire, u.prenom 
                    FROM boutique b 
                    LEFT JOIN utilisateur u ON b.id_utilisateur = u.id_utilisateur 
                    WHERE b.id_boutique = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getBoutiquesByUser($id_utilisateur) {
        try {
            $sql = "SELECT * FROM boutique WHERE id_utilisateur = :id_utilisateur ORDER BY date_creation DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_utilisateur' => $id_utilisateur]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllBoutiques() {
        try {
            $sql = "SELECT b.*, u.nom AS proprietaire, u.prenom 
                    FROM boutique b 
                    LEFT JOIN utilisateur u ON b.id_utilisateur = u.id_utilisateur 
                    WHERE b.statut = 'actif'
                    ORDER BY b.date_creation DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function deleteBoutique($id, $id_utilisateur) {
        try {
            // First, delete all vehicles associated with this boutique
            $sqlDeleteVehicles = "DELETE FROM vehicule WHERE id_boutique = :id";
            $stmtDeleteVehicles = $this->pdo->prepare($sqlDeleteVehicles);
            $stmtDeleteVehicles->execute([':id' => $id]);

            // Then, delete the boutique
            $sql = "DELETE FROM boutique WHERE id_boutique = :id AND id_utilisateur = :id_utilisateur";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id, ':id_utilisateur' => $id_utilisateur]);
            return ['success' => true, 'message' => 'Boutique et ses véhicules ont été supprimés avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()];
        }
    }

    public function countBoutiques() {
        try {
            $sql = "SELECT COUNT(*) AS total FROM boutique WHERE statut = 'actif'";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return ['total' => 0];
        }
    }

    public function countBoutiquesByUser($id_utilisateur) {
        try {
            $sql = "SELECT COUNT(*) AS total FROM boutique WHERE id_utilisateur = :id_utilisateur";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_utilisateur' => $id_utilisateur]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return ['total' => 0];
        }
    }

    public function getBoutiquesPerMonth() {
        try {
            $sql = "SELECT MONTH(date_creation) AS month, COUNT(*) AS total
                    FROM boutique
                    GROUP BY MONTH(date_creation)
                    ORDER BY MONTH(date_creation)";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
