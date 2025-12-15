<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/RendezVous.php';

class RendezVousController {
    private $pdo;
    private $googleCalendar;

    public function __construct() {
        $this->pdo = Config::getConnexion();
        // Google Calendar is optional
        if (file_exists(__DIR__ . '/GoogleCalendarService.php')) {
            require_once __DIR__ . '/GoogleCalendarService.php';
            $this->googleCalendar = new GoogleCalendarService();
        } else {
            $this->googleCalendar = null;
        }
    }

    public function addRendezVous($rendezVous) {
        try {
            // Start transaction
            $this->pdo->beginTransaction();

            // Insert into database
            $sql = "INSERT INTO rendez_vous (id_technicien, id_utilisateur, date_rdv, type_intervention, commentaire, statut)
                    VALUES (:id_tech, :id_user, :date_rdv, :type_int, :comment, :statut)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_tech' => $rendezVous->getIdTechnicien(),
                ':id_user' => $rendezVous->getIdUtilisateur(),
                ':date_rdv' => $rendezVous->getDateRdv(),
                ':type_int' => $rendezVous->getTypeIntervention(),
                ':comment' => $rendezVous->getCommentaire(),
                ':statut' => $rendezVous->getStatut()
            ]);

            $rdvId = $this->pdo->lastInsertId();
            $rendezVous->setIdRdv($rdvId);

            // Try to sync with Google Calendar (optional)
            if ($this->googleCalendar) {
                try {
                    $googleEventId = $this->googleCalendar->createEvent($rendezVous);
                    if ($googleEventId) {
                        // Update with Google Event ID
                        $updateSql = "UPDATE rendez_vous SET google_event_id = :event_id WHERE id_rdv = :id";
                        $updateStmt = $this->pdo->prepare($updateSql);
                        $updateStmt->execute([':event_id' => $googleEventId, ':id' => $rdvId]);
                        $rendezVous->setGoogleEventId($googleEventId);
                    }
                } catch (Exception $e) {
                    // Log error but don't fail the entire operation
                    error_log("Google Calendar sync failed: " . $e->getMessage());
                }
            }

            $this->pdo->commit();

            return ['success' => true, 'message' => 'Rendez-vous créé avec succès.', 'id' => $rdvId];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Erreur lors de la création: ' . $e->getMessage()];
        }
    }

    public function getRendezVousById($id) {
        try {
            $sql = "SELECT r.*, t.nom as technicien_nom, t.specialite, t.telephone, u.nom as utilisateur_nom
                    FROM rendez_vous r
                    JOIN technicien t ON r.id_technicien = t.id_technicien
                    JOIN utilisateur u ON r.id_utilisateur = u.id_utilisateur
                    WHERE r.id_rdv = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getRendezVousByUtilisateur($id_user) {
        try {
            $sql = "SELECT r.*, t.nom as technicien_nom, t.specialite, t.telephone
                    FROM rendez_vous r
                    JOIN technicien t ON r.id_technicien = t.id_technicien
                    WHERE r.id_utilisateur = :id_user
                    ORDER BY r.date_rdv DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_user' => $id_user]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getRendezVousByTechnicien($id_tech) {
        try {
            $sql = "SELECT r.*, u.nom as utilisateur_nom, u.prenom, u.email
                    FROM rendez_vous r
                    JOIN utilisateur u ON r.id_utilisateur = u.id_utilisateur
                    WHERE r.id_technicien = :id_tech
                    ORDER BY r.date_rdv DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_tech' => $id_tech]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateRendezVous($id, $rendezVous) {
        try {
            $this->pdo->beginTransaction();

            $sql = "UPDATE rendez_vous SET id_technicien = :id_tech, date_rdv = :date_rdv, 
                    type_intervention = :type_int, commentaire = :comment, statut = :statut
                    WHERE id_rdv = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_tech' => $rendezVous->getIdTechnicien(),
                ':date_rdv' => $rendezVous->getDateRdv(),
                ':type_int' => $rendezVous->getTypeIntervention(),
                ':comment' => $rendezVous->getCommentaire(),
                ':statut' => $rendezVous->getStatut(),
                ':id' => $id
            ]);

            // Get the current Google Event ID
            $currentRdv = $this->getRendezVousById($id);
            if ($this->googleCalendar && $currentRdv && $currentRdv['google_event_id']) {
                // Update in Google Calendar
                try {
                    $this->googleCalendar->updateEvent($currentRdv['google_event_id'], $rendezVous);
                } catch (Exception $e) {
                    error_log("Google Calendar update failed: " . $e->getMessage());
                }
            }

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Rendez-vous mis à jour avec succès.'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()];
        }
    }

    public function deleteRendezVous($id) {
        try {
            $this->pdo->beginTransaction();

            // Get the Google Event ID
            $rdv = $this->getRendezVousById($id);

            // Delete from database
            $sql = "DELETE FROM rendez_vous WHERE id_rdv = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            // Delete from Google Calendar (if available)
            if ($this->googleCalendar && $rdv && $rdv['google_event_id']) {
                try {
                    $this->googleCalendar->deleteEvent($rdv['google_event_id']);
                } catch (Exception $e) {
                    error_log("Google Calendar delete failed: " . $e->getMessage());
                }
            }

            $this->pdo->commit();
            return ['success' => true, 'message' => 'Rendez-vous supprimé avec succès.'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()];
        }
    }

    public function updateStatus($id, $status) {
        try {
            $allowed_status = ['en attente', 'confirme', 'annule'];
            if (!in_array($status, $allowed_status)) {
                return ['success' => false, 'message' => 'Statut invalide'];
            }

            $sql = "UPDATE rendez_vous SET statut = :statut WHERE id_rdv = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':statut' => $status, ':id' => $id]);

            return ['success' => true, 'message' => 'Statut mis à jour avec succès.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()];
        }
    }
}
?>
