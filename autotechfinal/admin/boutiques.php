<?php
$pageTitle = 'Gestion des Boutiques';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Config::getConnexion();
$message = '';
$messageType = '';

if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (deleteRecord('boutique', 'id_boutique', $_GET['id'], $pdo)) {
        $message = 'Boutique supprimée avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $nom_boutique = $_POST['nom_boutique'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $id_utilisateur = $_POST['id_utilisateur'] ?? null;
    $statut = $_POST['statut'] ?? 'actif';
    
    $logo = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
        $logo = uploadFile($_FILES['logo'], 'logos');
        if ($logo && isset($_POST['id'])) {
            $old = getRecordById('boutique', 'id_boutique', $_POST['id'], $pdo);
            if ($old && $old['logo']) {
                deleteFile($old['logo'], 'logos');
            }
        }
    }
    
    try {
        if ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $sql = "UPDATE boutique SET nom_boutique = :nom_boutique, adresse = :adresse,
                    telephone = :telephone, id_utilisateur = :id_utilisateur, statut = :statut";
            $params = [
                ':nom_boutique' => $nom_boutique, ':adresse' => $adresse,
                ':telephone' => $telephone, ':id_utilisateur' => $id_utilisateur,
                ':statut' => $statut, ':id' => $id
            ];
            if ($logo) {
                $sql .= ", logo = :logo";
                $params[':logo'] = $logo;
            }
            $sql .= " WHERE id_boutique = :id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                $message = 'Boutique mise à jour avec succès.';
                $messageType = 'success';
            }
        } else {
            $sql = "INSERT INTO boutique (nom_boutique, adresse, telephone, logo, id_utilisateur, statut)
                    VALUES (:nom_boutique, :adresse, :telephone, :logo, :id_utilisateur, :statut)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':nom_boutique' => $nom_boutique, ':adresse' => $adresse,
                ':telephone' => $telephone, ':logo' => $logo,
                ':id_utilisateur' => $id_utilisateur, ':statut' => $statut
            ])) {
                $message = 'Boutique ajoutée avec succès.';
                $messageType = 'success';
            }
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

$boutiques = $pdo->query("
    SELECT b.*, u.nom as user_nom, u.prenom as user_prenom,
           (SELECT COUNT(*) FROM vehicule WHERE id_boutique = b.id_boutique) as nb_vehicules
    FROM boutique b
    LEFT JOIN utilisateur u ON b.id_utilisateur = u.id_utilisateur
    ORDER BY b.date_creation DESC
")->fetchAll();

$users = $pdo->query("SELECT id_utilisateur, nom, prenom FROM utilisateur ORDER BY nom")->fetchAll();
$stats = [
    'total' => count($boutiques),
    'actives' => count(array_filter($boutiques, fn($b) => $b['statut'] === 'actif'))
];
?>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-store me-3"></i>Gestion des Boutiques
        </h1>
        <p class="text-muted mb-0">Gérer toutes les boutiques</p>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Ajouter une Boutique
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
        <p class="text-muted mb-0">Total Boutiques</p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-success"><?= $stats['actives'] ?></h3>
        <p class="text-muted mb-0">Boutiques Actives</p>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="boutiquesTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Logo</th>
            <th>Nom</th>
            <th>Adresse</th>
            <th>Téléphone</th>
            <th>Propriétaire</th>
            <th>Véhicules</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($boutiques as $boutique): ?>
          <tr>
            <td><?= $boutique['id_boutique'] ?></td>
            <td>
              <?php if ($boutique['logo']): ?>
                <img src="../uploads/logos/<?= htmlspecialchars($boutique['logo']) ?>" 
                     alt="Logo" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
              <?php else: ?>
                <div class="bg-secondary rounded d-inline-flex align-items-center justify-content-center" 
                     style="width: 50px; height: 50px;">
                  <i class="fas fa-store text-white"></i>
                </div>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($boutique['nom_boutique']) ?></td>
            <td><?= htmlspecialchars($boutique['adresse']) ?></td>
            <td><?= htmlspecialchars($boutique['telephone']) ?></td>
            <td><?= $boutique['user_prenom'] . ' ' . $boutique['user_nom'] ?></td>
            <td><span class="badge bg-info"><?= $boutique['nb_vehicules'] ?></span></td>
            <td>
              <span class="badge bg-<?= $boutique['statut'] === 'actif' ? 'success' : 'secondary' ?>">
                <?= htmlspecialchars($boutique['statut']) ?>
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="editBoutique(<?= htmlspecialchars(json_encode($boutique)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <a href="?delete=1&id=<?= $boutique['id_boutique'] ?>" 
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
        <h5 class="modal-title" id="modalTitle">Ajouter une Boutique</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="boutiqueId">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nom de la Boutique *</label>
            <input type="text" class="form-control" name="nom_boutique" id="nom_boutique" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Adresse *</label>
            <input type="text" class="form-control" name="adresse" id="adresse" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Téléphone *</label>
            <input type="text" class="form-control" name="telephone" id="telephone" required>
          </div>
          <div class="mb-3">
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
          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select class="form-select" name="statut" id="statut">
              <option value="actif">Actif</option>
              <option value="inactif">Inactif</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Logo</label>
            <input type="file" class="form-control" name="logo" accept="image/*">
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
function editBoutique(boutique) {
  document.getElementById('modalTitle').textContent = 'Modifier la Boutique';
  document.getElementById('formAction').value = 'update';
  document.getElementById('boutiqueId').value = boutique.id_boutique;
  document.getElementById('nom_boutique').value = boutique.nom_boutique || '';
  document.getElementById('adresse').value = boutique.adresse || '';
  document.getElementById('telephone').value = boutique.telephone || '';
  document.getElementById('id_utilisateur').value = boutique.id_utilisateur || '';
  document.getElementById('statut').value = boutique.statut || 'actif';
  new bootstrap.Modal(document.getElementById('addModal')).show();
}

document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
  document.querySelector('#addModal form').reset();
  document.getElementById('modalTitle').textContent = 'Ajouter une Boutique';
  document.getElementById('formAction').value = 'add';
  document.getElementById('boutiqueId').value = '';
});

$(document).ready(function() {
  $('#boutiquesTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
    order: [[0, 'desc']]
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

