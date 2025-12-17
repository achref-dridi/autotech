<?php
$pageTitle = 'Gestion des Propositions';
require_once __DIR__ . '/partials/header.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_GET['id_trajet'])) {
    header('Location: trajets.php');
    exit();
}

$id_trajet = (int)$_GET['id_trajet'];
$pdo = Config::getConnexion();
$message = '';
$messageType = '';

// Handle Delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    if (deleteRecord('proposition', 'id_proposition', $_GET['id'], $pdo)) {
        $message = 'Proposition supprimée avec succès.';
        $messageType = 'success';
    } else {
        $message = 'Erreur lors de la suppression.';
        $messageType = 'danger';
    }
}

// Fetch Trajet Info
$stmt = $pdo->prepare("SELECT * FROM trajet WHERE id_trajet = ?");
$stmt->execute([$id_trajet]);
$trajet = $stmt->fetch();

if (!$trajet) {
    header('Location: trajets.php');
    exit();
}

// Fetch Propositions
$stmt = $pdo->prepare("
    SELECT p.*, u.nom, u.prenom, u.email, u.telephone
    FROM proposition p
    JOIN utilisateur u ON p.id_conducteur = u.id_utilisateur
    WHERE p.id_trajet = ?
    ORDER BY p.date_proposition DESC
");
$stmt->execute([$id_trajet]);
$propositions = $stmt->fetchAll();
?>

<?php if ($message): ?>
<div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
  <?= htmlspecialchars($message) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-comments me-3"></i>Propositions
        </h1>
        <p class="text-muted mb-0">
            Pour la demande: <strong><?= htmlspecialchars($trajet['lieu_depart']) ?> <i class="fas fa-arrow-right mx-1"></i> <?= htmlspecialchars($trajet['lieu_arrivee']) ?></strong>
        </p>
      </div>
      <a href="trajets.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour
      </a>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <?php if (empty($propositions)): ?>
        <div class="text-center py-5">
            <p class="text-muted">Aucune proposition pour cette demande.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Conducteur</th>
                <th>Prix Proposé</th>
                <th>Date</th>
                <th>Message</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($propositions as $prop): ?>
              <tr>
                <td><?= $prop['id_proposition'] ?></td>
                <td>
                    <?= htmlspecialchars($prop['prenom'] . ' ' . $prop['nom']) ?><br>
                    <small class="text-muted"><?= htmlspecialchars($prop['email']) ?></small>
                </td>
                <td><strong><?= number_format($prop['prix'], 2) ?> TND</strong></td>
                <td><?= date('d/m/Y H:i', strtotime($prop['date_proposition'])) ?></td>
                <td>
                    <?php if (!empty($prop['message'])): ?>
                        <div style="max-width: 300px; white-space: normal;">
                            <?= htmlspecialchars(substr($prop['message'], 0, 100)) ?>...
                        </div>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge bg-<?= $prop['statut'] === 'acceptee' ? 'success' : 'secondary' ?>">
                        <?= ucfirst($prop['statut']) ?>
                    </span>
                </td>
                <td>
                  <!-- Can only delete as requested -->
                  <a href="?id_trajet=<?= $id_trajet ?>&delete=1&id=<?= $prop['id_proposition'] ?>" 
                     class="btn btn-sm btn-danger" 
                     onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette proposition?')">
                    <i class="fas fa-trash"></i> Supprimer
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
