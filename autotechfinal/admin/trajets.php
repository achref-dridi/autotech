<?php
$pageTitle = 'Gestion des Trajets';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Config::getConnexion();
$message = '';
$messageType = '';

if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (deleteRecord('trajet', 'id_trajet', $_GET['id'], $pdo)) {
        $message = 'Trajet supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id_utilisateur = $_POST['id_utilisateur'] ?? null;
    $lieu_depart = $_POST['lieu_depart'] ?? '';
    $lieu_arrivee = $_POST['lieu_arrivee'] ?? '';
    $date_depart = $_POST['date_depart'] ?? '';
    $duree_minutes = $_POST['duree_minutes'] ?? 0;
    $prix = $_POST['prix'] ?? 0;
    $description = $_POST['description'] ?? '';
    $places_disponibles = $_POST['places_disponibles'] ?? 1;
    $statut = $_POST['statut'] ?? 'disponible';
    
    try {
        if ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $sql = "UPDATE trajet SET id_utilisateur = :id_utilisateur, lieu_depart = :lieu_depart,
                    lieu_arrivee = :lieu_arrivee, date_depart = :date_depart, duree_minutes = :duree_minutes,
                    prix = :prix, description = :description, places_disponibles = :places_disponibles, statut = :statut
                    WHERE id_trajet = :id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_utilisateur' => $id_utilisateur, ':lieu_depart' => $lieu_depart,
                ':lieu_arrivee' => $lieu_arrivee, ':date_depart' => $date_depart,
                ':duree_minutes' => $duree_minutes, ':prix' => $prix,
                ':description' => $description, ':places_disponibles' => $places_disponibles,
                ':statut' => $statut, ':id' => $id
            ])) {
                $message = 'Trajet mis à jour avec succès.';
                $messageType = 'success';
            }
        } else {
            $sql = "INSERT INTO trajet (id_utilisateur, lieu_depart, lieu_arrivee, date_depart, duree_minutes, prix, description, places_disponibles, statut)
                    VALUES (:id_utilisateur, :lieu_depart, :lieu_arrivee, :date_depart, :duree_minutes, :prix, :description, :places_disponibles, :statut)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_utilisateur' => $id_utilisateur, ':lieu_depart' => $lieu_depart,
                ':lieu_arrivee' => $lieu_arrivee, ':date_depart' => $date_depart,
                ':duree_minutes' => $duree_minutes, ':prix' => $prix,
                ':description' => $description, ':places_disponibles' => $places_disponibles,
                ':statut' => $statut
            ])) {
                $message = 'Trajet ajouté avec succès.';
                $messageType = 'success';
            }
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

$trajets = $pdo->query("
    SELECT t.*, u.nom as user_nom, u.prenom as user_prenom,
           (SELECT COUNT(*) FROM reservation_trajet WHERE id_trajet = t.id_trajet) as nb_reservations
    FROM trajet t
    LEFT JOIN utilisateur u ON t.id_utilisateur = u.id_utilisateur
    ORDER BY t.date_depart DESC
")->fetchAll();

$users = $pdo->query("SELECT id_utilisateur, nom, prenom FROM utilisateur ORDER BY nom")->fetchAll();

$stats = [
    'total' => count($trajets),
    'disponibles' => count(array_filter($trajets, fn($t) => $t['statut'] === 'disponible'))
];
?>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-route me-3"></i>Gestion des Trajets
        </h1>
        <p class="text-muted mb-0">Gérer tous les trajets</p>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Ajouter un Trajet
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
        <p class="text-muted mb-0">Total Trajets</p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-success"><?= $stats['disponibles'] ?></h3>
        <p class="text-muted mb-0">Disponibles</p>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="trajetsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Conducteur</th>
            <th>Départ</th>
            <th>Arrivée</th>
            <th>Date Départ</th>
            <th>Durée</th>
            <th>Prix</th>
            <th>Places</th>
            <th>Réservations</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($trajets as $trajet): ?>
          <tr>
            <td><?= $trajet['id_trajet'] ?></td>
            <td><?= htmlspecialchars(($trajet['user_prenom'] ?? '') . ' ' . ($trajet['user_nom'] ?? '')) ?></td>
            <td><?= htmlspecialchars($trajet['lieu_depart']) ?></td>
            <td><?= htmlspecialchars($trajet['lieu_arrivee']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($trajet['date_depart'])) ?></td>
            <td><?= $trajet['duree_minutes'] ?> min</td>
            <td><?= number_format($trajet['prix'], 2) ?> TND</td>
            <td><?= $trajet['places_disponibles'] ?></td>
            <td><span class="badge bg-info"><?= $trajet['nb_reservations'] ?></span></td>
            <td>
              <span class="badge bg-<?= $trajet['statut'] === 'disponible' ? 'success' : ($trajet['statut'] === 'complet' ? 'warning' : 'secondary') ?>">
                <?= htmlspecialchars($trajet['statut']) ?>
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="editTrajet(<?= htmlspecialchars(json_encode($trajet)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <a href="?delete=1&id=<?= $trajet['id_trajet'] ?>" 
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
        <h5 class="modal-title" id="modalTitle">Ajouter un Trajet</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="trajetId">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Conducteur *</label>
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
              <label class="form-label">Lieu Départ *</label>
              <input type="text" class="form-control" name="lieu_depart" id="lieu_depart" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Lieu Arrivée *</label>
              <input type="text" class="form-control" name="lieu_arrivee" id="lieu_arrivee" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Date Départ *</label>
              <input type="datetime-local" class="form-control" name="date_depart" id="date_depart" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Durée (minutes) *</label>
              <input type="number" class="form-control" name="duree_minutes" id="duree_minutes" min="1" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Prix (TND) *</label>
              <input type="number" step="0.01" class="form-control" name="prix" id="prix" min="0" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Places Disponibles *</label>
              <input type="number" class="form-control" name="places_disponibles" id="places_disponibles" min="1" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Statut</label>
              <select class="form-select" name="statut" id="statut">
                <option value="disponible">Disponible</option>
                <option value="complet">Complet</option>
                <option value="termine">Terminé</option>
                <option value="annule">Annulé</option>
              </select>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" id="description" rows="3"></textarea>
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
function editTrajet(trajet) {
  document.getElementById('modalTitle').textContent = 'Modifier le Trajet';
  document.getElementById('formAction').value = 'update';
  document.getElementById('trajetId').value = trajet.id_trajet;
  document.getElementById('id_utilisateur').value = trajet.id_utilisateur || '';
  document.getElementById('lieu_depart').value = trajet.lieu_depart || '';
  document.getElementById('lieu_arrivee').value = trajet.lieu_arrivee || '';
  document.getElementById('date_depart').value = trajet.date_depart ? trajet.date_depart.replace(' ', 'T').substring(0, 16) : '';
  document.getElementById('duree_minutes').value = trajet.duree_minutes || '';
  document.getElementById('prix').value = trajet.prix || '';
  document.getElementById('places_disponibles').value = trajet.places_disponibles || '';
  document.getElementById('description').value = trajet.description || '';
  document.getElementById('statut').value = trajet.statut || 'disponible';
  new bootstrap.Modal(document.getElementById('addModal')).show();
}

document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
  document.querySelector('#addModal form').reset();
  document.getElementById('modalTitle').textContent = 'Ajouter un Trajet';
  document.getElementById('formAction').value = 'add';
  document.getElementById('trajetId').value = '';
});

$(document).ready(function() {
  $('#trajetsTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
    order: [[4, 'desc']]
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

