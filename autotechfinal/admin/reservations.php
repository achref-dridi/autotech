<?php
$pageTitle = 'Gestion des Réservations';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Config::getConnexion();
$message = '';
$messageType = '';

if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (deleteRecord('reservation', 'id_reservation', $_GET['id'], $pdo)) {
        $message = 'Réservation supprimée avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id_vehicule = $_POST['id_vehicule'] ?? null;
    $id_utilisateur = $_POST['id_utilisateur'] ?? null;
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $statut = $_POST['statut'] ?? 'en attente';
    $prix_total = $_POST['prix_total'] ?? null;
    
    try {
        if ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $sql = "UPDATE reservation SET id_vehicule = :id_vehicule, id_utilisateur = :id_utilisateur,
                    date_debut = :date_debut, date_fin = :date_fin, statut = :statut, prix_total = :prix_total
                    WHERE id_reservation = :id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_vehicule' => $id_vehicule, ':id_utilisateur' => $id_utilisateur,
                ':date_debut' => $date_debut, ':date_fin' => $date_fin,
                ':statut' => $statut, ':prix_total' => $prix_total, ':id' => $id
            ])) {
                $message = 'Réservation mise à jour avec succès.';
                $messageType = 'success';
            }
        } else {
            $sql = "INSERT INTO reservation (id_vehicule, id_utilisateur, date_debut, date_fin, statut, prix_total)
                    VALUES (:id_vehicule, :id_utilisateur, :date_debut, :date_fin, :statut, :prix_total)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_vehicule' => $id_vehicule, ':id_utilisateur' => $id_utilisateur,
                ':date_debut' => $date_debut, ':date_fin' => $date_fin,
                ':statut' => $statut, ':prix_total' => $prix_total
            ])) {
                $message = 'Réservation ajoutée avec succès.';
                $messageType = 'success';
            }
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

$reservations = $pdo->query("
    SELECT r.*, v.marque, v.modele, u.nom as user_nom, u.prenom as user_prenom
    FROM reservation r
    LEFT JOIN vehicule v ON r.id_vehicule = v.id_vehicule
    LEFT JOIN utilisateur u ON r.id_utilisateur = u.id_utilisateur
    ORDER BY r.date_debut DESC
")->fetchAll();

$vehicles = $pdo->query("SELECT id_vehicule, marque, modele FROM vehicule ORDER BY marque")->fetchAll();
$users = $pdo->query("SELECT id_utilisateur, nom, prenom FROM utilisateur ORDER BY nom")->fetchAll();

$stats = [
    'total' => count($reservations),
    'en_attente' => count(array_filter($reservations, fn($r) => $r['statut'] === 'en attente'))
];
?>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-calendar-check me-3"></i>Gestion des Réservations
        </h1>
        <p class="text-muted mb-0">Gérer toutes les réservations de véhicules</p>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Ajouter une Réservation
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
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-primary"><?= $stats['total'] ?></h3>
        <p class="text-muted mb-0">Total Réservations</p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-warning"><?= $stats['en_attente'] ?></h3>
        <p class="text-muted mb-0">En Attente</p>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="reservationsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Véhicule</th>
            <th>Utilisateur</th>
            <th>Date Début</th>
            <th>Date Fin</th>
            <th>Prix Total</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservations as $res): ?>
          <tr>
            <td><?= $res['id_reservation'] ?></td>
            <td><?= htmlspecialchars($res['marque'] . ' ' . $res['modele']) ?></td>
            <td><?= htmlspecialchars(($res['user_prenom'] ?? '') . ' ' . ($res['user_nom'] ?? '')) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($res['date_debut'])) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($res['date_fin'])) ?></td>
            <td><?= $res['prix_total'] ? number_format($res['prix_total'], 2) . ' TND' : '-' ?></td>
            <td>
              <span class="badge bg-<?= $res['statut'] === 'confirmee' ? 'success' : ($res['statut'] === 'en attente' ? 'warning' : 'danger') ?>">
                <?= htmlspecialchars($res['statut']) ?>
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="editReservation(<?= htmlspecialchars(json_encode($res)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <a href="?delete=1&id=<?= $res['id_reservation'] ?>" 
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
        <h5 class="modal-title" id="modalTitle">Ajouter une Réservation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="reservationId">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Véhicule *</label>
              <select class="form-select" name="id_vehicule" id="id_vehicule" required>
                <option value="">Sélectionner...</option>
                <?php foreach ($vehicles as $v): ?>
                <option value="<?= $v['id_vehicule'] ?>">
                  <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
                </option>
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
              <label class="form-label">Date Début *</label>
              <input type="datetime-local" class="form-control" name="date_debut" id="date_debut" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Date Fin *</label>
              <input type="datetime-local" class="form-control" name="date_fin" id="date_fin" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Prix Total (TND)</label>
              <input type="number" step="0.01" class="form-control" name="prix_total" id="prix_total">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Statut</label>
              <select class="form-select" name="statut" id="statut">
                <option value="en attente">En attente</option>
                <option value="confirmee">Confirmée</option>
                <option value="rejetee">Rejetée</option>
                <option value="annulee">Annulée</option>
              </select>
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
function editReservation(res) {
  document.getElementById('modalTitle').textContent = 'Modifier la Réservation';
  document.getElementById('formAction').value = 'update';
  document.getElementById('reservationId').value = res.id_reservation;
  document.getElementById('id_vehicule').value = res.id_vehicule || '';
  document.getElementById('id_utilisateur').value = res.id_utilisateur || '';
  document.getElementById('date_debut').value = res.date_debut ? res.date_debut.replace(' ', 'T').substring(0, 16) : '';
  document.getElementById('date_fin').value = res.date_fin ? res.date_fin.replace(' ', 'T').substring(0, 16) : '';
  document.getElementById('prix_total').value = res.prix_total || '';
  document.getElementById('statut').value = res.statut || 'en attente';
  new bootstrap.Modal(document.getElementById('addModal')).show();
}

document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
  document.querySelector('#addModal form').reset();
  document.getElementById('modalTitle').textContent = 'Ajouter une Réservation';
  document.getElementById('formAction').value = 'add';
  document.getElementById('reservationId').value = '';
});

$(document).ready(function() {
  $('#reservationsTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
    order: [[3, 'desc']]
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

