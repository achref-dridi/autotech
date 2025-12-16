<?php
$pageTitle = 'Tableau de Bord - Administration AutoTech';
require_once __DIR__ . '/partials/header.php';

$pdo = Config::getConnexion();

// Get statistics
$stats = [];

// Users stats
$stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
$stats['active_users'] = $pdo->query("SELECT COUNT(*) FROM utilisateur WHERE statut = 'actif'")->fetchColumn();

// Vehicles stats
$stats['total_vehicles'] = $pdo->query("SELECT COUNT(*) FROM vehicule")->fetchColumn();
$stats['available_vehicles'] = $pdo->query("SELECT COUNT(*) FROM vehicule WHERE statut_disponibilite = 'disponible'")->fetchColumn();
$stats['rented_vehicles'] = $pdo->query("SELECT COUNT(*) FROM vehicule WHERE statut_disponibilite = 'loué'")->fetchColumn();
$vehicles_in_boutiques = $pdo->query("SELECT COUNT(*) FROM vehicule WHERE id_boutique IS NOT NULL")->fetchColumn();
$stats['vehicles_in_boutiques_pct'] = $stats['total_vehicles'] > 0 ? round(($vehicles_in_boutiques / $stats['total_vehicles']) * 100, 1) : 0;

// Boutiques stats
$stats['total_boutiques'] = $pdo->query("SELECT COUNT(*) FROM boutique")->fetchColumn();
$stats['active_boutiques'] = $pdo->query("SELECT COUNT(*) FROM boutique WHERE statut = 'actif'")->fetchColumn();

// Technicians stats
$stats['total_technicians'] = $pdo->query("SELECT COUNT(*) FROM technicien")->fetchColumn();
$stats['active_technicians'] = $pdo->query("SELECT COUNT(*) FROM technicien WHERE disponibilite = 'actif'")->fetchColumn();

// Appointments stats
$stats['total_appointments'] = $pdo->query("SELECT COUNT(*) FROM rendez_vous")->fetchColumn();
$stats['pending_appointments'] = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut = 'en attente'")->fetchColumn();
$stats['confirmed_appointments'] = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE statut = 'confirme'")->fetchColumn();

// Reservations stats
$stats['total_reservations'] = $pdo->query("SELECT COUNT(*) FROM reservation")->fetchColumn();
$stats['pending_reservations'] = $pdo->query("SELECT COUNT(*) FROM reservation WHERE statut = 'en attente'")->fetchColumn();

// Trips stats
$stats['total_trips'] = $pdo->query("SELECT COUNT(*) FROM trajet")->fetchColumn();
$stats['available_trips'] = $pdo->query("SELECT COUNT(*) FROM trajet WHERE statut = 'disponible'")->fetchColumn();

// Trip reservations stats
$stats['total_trip_reservations'] = $pdo->query("SELECT COUNT(*) FROM reservation_trajet")->fetchColumn();
?>

<!-- Header -->
<div class="row mb-5">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="display-6 fw-bold text-primary mb-2">
          <i class="fas fa-tachometer-alt me-3"></i>Tableau de Bord
        </h1>
        <p class="text-muted mb-0">Bienvenue dans votre espace d'administration AutoTech</p>
      </div>
      <div class="text-end">
        <small class="text-muted d-block">Dernière connexion</small>
        <strong><?= date('d/m/Y à H:i') ?></strong>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-5">
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title text-muted text-uppercase small">Utilisateurs</h6>
            <h2 class="fw-bold text-primary mb-0"><?= $stats['total_users'] ?></h2>
            <small class="text-success"><?= $stats['active_users'] ?> actifs</small>
          </div>
          <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-users text-primary fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title text-muted text-uppercase small">Véhicules</h6>
            <h2 class="fw-bold text-info mb-0"><?= $stats['total_vehicles'] ?></h2>
            <small class="text-info"><?= $stats['available_vehicles'] ?> disponibles</small>
          </div>
          <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-car text-info fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title text-muted text-uppercase small">Boutiques</h6>
            <h2 class="fw-bold text-success mb-0"><?= $stats['total_boutiques'] ?></h2>
            <small class="text-success"><?= $stats['active_boutiques'] ?> actives</small>
          </div>
          <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-store text-success fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title text-muted text-uppercase small">Rendez-vous</h6>
            <h2 class="fw-bold text-warning mb-0"><?= $stats['total_appointments'] ?></h2>
            <small class="text-warning"><?= $stats['pending_appointments'] ?> en attente</small>
          </div>
          <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-calendar-alt text-warning fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Additional Stats Row -->
<div class="row mb-5">
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title text-muted text-uppercase small">Techniciens</h6>
            <h2 class="fw-bold text-danger mb-0"><?= $stats['total_technicians'] ?></h2>
            <small class="text-danger"><?= $stats['active_technicians'] ?> actifs</small>
          </div>
          <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-user-cog text-danger fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title text-muted text-uppercase small">Réservations</h6>
            <h2 class="fw-bold text-purple mb-0"><?= $stats['total_reservations'] ?></h2>
            <small class="text-muted"><?= $stats['pending_reservations'] ?> en attente</small>
          </div>
          <div class="icon-wrapper bg-purple bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-calendar-check text-purple fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title text-muted text-uppercase small">Trajets</h6>
            <h2 class="fw-bold text-secondary mb-0"><?= $stats['total_trips'] ?></h2>
            <small class="text-secondary"><?= $stats['available_trips'] ?> disponibles</small>
          </div>
          <div class="icon-wrapper bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-route text-secondary fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card stat-card border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="card-title text-muted text-uppercase small">Véhicules en Boutiques</h6>
            <h2 class="fw-bold text-primary mb-0"><?= $stats['vehicles_in_boutiques_pct'] ?>%</h2>
            <small class="text-muted"><?= $vehicles_in_boutiques ?> véhicules</small>
          </div>
          <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
            <i class="fas fa-percentage text-primary fa-2x"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row mb-5">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-primary">
          <i class="fas fa-bolt me-2"></i>Actions Rapides
        </h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-lg-3 col-md-6">
            <a href="utilisateurs.php" class="card action-card h-100 border-0 text-decoration-none">
              <div class="card-body text-center p-4">
                <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                  <i class="fas fa-users text-primary fa-2x"></i>
                </div>
                <h6 class="text-primary mb-2">Gérer les Utilisateurs</h6>
                <p class="text-muted small mb-0">Voir et modifier les utilisateurs</p>
              </div>
            </a>
          </div>

          <div class="col-lg-3 col-md-6">
            <a href="vehicules.php" class="card action-card h-100 border-0 text-decoration-none">
              <div class="card-body text-center p-4">
                <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                  <i class="fas fa-car text-info fa-2x"></i>
                </div>
                <h6 class="text-info mb-2">Gérer les Véhicules</h6>
                <p class="text-muted small mb-0">Voir et modifier les véhicules</p>
              </div>
            </a>
          </div>

          <div class="col-lg-3 col-md-6">
            <a href="boutiques.php" class="card action-card h-100 border-0 text-decoration-none">
              <div class="card-body text-center p-4">
                <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                  <i class="fas fa-store text-success fa-2x"></i>
                </div>
                <h6 class="text-success mb-2">Gérer les Boutiques</h6>
                <p class="text-muted small mb-0">Voir et modifier les boutiques</p>
              </div>
            </a>
          </div>

          <div class="col-lg-3 col-md-6">
            <a href="techniciens.php" class="card action-card h-100 border-0 text-decoration-none">
              <div class="card-body text-center p-4">
                <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                  <i class="fas fa-user-cog text-danger fa-2x"></i>
                </div>
                <h6 class="text-danger mb-2">Gérer les Techniciens</h6>
                <p class="text-muted small mb-0">Voir et modifier les techniciens</p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row mb-5">
  <div class="col-lg-6 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-primary">
          <i class="fas fa-chart-pie me-2"></i>Statistiques des Rendez-vous
        </h5>
      </div>
      <div class="card-body">
        <div class="chart-container" style="position: relative; height: 300px;">
          <canvas id="appointmentsChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-primary">
          <i class="fas fa-chart-bar me-2"></i>Statistiques des Véhicules
        </h5>
      </div>
      <div class="card-body">
        <div class="chart-container" style="position: relative; height: 300px;">
          <canvas id="vehiclesChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.text-purple { color: #6f42c1 !important; }
.bg-purple { background-color: #6f42c1 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Appointments Chart
  const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
  new Chart(appointmentsCtx, {
    type: 'pie',
    data: {
      labels: ['Confirmés', 'En attente'],
      datasets: [{
        data: [<?= $stats['confirmed_appointments'] ?>, <?= $stats['pending_appointments'] ?>],
        backgroundColor: ['rgba(25, 135, 84, 0.8)', 'rgba(255, 193, 7, 0.8)'],
        borderColor: ['rgba(25, 135, 84, 1)', 'rgba(255, 193, 7, 1)'],
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });

  // Vehicles Chart
  const vehiclesCtx = document.getElementById('vehiclesChart').getContext('2d');
  new Chart(vehiclesCtx, {
    type: 'bar',
    data: {
      labels: ['Disponibles', 'Loués', 'Indisponibles'],
      datasets: [{
        label: 'Véhicules',
        data: [
          <?= $stats['available_vehicles'] ?>,
          <?= $stats['rented_vehicles'] ?>,
          <?= $stats['total_vehicles'] - $stats['available_vehicles'] - $stats['rented_vehicles'] ?>
        ],
        backgroundColor: [
          'rgba(23, 125, 255, 0.8)',
          'rgba(25, 135, 84, 0.8)',
          'rgba(220, 53, 69, 0.8)'
        ],
        borderColor: [
          'rgba(23, 125, 255, 1)',
          'rgba(25, 135, 84, 1)',
          'rgba(220, 53, 69, 1)'
        ],
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

