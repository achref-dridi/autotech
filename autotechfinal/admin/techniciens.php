<?php
$pageTitle = 'Gestion des Techniciens';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Config::getConnexion();
$message = '';
$messageType = '';

if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (deleteRecord('technicien', 'id_technicien', $_GET['id'], $pdo)) {
        $message = 'Technicien supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $nom = $_POST['nom'] ?? '';
    $specialite = $_POST['specialite'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $email = $_POST['email'] ?? '';
    $disponibilite = $_POST['disponibilite'] ?? 'actif';
    
    try {
        if ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $sql = "UPDATE technicien SET nom = :nom, specialite = :specialite,
                    telephone = :telephone, email = :email, disponibilite = :disponibilite
                    WHERE id_technicien = :id";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':nom' => $nom, ':specialite' => $specialite,
                ':telephone' => $telephone, ':email' => $email,
                ':disponibilite' => $disponibilite, ':id' => $id
            ])) {
                $message = 'Technicien mis à jour avec succès.';
                $messageType = 'success';
            }
        } else {
            $sql = "INSERT INTO technicien (nom, specialite, telephone, email, disponibilite)
                    VALUES (:nom, :specialite, :telephone, :email, :disponibilite)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([
                ':nom' => $nom, ':specialite' => $specialite,
                ':telephone' => $telephone, ':email' => $email,
                ':disponibilite' => $disponibilite
            ])) {
                $message = 'Technicien ajouté avec succès.';
                $messageType = 'success';
            }
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

$techniciens = $pdo->query("
    SELECT t.*, 
           (SELECT COUNT(*) FROM rendez_vous WHERE id_technicien = t.id_technicien) as nb_rdv
    FROM technicien t
    ORDER BY t.date_creation DESC
")->fetchAll();

$stats = [
    'total' => count($techniciens),
    'actifs' => count(array_filter($techniciens, fn($t) => $t['disponibilite'] === 'actif'))
];
?>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-user-cog me-3"></i>Gestion des Techniciens
        </h1>
        <p class="text-muted mb-0">Gérer tous les techniciens</p>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Ajouter un Technicien
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
        <p class="text-muted mb-0">Total Techniciens</p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-success"><?= $stats['actifs'] ?></h3>
        <p class="text-muted mb-0">Techniciens Actifs</p>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="techniciensTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Spécialité</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Rendez-vous</th>
            <th>Disponibilité</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($techniciens as $tech): ?>
          <tr>
            <td><?= $tech['id_technicien'] ?></td>
            <td><?= htmlspecialchars($tech['nom']) ?></td>
            <td><?= htmlspecialchars($tech['specialite']) ?></td>
            <td><?= htmlspecialchars($tech['telephone'] ?? '-') ?></td>
            <td><?= htmlspecialchars($tech['email'] ?? '-') ?></td>
            <td><span class="badge bg-info"><?= $tech['nb_rdv'] ?></span></td>
            <td>
              <span class="badge bg-<?= $tech['disponibilite'] === 'actif' ? 'success' : 'secondary' ?>">
                <?= htmlspecialchars($tech['disponibilite']) ?>
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="editTechnicien(<?= htmlspecialchars(json_encode($tech)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <a href="?delete=1&id=<?= $tech['id_technicien'] ?>" 
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
        <h5 class="modal-title" id="modalTitle">Ajouter un Technicien</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="technicienId">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nom *</label>
            <input type="text" class="form-control" name="nom" id="nom" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Spécialité *</label>
            <input type="text" class="form-control" name="specialite" id="specialite" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" class="form-control" name="telephone" id="telephone">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="email">
          </div>
          <div class="mb-3">
            <label class="form-label">Disponibilité</label>
            <select class="form-select" name="disponibilite" id="disponibilite">
              <option value="actif">Actif</option>
              <option value="inactif">Inactif</option>
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
function editTechnicien(tech) {
  document.getElementById('modalTitle').textContent = 'Modifier le Technicien';
  document.getElementById('formAction').value = 'update';
  document.getElementById('technicienId').value = tech.id_technicien;
  document.getElementById('nom').value = tech.nom || '';
  document.getElementById('specialite').value = tech.specialite || '';
  document.getElementById('telephone').value = tech.telephone || '';
  document.getElementById('email').value = tech.email || '';
  document.getElementById('disponibilite').value = tech.disponibilite || 'actif';
  new bootstrap.Modal(document.getElementById('addModal')).show();
}

document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
  document.querySelector('#addModal form').reset();
  document.getElementById('modalTitle').textContent = 'Ajouter un Technicien';
  document.getElementById('formAction').value = 'add';
  document.getElementById('technicienId').value = '';
});

$(document).ready(function() {
  $('#techniciensTable').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json' },
    order: [[0, 'desc']]
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

