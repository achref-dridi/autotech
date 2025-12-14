<?php
require_once __DIR__ . '/../config/config.php';

class VehiculeController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
    }

    // CREATE
    public function createVehicule($marque, $modele, $annee, $carburant, $kilometrage, $couleur, $transmission, $image_principale)
    {
        $sql = "INSERT INTO vehicule 
                (marque, modele, annee, carburant, kilometrage, couleur, transmission, image_principale)
                VALUES (:marque, :modele, :annee, :carburant, :kilometrage, :couleur, :transmission, :image_principale)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':marque'           => $marque,
            ':modele'           => $modele,
            ':annee'            => $annee,
            ':carburant'        => $carburant,
            ':kilometrage'      => $kilometrage,
            ':couleur'          => $couleur,
            ':transmission'     => $transmission,
            ':image_principale' => $image_principale
        ]);

        return $this->pdo->lastInsertId();
    }

    // READ - un véhicule
    public function getVehiculeById($id_vehicule)
    {
        $sql = "SELECT * FROM vehicule WHERE id_vehicule = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_vehicule]);
        return $stmt->fetch(); // array assoc ou false
    }

    // READ - tous les véhicules
    public function getAllVehicules()
    {
        $sql = "SELECT * FROM vehicule ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // UPDATE
    public function updateVehicule($id_vehicule, $marque, $modele, $annee, $carburant, $kilometrage, $couleur, $transmission, $image_principale)
    {
        $sql = "UPDATE vehicule SET
                    marque = :marque,
                    modele = :modele,
                    annee = :annee,
                    carburant = :carburant,
                    kilometrage = :kilometrage,
                    couleur = :couleur,
                    transmission = :transmission,
                    image_principale = :image_principale
                WHERE id_vehicule = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':marque'           => $marque,
            ':modele'           => $modele,
            ':annee'            => $annee,
            ':carburant'        => $carburant,
            ':kilometrage'      => $kilometrage,
            ':couleur'          => $couleur,
            ':transmission'     => $transmission,
            ':image_principale' => $image_principale,
            ':id'               => $id_vehicule
        ]);
    }

    // DELETE
    public function deleteVehicule($id_vehicule)
    {
        $sql = "DELETE FROM vehicule WHERE id_vehicule = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id_vehicule]);
    }
}  