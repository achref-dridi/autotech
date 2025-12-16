<?php
$pageTitle = 'Gestion des Rendez-vous';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Config::getConnexion();
$message = '';
$messageType = '';

if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (deleteRecord('rendez_vous', 'id_rdv', $_GET['id'], $pdo)) {
        $message = 'Rendez-vous supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id_technicien = $_POST['id_technicien'] ?? null;
    $id_utilisateur = $_POST['id_utilisateur'] ?? null;
    $date_rdv = $_POST['date_rdv'] ?? '';
    $type_intervention = $_POST['type_intervention'] ?? '';
    $commentaire = $_POST['commentaire'] ?? '';
    $statut = $_POST['statut'] ?? 'en attente';
    
    try {
        if ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $sql = "UPDATE rendez_vous SET id_technicien = :id_technicien, id_utilisateur = :id_utilisateur,
                    date_rdv = :date_rdv, type_intervention = :type_intervention,
                    commentaire = :commentaire, statut = :statut
                    WHERE id_rdv = :id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_technicien' => $id_technicien, ':id_utilisateur' => $id_utilisateur,
                ':date_rdv' => $date_rdv, ':type_intervention' => $type_intervention,
                ':commentaire' => $commentaire, ':statut' => $statut, ':id' => $id
            ])) {
                $message = 'Rendez-vous mis à jour avec succès.';
                $messageType = 'success';
            }
        } else {
            $sql = "INSERT INTO rendez_vous (id_technicien, id_utilisateur, date_rdv, type_intervention, commentaire, statut)
                    VALUES (:id_technicien, :id_utilisateur, :date_rdv, :type_intervention, :commentaire, :statut)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_technicien' => $id_technicien, ':id_utilisateur' => $id_utilisateur,
                ':date_rdv' => $date_rdv, ':type_intervention' => $type_intervention,
                ':commentaire' => $commentaire, ':statut' => $statut
            ])) {
                $message = 'Rendez-vous ajouté avec succès.';
                $messageType = 'success';
            }
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

$rdvs = $pdo->query("
    SELECT r.*, t.nom as technicien_nom, u.nom as user_nom, u.prenom as user_prenom
    FROM rendez_vous r
    LEFT JOIN technicien t ON r.id_technicien = t.id_technicien
    LEFT JOIN utilisateur u ON r.id_utilisateur = u.id_utilisateur
    ORDER BY r.date_rdv DESC
")->fetchAll();

$techniciens = $pdo->query("SELECT id_technicien, nom FROM technicien ORDER BY nom")->fetchAll();
$users = $pdo->query("SELECT id_utilisateur, nom, prenom FROM utilisateur ORDER BY nom")->fetchAll();

$stats = [
    'total' => count($rdvs),
    'en_attente' => count(array_filter($rdvs, fn($r) => $r['statut'] === 'en attente')),
    'confirme' => count(array_filter($rdvs, fn($r) => $r['statut'] === 'confirme'))
];
?>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-calendar-alt me-3"></i>Gestion des Rendez-vous
        </h1>
        <p class="text-muted mb-0">Gérer tous les rendez-vous</p>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Ajouter un Rendez-vous
      </button>
    </div>
  </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
  <?= htmlspecialchars($message) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row mb-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-primary"><?= $stats['total'] ?></h3>
        <p class="text-muted mb-0">Total Rendez-vous</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-warning"><?= $stats['en_attente'] ?></h3>
        <p class="text-muted mb-0">En Attente</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-success"><?= $stats['confirme'] ?></h3>
        <p class="text-muted mb-0">Confirmés</p>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="rdvsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Date/Heure</th>
            <th>Technicien</th>
            <th>Utilisateur</th>
            <th>Type Intervention</th>
            <th>Commentaire</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rdvs as $rdv): ?>
          <tr>
            <td><?= $rdv['id_rdv'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($rdv['date_rdv'])) ?></td>
            <td><?= htmlspecialchars($rdv['technicien_nom'] ?? '-') ?></td>
            <td><?= htmlspecialchars(($rdv['user_prenom'] ?? '') . ' ' . ($rdv['user_nom'] ?? '')) ?></td>
            <td><?= htmlspecialchars($rdv['type_intervention']) ?></td>
            <td><?= htmlspecialchars(substr($rdv['commentaire'] ?? '', 0, 50)) ?><?= strlen($rdv['commentaire'] ?? '') > 50 ? '...' : '' ?></td>
            <td>
              <span class="badge bg-<?= $rdv['statut'] === 'confirme' ? 'success' : ($rdv['statut'] === 'en attente' ? 'warning' : 'danger') ?>">
                <?= htmlspecialchars($rdv['statut']) ?>
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="editRdv(<?= htmlspecialchars(json_encode($rdv)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <a href="?delete=1&id=<?= $rdv['id_rdv'] ?>" 
                 class="btn btn-sm btn-danger" 
                 onclick="return confirm('Êtes-vous sûr?')">
                <i class="fas fa-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Ajouter un Rendez-vous</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="rdvId">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Technicien *</label>
              <select class="form-select" name="id_technicien" id="id_technicien" required>
                <option value="">Sélectionner...</option>
                <?php foreach ($techniciens as $tech): ?>
                <option value="<?= $tech['id_technicien'] ?>"><?= htmlspecialchars($tech['nom']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Utilisateur *</label>
              <select class="form-select" name="id_utilisateur" id="id_utilisateur" required>
                <option value="">Sélectionner...</option>
                <?php foreach ($users as $user): ?>
                <option value="<?= $user['id_utilisateur'] ?>">
                  <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Date/Heure *</label>
              <input type="datetime-local" class="form-control" name="date_rdv" id="date_rdv" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Type Intervention *</label>
              <input type="text" class="form-control" name="type_intervention" id="type_intervention" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Statut</label>
              <select class="form-select" name="statut" id="statut">
                <option value="en attente">En attente</option>
                <option value="confirme">Confirmé</option>
                <option value="annule">Annulé</option>
              </select>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Commentaire</label>
              <textarea class="form-control" name="commentaire" id="commentaire" rows="3"></textarea>
            </div>
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
function editRdv(rdv) {
  document.getElementById('modalTitle').textContent = 'Modifier le Rendez-vous';
  document.getElementById('formAction').value = 'update';
  document.getElementById('rdvId').value = rdv.id_rdv;
  document.getElementById('id_technicien').value = rdv.id_technicien || '';
  document.getElementById('id_utilisateur').value = rdv.id_utilisateur || '';
  document.getElementById('date_rdv').value = rdv.date_rdv ? rdv.date_rdv.replace(' ', 'T').substring(0, 16) : '';
  document.getElementById('type_intervention').value = rdv.type_intervention || '';
  document.getElementById('commentaire').value = rdv.commentaire || '';
  document.getElementById('statut').value = rdv.statut || 'en attente';
  new bootstrap.Modal(document.getElementById('addModal')).show();
}

document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
  document.querySelector('#addModal form').reset();
  document.getElementById('modalTitle').textContent = 'Ajouter un Rendez-vous';
  document.getElementById('formAction').value = 'add';
  document.getElementById('rdvId').value = '';
});

$(document).ready(function() {
  $('#rdvsTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
    order: [[1, 'desc']]
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

