<?php
$pageTitle = 'Gestion des Réservations Trajets';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Config::getConnexion();
$message = '';
$messageType = '';

if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (deleteRecord('reservation_trajet', 'id_reservation_trajet', $_GET['id'], $pdo)) {
        $message = 'Réservation supprimée avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id_trajet = $_POST['id_trajet'] ?? null;
    $id_utilisateur = $_POST['id_utilisateur'] ?? null;
    $nombre_places = $_POST['nombre_places'] ?? 1;
    $statut = $_POST['statut'] ?? 'en attente';
    
    try {
        if ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $sql = "UPDATE reservation_trajet SET id_trajet = :id_trajet, id_utilisateur = :id_utilisateur,
                    nombre_places = :nombre_places, statut = :statut
                    WHERE id_reservation_trajet = :id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_trajet' => $id_trajet, ':id_utilisateur' => $id_utilisateur,
                ':nombre_places' => $nombre_places, ':statut' => $statut, ':id' => $id
            ])) {
                $message = 'Réservation mise à jour avec succès.';
                $messageType = 'success';
            }
        } else {
            $sql = "INSERT INTO reservation_trajet (id_trajet, id_utilisateur, nombre_places, statut)
                    VALUES (:id_trajet, :id_utilisateur, :nombre_places, :statut)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_trajet' => $id_trajet, ':id_utilisateur' => $id_utilisateur,
                ':nombre_places' => $nombre_places, ':statut' => $statut
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
    SELECT rt.*, t.lieu_depart, t.lieu_arrivee, t.date_depart, t.prix,
           u.nom as user_nom, u.prenom as user_prenom
    FROM reservation_trajet rt
    LEFT JOIN trajet t ON rt.id_trajet = t.id_trajet
    LEFT JOIN utilisateur u ON rt.id_utilisateur = u.id_utilisateur
    ORDER BY rt.date_creation DESC
")->fetchAll();

$trajets = $pdo->query("SELECT id_trajet, lieu_depart, lieu_arrivee, date_depart FROM trajet ORDER BY date_depart DESC")->fetchAll();
$users = $pdo->query("SELECT id_utilisateur, nom, prenom FROM utilisateur ORDER BY nom")->fetchAll();

$stats = [
    'total' => count($reservations),
    'en_attente' => count(array_filter($reservations, fn($r) => $r['statut'] === 'en attente')),
    'confirmee' => count(array_filter($reservations, fn($r) => $r['statut'] === 'confirmee'))
];
?>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-ticket-alt me-3"></i>Gestion des Réservations Trajets
        </h1>
        <p class="text-muted mb-0">Gérer toutes les réservations de trajets</p>
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
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-primary"><?= $stats['total'] ?></h3>
        <p class="text-muted mb-0">Total Réservations</p>
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
        <h3 class="text-success"><?= $stats['confirmee'] ?></h3>
        <p class="text-muted mb-0">Confirmées</p>
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
            <th>Trajet</th>
            <th>Utilisateur</th>
            <th>Date Trajet</th>
            <th>Nombre Places</th>
            <th>Prix Total</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservations as $res): ?>
          <tr>
            <td><?= $res['id_reservation_trajet'] ?></td>
            <td><?= htmlspecialchars($res['lieu_depart'] . ' → ' . $res['lieu_arrivee']) ?></td>
            <td><?= htmlspecialchars(($res['user_prenom'] ?? '') . ' ' . ($res['user_nom'] ?? '')) ?></td>
            <td><?= $res['date_depart'] ? date('d/m/Y H:i', strtotime($res['date_depart'])) : '-' ?></td>
            <td><?= $res['nombre_places'] ?></td>
            <td><?= $res['prix'] ? number_format($res['prix'] * $res['nombre_places'], 2) . ' TND' : '-' ?></td>
            <td>
              <span class="badge bg-<?= $res['statut'] === 'confirmee' ? 'success' : ($res['statut'] === 'en attente' ? 'warning' : ($res['statut'] === 'rejetee' ? 'danger' : 'secondary')) ?>">
                <?= htmlspecialchars($res['statut']) ?>
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="editReservation(<?= htmlspecialchars(json_encode($res)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <a href="?delete=1&id=<?= $res['id_reservation_trajet'] ?>" 
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
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Ajouter une Réservation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="reservationId">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Trajet *</label>
            <select class="form-select" name="id_trajet" id="id_trajet" required>
              <option value="">Sélectionner...</option>
              <?php foreach ($trajets as $t): ?>
              <option value="<?= $t['id_trajet'] ?>">
                <?= htmlspecialchars($t['lieu_depart'] . ' → ' . $t['lieu_arrivee'] . ' (' . date('d/m/Y H:i', strtotime($t['date_depart'])) . ')') ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
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
          <div class="mb-3">
            <label class="form-label">Nombre de Places *</label>
            <input type="number" class="form-control" name="nombre_places" id="nombre_places" min="1" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select class="form-select" name="statut" id="statut">
              <option value="en attente">En attente</option>
              <option value="confirmee">Confirmée</option>
              <option value="rejetee">Rejetée</option>
              <option value="annulee">Annulée</option>
            </select>
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
  document.getElementById('reservationId').value = res.id_reservation_trajet;
  document.getElementById('id_trajet').value = res.id_trajet || '';
  document.getElementById('id_utilisateur').value = res.id_utilisateur || '';
  document.getElementById('nombre_places').value = res.nombre_places || '';
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
    order: [[0, 'desc']]
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

