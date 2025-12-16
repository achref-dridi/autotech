<?php
$pageTitle = 'Gestion des Signalements';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../controller/SignalementController.php';

$controller = new SignalementController();
$message = '';
$messageType = '';

// Handle Reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'repondre') {
    $id = $_POST['id_signalement'];
    $reponse = $_POST['reponse_admin'];
    $statut = $_POST['statut'];
    $file = null;

    if (isset($_FILES['piece_jointe']) && $_FILES['piece_jointe']['error'] === 0) {
        $uploadDir = __DIR__ . '/../uploads/signalements/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $extension = pathinfo($_FILES['piece_jointe']['name'], PATHINFO_EXTENSION);
        $file = 'rep_' . $id . '_' . time() . '.' . $extension;
        move_uploaded_file($_FILES['piece_jointe']['tmp_name'], $uploadDir . $file);
    }

    $res = $controller->repondreSignalement($id, $reponse, $statut, $file);
    $message = $res['message'];
    $messageType = $res['success'] ? 'success' : 'danger';
}

$signalements = $controller->getAllSignalements();
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="display-6 fw-bold text-primary mb-2">
            <i class="fas fa-exclamation-circle me-3"></i>Gestion des Signalements
        </h1>
        <p class="text-muted mb-0">Traiter les signalements des utilisateurs</p>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="signalementsTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Sujet</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($signalements as $sig): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($sig['date_creation'])) ?></td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($sig['prenom'] . ' ' . $sig['nom']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($sig['email']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($sig['sujet']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= ucfirst($sig['type_objet']) ?></span>
                                <?php if ($sig['id_objet']): ?>
                                    <small class="text-muted">#<?= $sig['id_objet'] ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $badgeClass = 'secondary';
                                if ($sig['statut'] === 'traite') $badgeClass = 'success';
                                if ($sig['statut'] === 'ignore') $badgeClass = 'danger';
                                if ($sig['statut'] === 'en_attente') $badgeClass = 'warning';
                                ?>
                                <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $sig['statut'])) ?></span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="openReplyModal(<?= htmlspecialchars(json_encode($sig)) ?>)">
                                    <i class="fas fa-edit"></i> Traiter
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Traiter le signalement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="repondre">
                <input type="hidden" name="id_signalement" id="id_signalement">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description du problème :</label>
                        <div class="p-3 bg-light rounded" id="user_description"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select class="form-select" name="statut" id="statut">
                            <option value="en_attente">En attente</option>
                            <option value="traite">Traité</option>
                            <option value="ignore">Ignoré</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Réponse Admin</label>
                        <textarea class="form-control" name="reponse_admin" id="reponse_admin" rows="4" placeholder="Votre réponse à l'utilisateur..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pièce jointe (Optionnel)</label>
                        <input type="file" class="form-control" name="piece_jointe">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReplyModal(sig) {
    document.getElementById('id_signalement').value = sig.id_signalement;
    document.getElementById('user_description').textContent = sig.description;
    document.getElementById('reponse_admin').value = sig.reponse_admin || '';
    document.getElementById('statut').value = sig.statut;
    new bootstrap.Modal(document.getElementById('replyModal')).show();
}

$(document).ready(function() {
    $('#signalementsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
        order: [[0, 'desc']]
    });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
