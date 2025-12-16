<?php
require_once __DIR__ . '/../config/config.php';

class SignalementController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function ajouterSignalement($id_utilisateur, $data) {
        try {
            $sql = "INSERT INTO signalement (id_utilisateur, type_objet, id_objet, sujet, description, statut) 
                    VALUES (:id_utilisateur, :type_objet, :id_objet, :sujet, :description, 'en_attente')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_utilisateur' => $id_utilisateur,
                ':type_objet' => $data['type_objet'],
                ':id_objet' => !empty($data['id_objet']) ? $data['id_objet'] : null,
                ':sujet' => $data['sujet'],
                ':description' => $data['description']
            ]);
            return ['success' => true, 'message' => 'Signalement envoyé avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'envoi : ' . $e->getMessage()];
        }
    }

    public function getMesSignalements($id_utilisateur) {
        $sql = "SELECT * FROM signalement WHERE id_utilisateur = :id_utilisateur ORDER BY date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_utilisateur' => $id_utilisateur]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllSignalements() {
        $sql = "SELECT s.*, u.nom, u.prenom, u.email 
                FROM signalement s
                JOIN utilisateur u ON s.id_utilisateur = u.id_utilisateur
                ORDER BY 
                    CASE WHEN s.statut = 'en_attente' THEN 1 ELSE 2 END,
                    s.date_creation DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSignalementById($id) {
        $sql = "SELECT s.*, u.nom, u.prenom, u.email 
                FROM signalement s
                JOIN utilisateur u ON s.id_utilisateur = u.id_utilisateur
                WHERE s.id_signalement = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function repondreSignalement($id, $reponse, $statut, $file = null) {
        try {
            $params = [
                ':reponse' => $reponse,
                ':statut' => $statut,
                ':id' => $id
            ];
            
            $sql = "UPDATE signalement SET reponse_admin = :reponse, statut = :statut";
            
            if ($file) {
                $sql .= ", piece_jointe_admin = :file";
                $params[':file'] = $file;
            }
            
            $sql .= " WHERE id_signalement = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return ['success' => true, 'message' => 'Réponse envoyée avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
}
