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
    $budget = $_POST['budget'] ?? 0;
    $description = $_POST['description'] ?? '';
    $places_demandees = $_POST['places_demandees'] ?? 1;
    $statut = $_POST['statut'] ?? 'disponible';
    
    try {
        if ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $sql = "UPDATE trajet SET id_utilisateur = :id_utilisateur, lieu_depart = :lieu_depart,
                    lieu_arrivee = :lieu_arrivee, date_depart = :date_depart, duree_minutes = :duree_minutes,
                    budget = :budget, description = :description, places_demandees = :places_demandees, statut = :statut
                    WHERE id_trajet = :id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_utilisateur' => $id_utilisateur, ':lieu_depart' => $lieu_depart,
                ':lieu_arrivee' => $lieu_arrivee, ':date_depart' => $date_depart,
                ':duree_minutes' => $duree_minutes, ':budget' => $budget,
                ':description' => $description, ':places_demandees' => $places_demandees,
                ':statut' => $statut, ':id' => $id
            ])) {
                $message = 'Trajet mis à jour avec succès.';
                $messageType = 'success';
            }
        } else {
            $sql = "INSERT INTO trajet (id_utilisateur, lieu_depart, lieu_arrivee, date_depart, duree_minutes, budget, description, places_demandees, statut)
                    VALUES (:id_utilisateur, :lieu_depart, :lieu_arrivee, :date_depart, :duree_minutes, :budget, :description, :places_demandees, :statut)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':id_utilisateur' => $id_utilisateur, ':lieu_depart' => $lieu_depart,
                ':lieu_arrivee' => $lieu_arrivee, ':date_depart' => $date_depart,
                ':duree_minutes' => $duree_minutes, ':budget' => $budget,
                ':description' => $description, ':places_demandees' => $places_demandees,
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
           (SELECT COUNT(*) FROM proposition WHERE id_trajet = t.id_trajet) as nb_propositions
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
        <i class="fas fa-plus me-2"></i>Ajouter une Demande
      </button>
    </div>
  </div>
</div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { height: 400px; width: 100%; border-radius: 4px; }
        /* Ensure map modal is above the add modal */
        #mapModal { z-index: 1060; }
        .input-group-text { cursor: pointer; }
        .input-group-text:hover { background-color: #e9ecef; }
    </style>
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
            <th>Budget</th>
            <th>Places Dem.</th>
            <th>Propositions</th>
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
            <td><?= number_format($trajet['budget'], 2) ?> TND</td>
            <td><?= $trajet['places_demandees'] ?></td>
            <td>
                <a href="propositions.php?id_trajet=<?= $trajet['id_trajet'] ?>" class="badge bg-info text-decoration-none">
                    <?= $trajet['nb_propositions'] ?> <i class="fas fa-eye ms-1"></i>
                </a>
            </td>
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

<!-- Main Modal for Add/Edit -->
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
              <div class="input-group">
                <input type="text" class="form-control" name="lieu_depart" id="lieu_depart" required>
                <span class="input-group-text bg-primary text-white" onclick="openMap('lieu_depart')">
                    <i class="fas fa-map-marker-alt"></i>
                </span>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Lieu Arrivée *</label>
               <div class="input-group">
                <input type="text" class="form-control" name="lieu_arrivee" id="lieu_arrivee" required>
                <span class="input-group-text bg-primary text-white" onclick="openMap('lieu_arrivee')">
                    <i class="fas fa-map-marker-alt"></i>
                </span>
              </div>
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
              <label class="form-label">Budget (TND) *</label>
              <input type="number" step="0.01" class="form-control" name="budget" id="budget" min="0" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Places Demandées *</label>
              <input type="number" class="form-control" name="places_demandees" id="places_demandees" min="1" required>
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

<!-- Map Modal (Stacked) -->
<div class="modal fade" id="mapModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Choisir sur la carte</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="map"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let map;
let marker;
let currentTargetId;
let mapModal;
let isMapNavigation = false; // Flag to prevent form reset when switching modals

function editTrajet(trajet) {
  document.getElementById('modalTitle').textContent = 'Modifier le Trajet';
  document.getElementById('formAction').value = 'update';
  document.getElementById('trajetId').value = trajet.id_trajet;
  document.getElementById('id_utilisateur').value = trajet.id_utilisateur || '';
  document.getElementById('lieu_depart').value = trajet.lieu_depart || '';
  document.getElementById('lieu_arrivee').value = trajet.lieu_arrivee || '';
  document.getElementById('date_depart').value = trajet.date_depart ? trajet.date_depart.replace(' ', 'T').substring(0, 16) : '';
  document.getElementById('duree_minutes').value = trajet.duree_minutes || '';
  document.getElementById('budget').value = trajet.budget || '';
  document.getElementById('places_demandees').value = trajet.places_demandees || 1;
  document.getElementById('description').value = trajet.description || '';
  document.getElementById('statut').value = trajet.statut || 'disponible';
  
  const modalEl = document.getElementById('addModal');
  const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
  modal.show();
}

document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
  // Only reset if we are NOT switching to the map
  if (!isMapNavigation) {
      document.querySelector('#addModal form').reset();
      document.getElementById('modalTitle').textContent = 'Ajouter un Trajet';
      document.getElementById('formAction').value = 'add';
      document.getElementById('trajetId').value = '';
  }
});

// Configure Map Modal cleanup
document.getElementById('mapModal').addEventListener('hidden.bs.modal', function() {
    // When map closes, reopen the addModal
    const addModalEl = document.getElementById('addModal');
    const addModal = bootstrap.Modal.getInstance(addModalEl) || new bootstrap.Modal(addModalEl);
    addModal.show();
    
    // Reset flag after transition
    setTimeout(() => { isMapNavigation = false; }, 500);
});

$(document).ready(function() {
  $('#trajetsTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
    order: [[4, 'desc']]
  });
});

function initMap() {
    if (map) return;
    map = L.map('map').setView([34.0, 9.0], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
            .then(res => res.json())
            .then(data => {
                if(data.display_name && currentTargetId) {
                    document.getElementById(currentTargetId).value = data.display_name;
                    // Hiding mapModal will trigger the event listener to reopen addModal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('mapModal'));
                    modal.hide();
                }
            })
            .catch(err => console.error(err));
    });
}

function openMap(targetId) {
    currentTargetId = targetId;
    isMapNavigation = true; // Set flag prevents form reset
    
    // Hide addModal
    const addModalEl = document.getElementById('addModal');
    const addModal = bootstrap.Modal.getInstance(addModalEl) || new bootstrap.Modal(addModalEl);
    addModal.hide();
    
    // Show mapModal
    const mapModalEl = document.getElementById('mapModal');
    if (!mapModal) {
        mapModal = new bootstrap.Modal(mapModalEl);
    }
    // Need small delay or direct show? Direct show is fine usually
    mapModal.show();
    
    // Invalidate size
    mapModalEl.addEventListener('shown.bs.modal', function () {
        if(!map) initMap();
        else map.invalidateSize();
    }, { once: true });
}
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

