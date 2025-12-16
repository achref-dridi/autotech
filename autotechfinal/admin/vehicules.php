<?php
$pageTitle = 'Gestion des Véhicules';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Config::getConnexion();
$message = '';
$messageType = '';

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (deleteRecord('vehicule', 'id_vehicule', $_GET['id'], $pdo)) {
        $message = 'Véhicule supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'danger';
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $id_utilisateur = !empty($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : null;
    $id_boutique = !empty($_POST['id_boutique']) ? (int)$_POST['id_boutique'] : null;
    $marque = $_POST['marque'] ?? '';
    $modele = $_POST['modele'] ?? '';
    $annee = $_POST['annee'] ?? '';
    $carburant = $_POST['carburant'] ?? '';
    $kilometrage = $_POST['kilometrage'] ?? 0;
    $couleur = $_POST['couleur'] ?? '';
    $transmission = $_POST['transmission'] ?? '';
    $prix_journalier = $_POST['prix_journalier'] ?? null;
    $description = $_POST['description'] ?? '';
    $statut_disponibilite = $_POST['statut_disponibilite'] ?? 'disponible';
    
    $image_principale = null;
    if (isset($_FILES['image_principale']) && $_FILES['image_principale']['error'] === 0) {
        $image_principale = uploadFile($_FILES['image_principale'], 'vehicule');
        if ($image_principale) {
            $old = getRecordById('vehicule', 'id_vehicule', $id, $pdo);
            if ($old && $old['image_principale']) {
                deleteFile($old['image_principale'], 'vehicule');
            }
        }
    }
    
    try {
        $sql = "UPDATE vehicule SET id_utilisateur = :id_utilisateur, id_boutique = :id_boutique,
                marque = :marque, modele = :modele, annee = :annee, carburant = :carburant,
                kilometrage = :kilometrage, couleur = :couleur, transmission = :transmission,
                prix_journalier = :prix_journalier, description = :description,
                statut_disponibilite = :statut_disponibilite";
        $params = [
            ':id_utilisateur' => $id_utilisateur, ':id_boutique' => $id_boutique,
            ':marque' => $marque, ':modele' => $modele, ':annee' => $annee,
            ':carburant' => $carburant, ':kilometrage' => $kilometrage,
            ':couleur' => $couleur, ':transmission' => $transmission,
            ':prix_journalier' => $prix_journalier, ':description' => $description,
            ':statut_disponibilite' => $statut_disponibilite, ':id' => $id
        ];
        
        if ($image_principale) {
            $sql .= ", image_principale = :image_principale";
            $params[':image_principale'] = $image_principale;
        }
        
        $sql .= " WHERE id_vehicule = :id";
        $params[':id'] = $id;
        
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $message = 'Véhicule mis à jour avec succès.';
            $messageType = 'success';
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $id_utilisateur = !empty($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : null;
    $id_boutique = !empty($_POST['id_boutique']) ? (int)$_POST['id_boutique'] : null;
    $marque = $_POST['marque'] ?? '';
    $modele = $_POST['modele'] ?? '';
    $annee = $_POST['annee'] ?? '';
    $carburant = $_POST['carburant'] ?? '';
    $kilometrage = $_POST['kilometrage'] ?? 0;
    $couleur = $_POST['couleur'] ?? '';
    $transmission = $_POST['transmission'] ?? '';
    $prix_journalier = $_POST['prix_journalier'] ?? null;
    $description = $_POST['description'] ?? '';
    $statut_disponibilite = $_POST['statut_disponibilite'] ?? 'disponible';
    
    $image_principale = null;
    if (isset($_FILES['image_principale']) && $_FILES['image_principale']['error'] === 0) {
        $image_principale = uploadFile($_FILES['image_principale'], 'vehicule');
    }
    
    try {
        $sql = "INSERT INTO vehicule (id_utilisateur, id_boutique, marque, modele, annee, carburant,
                kilometrage, couleur, transmission, prix_journalier, description, image_principale, statut_disponibilite)
                VALUES (:id_utilisateur, :id_boutique, :marque, :modele, :annee, :carburant,
                :kilometrage, :couleur, :transmission, :prix_journalier, :description, :image_principale, :statut_disponibilite)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            ':id_utilisateur' => $id_utilisateur, ':id_boutique' => $id_boutique,
            ':marque' => $marque, ':modele' => $modele, ':annee' => $annee,
            ':carburant' => $carburant, ':kilometrage' => $kilometrage,
            ':couleur' => $couleur, ':transmission' => $transmission,
            ':prix_journalier' => $prix_journalier, ':description' => $description,
            ':image_principale' => $image_principale, ':statut_disponibilite' => $statut_disponibilite
        ])) {
            $message = 'Véhicule ajouté avec succès.';
            $messageType = 'success';
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get all vehicles with user and boutique info
$vehicles = $pdo->query("
    SELECT v.*, u.nom as user_nom, u.prenom as user_prenom, b.nom_boutique
    FROM vehicule v
    LEFT JOIN utilisateur u ON v.id_utilisateur = u.id_utilisateur
    LEFT JOIN boutique b ON v.id_boutique = b.id_boutique
    ORDER BY v.created_at DESC
")->fetchAll();

$users = $pdo->query("SELECT id_utilisateur, nom, prenom FROM utilisateur ORDER BY nom")->fetchAll();
$boutiques = $pdo->query("SELECT id_boutique, nom_boutique FROM boutique ORDER BY nom_boutique")->fetchAll();

$stats = [
    'total' => count($vehicles),
    'disponibles' => count(array_filter($vehicles, fn($v) => $v['statut_disponibilite'] === 'disponible')),
    'loues' => count(array_filter($vehicles, fn($v) => $v['statut_disponibilite'] === 'loué')),
    'en_boutique' => count(array_filter($vehicles, fn($v) => $v['id_boutique'] !== null))
];
?>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-car me-3"></i>Gestion des Véhicules
        </h1>
        <p class="text-muted mb-0">Gérer tous les véhicules du système</p>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Ajouter un Véhicule
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

<!-- Statistics -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-primary"><?= $stats['total'] ?></h3>
        <p class="text-muted mb-0">Total Véhicules</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-success"><?= $stats['disponibles'] ?></h3>
        <p class="text-muted mb-0">Disponibles</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-info"><?= $stats['loues'] ?></h3>
        <p class="text-muted mb-0">Loués</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-warning"><?= $stats['en_boutique'] ?></h3>
        <p class="text-muted mb-0">En Boutique</p>
      </div>
    </div>
  </div>
</div>

<!-- Vehicles Table -->
<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="vehiclesTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Marque/Modèle</th>
            <th>Année</th>
            <th>Propriétaire</th>
            <th>Boutique</th>
            <th>Prix/Jour</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($vehicles as $vehicle): ?>
          <tr>
            <td><?= $vehicle['id_vehicule'] ?></td>
            <td>
              <?php if ($vehicle['image_principale']): ?>
                <img src="../uploads/vehicule/<?= htmlspecialchars($vehicle['image_principale']) ?>" 
                     alt="Véhicule" style="width: 60px; height: 40px; object-fit: cover;" class="rounded">
              <?php else: ?>
                <div class="bg-secondary d-inline-flex align-items-center justify-content-center rounded" 
                     style="width: 60px; height: 40px;">
                  <i class="fas fa-car text-white"></i>
                </div>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($vehicle['marque'] . ' ' . $vehicle['modele']) ?></td>
            <td><?= $vehicle['annee'] ?></td>
            <td><?= $vehicle['user_prenom'] . ' ' . $vehicle['user_nom'] ?></td>
            <td><?= $vehicle['nom_boutique'] ?? '-' ?></td>
            <td><?= $vehicle['prix_journalier'] ? number_format($vehicle['prix_journalier'], 2) . ' TND' : '-' ?></td>
            <td>
              <span class="badge bg-<?= $vehicle['statut_disponibilite'] === 'disponible' ? 'success' : ($vehicle['statut_disponibilite'] === 'loué' ? 'warning' : 'danger') ?>">
                <?= htmlspecialchars($vehicle['statut_disponibilite']) ?>
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="editVehicle(<?= htmlspecialchars(json_encode($vehicle)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <a href="?delete=1&id=<?= $vehicle['id_vehicule'] ?>" 
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Ajouter un Véhicule</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="vehicleId">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Propriétaire</label>
              <select class="form-select" name="id_utilisateur" id="id_utilisateur">
                <option value="">Sélectionner...</option>
                <?php foreach ($users as $user): ?>
                <option value="<?= $user['id_utilisateur'] ?>">
                  <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Boutique</label>
              <select class="form-select" name="id_boutique" id="id_boutique">
                <option value="">Aucune</option>
                <?php foreach ($boutiques as $boutique): ?>
                <option value="<?= $boutique['id_boutique'] ?>">
                  <?= htmlspecialchars($boutique['nom_boutique']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Marque *</label>
              <input type="text" class="form-control" name="marque" id="marque" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Modèle *</label>
              <input type="text" class="form-control" name="modele" id="modele" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Année *</label>
              <input type="number" class="form-control" name="annee" id="annee" min="1900" max="<?= date('Y') + 1 ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Carburant *</label>
              <select class="form-select" name="carburant" id="carburant" required>
                <option value="Essence">Essence</option>
                <option value="Diesel">Diesel</option>
                <option value="Électrique">Électrique</option>
                <option value="Hybride">Hybride</option>
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Kilométrage *</label>
              <input type="number" class="form-control" name="kilometrage" id="kilometrage" min="0" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Couleur</label>
              <input type="text" class="form-control" name="couleur" id="couleur">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Transmission</label>
              <select class="form-select" name="transmission" id="transmission">
                <option value="">Sélectionner...</option>
                <option value="Manuelle">Manuelle</option>
                <option value="Automatique">Automatique</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Prix Journalier (TND)</label>
              <input type="number" step="0.01" class="form-control" name="prix_journalier" id="prix_journalier">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Statut</label>
              <select class="form-select" name="statut_disponibilite" id="statut_disponibilite">
                <option value="disponible">Disponible</option>
                <option value="loué">Loué</option>
                <option value="indisponible">Indisponible</option>
              </select>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" id="description" rows="3"></textarea>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Image Principale</label>
              <input type="file" class="form-control" name="image_principale" accept="image/*">
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
function editVehicle(vehicle) {
  document.getElementById('modalTitle').textContent = 'Modifier le Véhicule';
  document.getElementById('formAction').value = 'update';
  document.getElementById('vehicleId').value = vehicle.id_vehicule;
  document.getElementById('id_utilisateur').value = vehicle.id_utilisateur || '';
  document.getElementById('id_boutique').value = vehicle.id_boutique || '';
  document.getElementById('marque').value = vehicle.marque || '';
  document.getElementById('modele').value = vehicle.modele || '';
  document.getElementById('annee').value = vehicle.annee || '';
  document.getElementById('carburant').value = vehicle.carburant || '';
  document.getElementById('kilometrage').value = vehicle.kilometrage || '';
  document.getElementById('couleur').value = vehicle.couleur || '';
  document.getElementById('transmission').value = vehicle.transmission || '';
  document.getElementById('prix_journalier').value = vehicle.prix_journalier || '';
  document.getElementById('description').value = vehicle.description || '';
  document.getElementById('statut_disponibilite').value = vehicle.statut_disponibilite || 'disponible';
  new bootstrap.Modal(document.getElementById('addModal')).show();
}

document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
  document.querySelector('#addModal form').reset();
  document.getElementById('modalTitle').textContent = 'Ajouter un Véhicule';
  document.getElementById('formAction').value = 'add';
  document.getElementById('vehicleId').value = '';
});

$(document).ready(function() {
  $('#vehiclesTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
    order: [[0, 'desc']]
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

