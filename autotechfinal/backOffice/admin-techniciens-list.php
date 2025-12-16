<?php
$page_title = "Gestion des Techniciens - Administration";
?>
<link rel="stylesheet" href="/AutoTech/assets/css/admin-techniciens-list.css">
<?php
include("partials/admin-header.php");

require_once "../../Controller/TechnicienController.php";
$ctrl = new TechnicienController();
$techniciens = $ctrl->liste();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $result = $ctrl->ajouter($_POST);
                if ($result === true) {
                    $message = "Technicien ajouté avec succès!";
                    $message_type = "success";
                    $techniciens = $ctrl->liste();
                } else {
                    $message = $result;
                    $message_type = "danger";
                }
                break;
                
            case 'edit':
                $result = $ctrl->modifier($_POST['id_technicien'], $_POST);
                if ($result === true) {
                    $message = "Technicien modifié avec succès!";
                    $message_type = "success";
                    $techniciens = $ctrl->liste();
                } else {
                    $message = $result;
                    $message_type = "danger";
                }
                break;
                
            case 'delete':
                if (isset($_POST['id_technicien'])) {
                    $result = $ctrl->supprimer($_POST['id_technicien']);
                    if ($result === true) {
                        $message = "Technicien supprimé avec succès!";
                        $message_type = "success";
                        $techniciens = $ctrl->liste();
                    } else {
                        $message = $result;
                        $message_type = "danger";
                    }
                }
                break;
        }
    }
}
?>

<section class="dashboard-section py-5 bg-light min-vh-100">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold text-primary mb-2">
                            <i class="fas fa-user-cog me-3"></i>Gestion des Techniciens
                        </h1>
                        <p class="text-muted mb-0">Gérez l'équipe des techniciens experts</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary fs-6">
                            <?= count($techniciens) ?> techniciens
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                        <i class="fas <?= $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Add Technician Button -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTechnicianModal">
                        <i class="fas fa-plus me-2"></i>Ajouter un Technicien
                    </button>
                    
                    <!-- Search -->
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un technicien...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Technicians Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 text-primary">
                            <i class="fas fa-list me-2"></i>Liste des Techniciens
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($techniciens)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">Aucun technicien trouvé</h4>
                                <p class="text-muted">Aucun technicien n'est actuellement enregistré.</p>
                                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addTechnicianModal">
                                    <i class="fas fa-plus me-2"></i>Ajouter le premier technicien
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="techniciansTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">#ID</th>
                                            <th>Nom</th>
                                            <th>Spécialité</th>
                                            <th>Téléphone</th>
                                            <th>Email</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($techniciens as $t):
                                            if (is_object($t)) {
                                                $id = $t->id_technicien ?? '';
                                                $nom = $t->nom ?? '';
                                                $specialite = $t->specialite ?? '';
                                                $telephone = $t->telephone ?? '';
                                                $email = $t->email ?? '';
                                            } else {
                                                $id = $t['id_technicien'] ?? '';
                                                $nom = $t['nom'] ?? '';
                                                $specialite = $t['specialite'] ?? '';
                                                $telephone = $t['telephone'] ?? '';
                                                $email = $t['email'] ?? '';
                                            }
                                        ?>
                                        <tr class="technician-row">
                                            <td class="ps-4 fw-bold text-primary">#<?= $id ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user-cog text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <strong><?= htmlspecialchars($nom) ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?= htmlspecialchars($specialite) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($telephone) ?></td>
                                            <td>
                                                <?php if (!empty($email)): ?>
                                                    <a href="mailto:<?= htmlspecialchars($email) ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($email) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted fst-italic">Non renseigné</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <!-- Edit Button -->
                                                    <button class="btn btn-sm btn-outline-primary edit-technician" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editTechnicianModal"
                                                            data-id="<?= $id ?>"
                                                            data-nom="<?= htmlspecialchars($nom) ?>"
                                                            data-specialite="<?= htmlspecialchars($specialite) ?>"
                                                            data-telephone="<?= htmlspecialchars($telephone) ?>"
                                                            data-email="<?= htmlspecialchars($email) ?>">
                                                        <i class="fas fa-edit me-1"></i>Modifier
                                                    </button>
                                                    
                                                    <!-- Delete Button -->
                                                    <button class="btn btn-sm btn-outline-danger delete-technician" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteTechnicianModal"
                                                            data-id="<?= $id ?>"
                                                            data-nom="<?= htmlspecialchars($nom) ?>">
                                                        <i class="fas fa-trash me-1"></i>Supprimer
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Table Footer -->
                    <?php if (!empty($techniciens)): ?>
                    <div class="card-footer bg-light py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    Affichage de <strong><?= count($techniciens) ?></strong> techniciens
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="dashboard.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i>Retour au tableau de bord
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Technician Modal -->
<div class="modal fade" id="addTechnicianModal" tabindex="-1" aria-labelledby="addTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addTechnicianModalLabel">
                    <i class="fas fa-plus me-2"></i>Ajouter un Technicien
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="addTechnicianForm">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nom" class="form-label fw-bold">Nom complet *</label>
                            <input type="text" class="form-control" id="nom" name="nom" 
                                   placeholder="Ex: Jean Dupont">
                            <div class="invalid-feedback">Veuillez saisir le nom du technicien.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="specialite" class="form-label fw-bold">Spécialité *</label>
                            <input type="text" class="form-control" id="specialite" name="specialite" 
                                   placeholder="Ex: Mécanique générale, Électronique...">
                            <div class="invalid-feedback">Veuillez saisir la spécialité du technicien.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="telephone" class="form-label fw-bold">Téléphone *</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" 
                                   placeholder="Ex: 06 12 34 56 78">
                            <div class="invalid-feedback">Veuillez saisir un numéro de téléphone valide.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Ex: technicien@example.com">
                            <div class="invalid-feedback">Veuillez saisir une adresse email valide.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="addSubmitBtn">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Technician Modal -->
<div class="modal fade" id="editTechnicianModal" tabindex="-1" aria-labelledby="editTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editTechnicianModalLabel">
                    <i class="fas fa-edit me-2"></i>Modifier le Technicien
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editTechnicianForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_technicien" id="edit_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_nom" class="form-label fw-bold">Nom complet *</label>
                            <input type="text" class="form-control" id="edit_nom" name="nom">
                            <div class="invalid-feedback">Veuillez saisir le nom du technicien.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_specialite" class="form-label fw-bold">Spécialité *</label>
                            <input type="text" class="form-control" id="edit_specialite" name="specialite">
                            <div class="invalid-feedback">Veuillez saisir la spécialité du technicien.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_telephone" class="form-label fw-bold">Téléphone *</label>
                            <input type="tel" class="form-control" id="edit_telephone" name="telephone">
                            <div class="invalid-feedback">Veuillez saisir un numéro de téléphone valide.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email">
                            <div class="invalid-feedback">Veuillez saisir une adresse email valide.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteTechnicianModal" tabindex="-1" aria-labelledby="deleteTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteTechnicianModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmation de suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="deleteTechnicianForm">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id_technicien" id="delete_id">
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer le technicien <strong id="delete_nom"></strong> ?</p>
                    <p class="text-danger small">
                        <i class="fas fa-info-circle me-1"></i>
                        Cette action est irréversible. Tous les rendez-vous associés à ce technicien seront également supprimés.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger" id="deleteSubmitBtn">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/AutoTech/assets/js/admin-techniciens-list.js"></script>

<?php include("partials/admin-footer.php"); ?>