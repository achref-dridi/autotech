<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/Utilisateur.php';

class UtilisateurController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Config::getConnexion();
    }
    

    public function inscrire($nom, $prenom, $email, $mot_de_passe, $telephone = null) {
        try {
            if ($this->emailExiste($email)) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
            }
            
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, telephone) 
                    VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone)";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':mot_de_passe' => $mot_de_passe_hash,
                ':telephone' => $telephone
            ]);
            
            return ['success' => true, 'message' => 'Inscription réussie!', 'id' => $this->pdo->lastInsertId()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()];
        }
    }

    public function connecter($email, $mot_de_passe) {
        try {
            $sql = "SELECT * FROM utilisateur WHERE email = :email AND statut = 'actif'";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            
            $utilisateur = $stmt->fetch();
            
            if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
                $_SESSION['user_id'] = $utilisateur['id_utilisateur'];
                $_SESSION['user_nom'] = $utilisateur['nom'];
                $_SESSION['user_prenom'] = $utilisateur['prenom'];
                $_SESSION['user_email'] = $utilisateur['email'];
                $_SESSION['user_role'] = $utilisateur['role'];
                $_SESSION['logged_in'] = true;
                
                return ['success' => true, 'message' => 'Connexion réussie!', 'user' => $utilisateur];
            } else {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la connexion: ' . $e->getMessage()];
        }
    }

    public function deconnecter() {
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Déconnexion réussie.'];
    }

    public function estConnecte() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public function getUtilisateurConnecte() {
        if ($this->estConnecte()) {
            return $this->getUtilisateurById($_SESSION['user_id']);
        }
        return null;
    }

    public function getUtilisateurById($id) {
        $sql = "SELECT * FROM utilisateur WHERE id_utilisateur = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    

    public function updateProfil($id, $nom, $prenom, $telephone, $adresse, $ville, $code_postal, $photo_profil = null) {
        try {
            $sql = "UPDATE utilisateur SET 
                    nom = :nom, 
                    prenom = :prenom, 
                    telephone = :telephone,
                    adresse = :adresse,
                    ville = :ville,
                    code_postal = :code_postal";
            
            $params = [
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':telephone' => $telephone,
                ':adresse' => $adresse,
                ':ville' => $ville,
                ':code_postal' => $code_postal,
                ':id' => $id
            ];
            
            if ($photo_profil !== null) {
                $sql .= ", photo_profil = :photo_profil";
                $params[':photo_profil'] = $photo_profil;
            }
            
            $sql .= " WHERE id_utilisateur = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_prenom'] = $prenom;
            
            return ['success' => true, 'message' => 'Profil mis à jour avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()];
        }
    }

    public function changerMotDePasse($id, $ancien_mot_de_passe, $nouveau_mot_de_passe) {
        try {
            $utilisateur = $this->getUtilisateurById($id);
            
            if (!password_verify($ancien_mot_de_passe, $utilisateur['mot_de_passe'])) {
                return ['success' => false, 'message' => 'L\'ancien mot de passe est incorrect.'];
            }
            
            $nouveau_mot_de_passe_hash = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
            
            $sql = "UPDATE utilisateur SET mot_de_passe = :mot_de_passe WHERE id_utilisateur = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':mot_de_passe' => $nouveau_mot_de_passe_hash,
                ':id' => $id
            ]);
            
            return ['success' => true, 'message' => 'Mot de passe changé avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors du changement de mot de passe: ' . $e->getMessage()];
        }
    }
    

    private function emailExiste($email) {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function getAllUtilisateurs() {
        $sql = "SELECT * FROM utilisateur ORDER BY date_creation DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function getUserByEmail($email) {
        try {
            $sql = "SELECT * FROM utilisateur WHERE email = :email LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function saveResetToken($userId, $token, $expires) {
        try {
            $sql = "UPDATE utilisateur SET reset_token = :token, reset_expires = :expires WHERE id_utilisateur = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':token' => $token,
                ':expires' => $expires,
                ':id' => $userId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUserByResetToken($token) {
        try {
            $sql = "SELECT * FROM utilisateur WHERE reset_token = :token AND reset_expires > NOW() LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':token' => $token]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updatePasswordAndClearToken($userId, $hashedPassword) {
        try {
            $sql = "UPDATE utilisateur SET mot_de_passe = :password, reset_token = NULL, reset_expires = NULL WHERE id_utilisateur = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':password' => $hashedPassword,
                ':id' => $userId
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
