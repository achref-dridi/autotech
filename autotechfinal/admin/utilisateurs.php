<?php
$pageTitle = 'Gestion des Utilisateurs';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = Config::getConnexion();
$message = '';
$messageType = '';

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (deleteRecord('utilisateur', 'id_utilisateur', $_GET['id'], $pdo)) {
        $message = 'Utilisateur supprimé avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'danger';
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $code_postal = $_POST['code_postal'] ?? '';
    $statut = $_POST['statut'] ?? 'actif';
    $role = $_POST['role'] ?? 'utilisateur';
    
    $photo_profil = null;
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === 0) {
        $photo_profil = uploadFile($_FILES['photo_profil'], 'profils');
        if ($photo_profil) {
            // Delete old photo
            $old = getRecordById('utilisateur', 'id_utilisateur', $id, $pdo);
            if ($old && $old['photo_profil']) {
                deleteFile($old['photo_profil'], 'profils');
            }
        }
    }
    
    try {
        $sql = "UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, 
                telephone = :telephone, adresse = :adresse, ville = :ville, 
                code_postal = :code_postal, statut = :statut, role = :role";
        $params = [
            ':nom' => $nom, ':prenom' => $prenom, ':email' => $email,
            ':telephone' => $telephone, ':adresse' => $adresse, ':ville' => $ville,
            ':code_postal' => $code_postal, ':statut' => $statut, ':role' => $role,
            ':id' => $id
        ];
        
        if ($photo_profil) {
            $sql .= ", photo_profil = :photo_profil";
            $params[':photo_profil'] = $photo_profil;
        }
        
        $sql .= " WHERE id_utilisateur = :id";
        $params[':id'] = $id;
        
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            $message = 'Utilisateur mis à jour avec succès.';
            $messageType = 'success';
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $code_postal = $_POST['code_postal'] ?? '';
    $statut = $_POST['statut'] ?? 'actif';
    $role = $_POST['role'] ?? 'utilisateur';
    
    $photo_profil = null;
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === 0) {
        $photo_profil = uploadFile($_FILES['photo_profil'], 'profils');
    }
    
    try {
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $sql = "INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe, 
                adresse, ville, code_postal, photo_profil, statut, role) 
                VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe, 
                :adresse, :ville, :code_postal, :photo_profil, :statut, :role)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            ':nom' => $nom, ':prenom' => $prenom, ':email' => $email,
            ':telephone' => $telephone, ':mot_de_passe' => $mot_de_passe_hash,
            ':adresse' => $adresse, ':ville' => $ville, ':code_postal' => $code_postal,
            ':photo_profil' => $photo_profil, ':statut' => $statut, ':role' => $role
        ])) {
            $message = 'Utilisateur ajouté avec succès.';
            $messageType = 'success';
        }
    } catch (PDOException $e) {
        $message = 'Erreur: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get all users
$users = getTableData('utilisateur', $pdo);
$stats = [
    'total' => count($users),
    'actifs' => count(array_filter($users, fn($u) => $u['statut'] === 'actif')),
    'admins' => count(array_filter($users, fn($u) => $u['role'] === 'admin'))
];
?>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-users me-3"></i>Gestion des Utilisateurs
        </h1>
        <p class="text-muted mb-0">Gérer tous les utilisateurs du système</p>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="fas fa-plus me-2"></i>Ajouter un Utilisateur
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

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-primary"><?= $stats['total'] ?></h3>
        <p class="text-muted mb-0">Total Utilisateurs</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-success"><?= $stats['actifs'] ?></h3>
        <p class="text-muted mb-0">Utilisateurs Actifs</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <h3 class="text-info"><?= $stats['admins'] ?></h3>
        <p class="text-muted mb-0">Administrateurs</p>
      </div>
    </div>
  </div>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="usersTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Photo</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Ville</th>
            <th>Statut</th>
            <th>Rôle</th>
            <th>Date Création</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
          <tr>
            <td><?= $user['id_utilisateur'] ?></td>
            <td>
              <?php if ($user['photo_profil']): ?>
                <img src="../uploads/profils/<?= htmlspecialchars($user['photo_profil']) ?>" 
                     alt="Photo" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
              <?php else: ?>
                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" 
                     style="width: 40px; height: 40px;">
                  <i class="fas fa-user text-white"></i>
                </div>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['telephone'] ?? '-') ?></td>
            <td><?= htmlspecialchars($user['ville'] ?? '-') ?></td>
            <td>
              <span class="badge bg-<?= $user['statut'] === 'actif' ? 'success' : ($user['statut'] === 'inactif' ? 'secondary' : 'danger') ?>">
                <?= htmlspecialchars($user['statut']) ?>
              </span>
            </td>
            <td>
              <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                <?= htmlspecialchars($user['role']) ?>
              </span>
            </td>
            <td><?= date('d/m/Y', strtotime($user['date_creation'])) ?></td>
            <td>
              <button class="btn btn-sm btn-primary" onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <a href="?delete=1&id=<?= $user['id_utilisateur'] ?>" 
                 class="btn btn-sm btn-danger" 
                 onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">
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
        <h5 class="modal-title" id="modalTitle">Ajouter un Utilisateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" enctype="multipart/form-data" id="userForm">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="id" id="userId">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nom *</label>
              <input type="text" class="form-control" name="nom" id="nom" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Prénom *</label>
              <input type="text" class="form-control" name="prenom" id="prenom" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email *</label>
              <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Téléphone</label>
              <input type="text" class="form-control" name="telephone" id="telephone">
            </div>
            <div class="col-md-6 mb-3" id="passwordField">
              <label class="form-label">Mot de passe *</label>
              <input type="password" class="form-control" name="mot_de_passe" id="mot_de_passe">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Statut</label>
              <select class="form-select" name="statut" id="statut">
                <option value="actif">Actif</option>
                <option value="inactif">Inactif</option>
                <option value="suspendu">Suspendu</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Rôle</label>
              <select class="form-select" name="role" id="role">
                <option value="utilisateur">Utilisateur</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Ville</label>
              <input type="text" class="form-control" name="ville" id="ville">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Code Postal</label>
              <input type="text" class="form-control" name="code_postal" id="code_postal">
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Adresse</label>
              <textarea class="form-control" name="adresse" id="adresse" rows="2"></textarea>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Photo de Profil</label>
              <input type="file" class="form-control" name="photo_profil" accept="image/*">
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
function editUser(user) {
  document.getElementById('modalTitle').textContent = 'Modifier l\'Utilisateur';
  document.getElementById('formAction').value = 'update';
  document.getElementById('userId').value = user.id_utilisateur;
  document.getElementById('nom').value = user.nom || '';
  document.getElementById('prenom').value = user.prenom || '';
  document.getElementById('email').value = user.email || '';
  document.getElementById('telephone').value = user.telephone || '';
  document.getElementById('ville').value = user.ville || '';
  document.getElementById('code_postal').value = user.code_postal || '';
  document.getElementById('adresse').value = user.adresse || '';
  document.getElementById('statut').value = user.statut || 'actif';
  document.getElementById('role').value = user.role || 'utilisateur';
  document.getElementById('passwordField').style.display = 'none';
  document.getElementById('mot_de_passe').removeAttribute('required');
  new bootstrap.Modal(document.getElementById('addModal')).show();
}

document.getElementById('addModal').addEventListener('hidden.bs.modal', function() {
  document.getElementById('userForm').reset();
  document.getElementById('modalTitle').textContent = 'Ajouter un Utilisateur';
  document.getElementById('formAction').value = 'add';
  document.getElementById('userId').value = '';
  document.getElementById('passwordField').style.display = 'block';
  document.getElementById('mot_de_passe').setAttribute('required', 'required');
});

$(document).ready(function() {
  $('#usersTable').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
    },
    order: [[0, 'desc']]
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

