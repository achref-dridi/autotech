<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/Vehicule.php';

class VehiculeController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Config::getConnexion();
    }
    

    public function createVehicule($id_utilisateur, $marque, $modele, $annee, $carburant, $kilometrage, 
                                   $couleur, $transmission, $prix_journalier, $description, $image_principale) {
        try {
            $sql = "INSERT INTO vehicule 
                    (id_utilisateur, marque, modele, annee, carburant, kilometrage, couleur, transmission, 
                     prix_journalier, description, image_principale)
                    VALUES (:id_utilisateur, :marque, :modele, :annee, :carburant, :kilometrage, :couleur, 
                            :transmission, :prix_journalier, :description, :image_principale)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_utilisateur' => $id_utilisateur,
                ':marque' => $marque,
                ':modele' => $modele,
                ':annee' => $annee,
                ':carburant' => $carburant,
                ':kilometrage' => $kilometrage,
                ':couleur' => $couleur,
                ':transmission' => $transmission,
                ':prix_journalier' => $prix_journalier,
                ':description' => $description,
                ':image_principale' => $image_principale
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la création du véhicule: ' . $e->getMessage());
        }
    }
    

    public function getVehiculeById($id_vehicule) {
        $sql = "SELECT v.*, u.nom, u.prenom, u.email, u.telephone, u.ville
                FROM vehicule v
                LEFT JOIN utilisateur u ON v.id_utilisateur = u.id_utilisateur
                WHERE v.id_vehicule = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_vehicule]);
        return $stmt->fetch();
    }

    public function getAllVehicules() {
        $sql = "SELECT v.*, u.nom, u.prenom, u.email, u.telephone, u.ville
                FROM vehicule v
                LEFT JOIN utilisateur u ON v.id_utilisateur = u.id_utilisateur
                ORDER BY v.created_at DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getVehiculesByUtilisateur($id_utilisateur) {
        $sql = "SELECT v.*, u.nom, u.prenom
                FROM vehicule v
                LEFT JOIN utilisateur u ON v.id_utilisateur = u.id_utilisateur
                WHERE v.id_utilisateur = :id_utilisateur
                ORDER BY v.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        return $stmt->fetchAll();
    }
    

    public function updateVehicule($id_vehicule, $marque, $modele, $annee, $carburant, $kilometrage, 
                                   $couleur, $transmission, $prix_journalier, $description, $image_principale = null) {
        try {
            $sql = "UPDATE vehicule SET
                        marque = :marque,
                        modele = :modele,
                        annee = :annee,
                        carburant = :carburant,
                        kilometrage = :kilometrage,
                        couleur = :couleur,
                        transmission = :transmission,
                        prix_journalier = :prix_journalier,
                        description = :description";
            
            $params = [
                ':marque' => $marque,
                ':modele' => $modele,
                ':annee' => $annee,
                ':carburant' => $carburant,
                ':kilometrage' => $kilometrage,
                ':couleur' => $couleur,
                ':transmission' => $transmission,
                ':prix_journalier' => $prix_journalier,
                ':description' => $description,
                ':id' => $id_vehicule
            ];
            
            if ($image_principale !== null) {
                $sql .= ", image_principale = :image_principale";
                $params[':image_principale'] = $image_principale;
            }
            
            $sql .= " WHERE id_vehicule = :id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la mise à jour du véhicule: ' . $e->getMessage());
        }
    }
    

    public function deleteVehicule($id_vehicule) {
        $sql = "DELETE FROM vehicule WHERE id_vehicule = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id_vehicule]);
    }

    public function estProprietaire($id_vehicule, $id_utilisateur) {
        $sql = "SELECT COUNT(*) FROM vehicule WHERE id_vehicule = :id_vehicule AND id_utilisateur = :id_utilisateur";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_vehicule' => $id_vehicule,
            ':id_utilisateur' => $id_utilisateur
        ]);
        return $stmt->fetchColumn() > 0;
    }
    

    public function rechercherVehicules($marque = null, $prix_max = null, $carburant = null) {
        $sql = "SELECT v.*, u.nom, u.prenom, u.email, u.telephone, u.ville
                FROM vehicule v
                LEFT JOIN utilisateur u ON v.id_utilisateur = u.id_utilisateur
                WHERE 1=1";
        
        $params = [];
        
        if ($marque) {
            $sql .= " AND v.marque LIKE :marque";
            $params[':marque'] = '%' . $marque . '%';
        }
        
        if ($prix_max) {
            $sql .= " AND v.prix_journalier <= :prix_max";
            $params[':prix_max'] = $prix_max;
        }
        
        if ($carburant) {
            $sql .= " AND v.carburant = :carburant";
            $params[':carburant'] = $carburant;
        }
        
        $sql .= " ORDER BY v.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
?>
