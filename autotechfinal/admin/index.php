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

// Get monthly trends for charts (last 6 months)
$monthlyData = [];
$months = [];
for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m', strtotime("-$i months"));
    $months[] = date('M Y', strtotime("-$i months"));
    
    $monthlyData['users'][] = $pdo->query("SELECT COUNT(*) FROM utilisateur WHERE DATE_FORMAT(date_creation, '%Y-%m') = '$date'")->fetchColumn();
    $monthlyData['vehicles'][] = $pdo->query("SELECT COUNT(*) FROM vehicule WHERE DATE_FORMAT(created_at, '%Y-%m') = '$date'")->fetchColumn();
    $monthlyData['appointments'][] = $pdo->query("SELECT COUNT(*) FROM rendez_vous WHERE DATE_FORMAT(date_creation, '%Y-%m') = '$date'")->fetchColumn();
    $monthlyData['boutiques'][] = $pdo->query("SELECT COUNT(*) FROM boutique WHERE DATE_FORMAT(date_creation, '%Y-%m') = '$date'")->fetchColumn();
}

// Get boutique addresses for map (Tunisia coordinates as example)
$boutiques = $pdo->query("SELECT nom_boutique, adresse FROM boutique WHERE statut = 'actif' LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
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

<!-- Trends Chart Row -->
<div class="row mb-5">
  <div class="col-lg-12 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-primary">
          <i class="fas fa-chart-line me-2"></i>Évolution Mensuelle (6 Derniers Mois)
        </h5>
      </div>
      <div class="card-body">
        <div class="chart-container" style="position: relative; height: 400px;">
          <canvas id="trendsChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Map and Additional Charts Row -->
<div class="row mb-5">
  <div class="col-lg-8 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-primary">
          <i class="fas fa-map-marker-alt me-2"></i>Carte des Boutiques AutoTech
        </h5>
      </div>
      <div class="card-body">
        <div class="mapcontainer">
          <div id="world-map" class="w-100" style="height: 450px;"></div>
        </div>
        <div class="mt-3">
          <small class="text-muted">
            <i class="fas fa-info-circle"></i> 
            <?= count($boutiques) ?> boutiques actives affichées sur la carte
          </small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-primary">
          <i class="fas fa-chart-area me-2"></i>Statistiques Rapides
        </h5>
      </div>
      <div class="card-body">
        <div class="mb-4">
          <h6 class="text-muted mb-2">Utilisateurs</h6>
          <div id="sparklineUsers" class="mb-3"></div>
          <small class="text-muted">Total: <?= $stats['total_users'] ?></small>
        </div>
        <div class="mb-4">
          <h6 class="text-muted mb-2">Véhicules</h6>
          <div id="sparklineVehicles" class="mb-3"></div>
          <small class="text-muted">Total: <?= $stats['total_vehicles'] ?></small>
        </div>
        <div class="mb-4">
          <h6 class="text-muted mb-2">Rendez-vous</h6>
          <div id="sparklineAppointments" class="mb-3"></div>
          <small class="text-muted">Total: <?= $stats['total_appointments'] ?></small>
        </div>
        <div>
          <h6 class="text-muted mb-2">Boutiques</h6>
          <div id="sparklineBoutiques" class="mb-3"></div>
          <small class="text-muted">Total: <?= $stats['total_boutiques'] ?></small>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Additional Stats Charts -->
<div class="row mb-5">
  <div class="col-lg-6 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-primary">
          <i class="fas fa-chart-doughnut me-2"></i>Répartition des Utilisateurs
        </h5>
      </div>
      <div class="card-body">
        <div class="chart-container" style="position: relative; height: 300px;">
          <canvas id="usersChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-primary">
          <i class="fas fa-chart-bar me-2"></i>Activité par Catégorie
        </h5>
      </div>
      <div class="card-body">
        <div class="chart-container" style="position: relative; height: 300px;">
          <canvas id="activityChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.text-purple { color: #6f42c1 !important; }
.bg-purple { background-color: #6f42c1 !important; }
</style>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

<!-- Additional Scripts for Maps and Sparklines -->
<script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
<script src="assets/js/plugin/jsvectormap/world.js"></script>
<script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

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
          <?= max(0, $stats['total_vehicles'] - $stats['available_vehicles'] - $stats['rented_vehicles']) ?>
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

  // Trends Chart (Multiple Line)
  const trendsCtx = document.getElementById('trendsChart').getContext('2d');
  new Chart(trendsCtx, {
    type: 'line',
    data: {
      labels: <?= json_encode($months) ?>,
      datasets: [
        {
          label: 'Utilisateurs',
          borderColor: '#1d7af3',
          pointBorderColor: '#FFF',
          pointBackgroundColor: '#1d7af3',
          pointBorderWidth: 2,
          pointHoverRadius: 4,
          pointRadius: 4,
          backgroundColor: 'transparent',
          fill: true,
          borderWidth: 2,
          data: <?= json_encode($monthlyData['users']) ?>
        },
        {
          label: 'Véhicules',
          borderColor: '#59d05d',
          pointBorderColor: '#FFF',
          pointBackgroundColor: '#59d05d',
          pointBorderWidth: 2,
          pointHoverRadius: 4,
          pointRadius: 4,
          backgroundColor: 'transparent',
          fill: true,
          borderWidth: 2,
          data: <?= json_encode($monthlyData['vehicles']) ?>
        },
        {
          label: 'Rendez-vous',
          borderColor: '#f3545d',
          pointBorderColor: '#FFF',
          pointBackgroundColor: '#f3545d',
          pointBorderWidth: 2,
          pointHoverRadius: 4,
          pointRadius: 4,
          backgroundColor: 'transparent',
          fill: true,
          borderWidth: 2,
          data: <?= json_encode($monthlyData['appointments']) ?>
        },
        {
          label: 'Boutiques',
          borderColor: '#fdaf4b',
          pointBorderColor: '#FFF',
          pointBackgroundColor: '#fdaf4b',
          pointBorderWidth: 2,
          pointHoverRadius: 4,
          pointRadius: 4,
          backgroundColor: 'transparent',
          fill: true,
          borderWidth: 2,
          data: <?= json_encode($monthlyData['boutiques']) ?>
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top'
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Users Chart (Doughnut)
  const usersCtx = document.getElementById('usersChart').getContext('2d');
  new Chart(usersCtx, {
    type: 'doughnut',
    data: {
      labels: ['Actifs', 'Inactifs', 'Suspendus'],
      datasets: [{
        data: [
          <?= $stats['active_users'] ?>,
          <?= $pdo->query("SELECT COUNT(*) FROM utilisateur WHERE statut = 'inactif'")->fetchColumn() ?>,
          <?= $pdo->query("SELECT COUNT(*) FROM utilisateur WHERE statut = 'suspendu'")->fetchColumn() ?>
        ],
        backgroundColor: ['#1d7af3', '#fdaf4b', '#f3545d'],
        borderWidth: 0
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

  // Activity Chart (Bar)
  const activityCtx = document.getElementById('activityChart').getContext('2d');
  new Chart(activityCtx, {
    type: 'bar',
    data: {
      labels: ['Utilisateurs', 'Véhicules', 'Boutiques', 'Rendez-vous', 'Réservations', 'Trajets'],
      datasets: [{
        label: 'Total',
        data: [
          <?= $stats['total_users'] ?>,
          <?= $stats['total_vehicles'] ?>,
          <?= $stats['total_boutiques'] ?>,
          <?= $stats['total_appointments'] ?>,
          <?= $stats['total_reservations'] ?>,
          <?= $stats['total_trips'] ?>
        ],
        backgroundColor: [
          'rgba(29, 122, 243, 0.8)',
          'rgba(89, 208, 93, 0.8)',
          'rgba(253, 175, 75, 0.8)',
          'rgba(243, 84, 93, 0.8)',
          'rgba(111, 66, 193, 0.8)',
          'rgba(108, 117, 125, 0.8)'
        ],
        borderColor: [
          'rgba(29, 122, 243, 1)',
          'rgba(89, 208, 93, 1)',
          'rgba(253, 175, 75, 1)',
          'rgba(243, 84, 93, 1)',
          'rgba(111, 66, 193, 1)',
          'rgba(108, 117, 125, 1)'
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

  // Sparkline Charts
  $("#sparklineUsers").sparkline(<?= json_encode($monthlyData['users']) ?>, {
    type: "line",
    height: "50",
    width: "100%",
    lineWidth: "2",
    lineColor: "#1d7af3",
    fillColor: "rgba(29, 122, 243, 0.2)"
  });

  $("#sparklineVehicles").sparkline(<?= json_encode($monthlyData['vehicles']) ?>, {
    type: "line",
    height: "50",
    width: "100%",
    lineWidth: "2",
    lineColor: "#59d05d",
    fillColor: "rgba(89, 208, 93, 0.2)"
  });

  $("#sparklineAppointments").sparkline(<?= json_encode($monthlyData['appointments']) ?>, {
    type: "line",
    height: "50",
    width: "100%",
    lineWidth: "2",
    lineColor: "#f3545d",
    fillColor: "rgba(243, 84, 93, 0.2)"
  });

  $("#sparklineBoutiques").sparkline(<?= json_encode($monthlyData['boutiques']) ?>, {
    type: "line",
    height: "50",
    width: "100%",
    lineWidth: "2",
    lineColor: "#fdaf4b",
    fillColor: "rgba(253, 175, 75, 0.2)"
  });

  // World Map with Tunisia focus
  var world_map = new jsVectorMap({
    selector: "#world-map",
    map: "world",
    zoomOnScroll: false,
    regionStyle: {
      initial: {
        fill: "#e3eaef"
      },
      hover: {
        fill: "#435ebe"
      }
    },
    markers: [
      {
        name: "Tunis, Tunisie",
        coords: [36.8065, 10.1815],
        style: {
          fill: "#435ebe"
        }
      },
      {
        name: "Ariana, Tunisie",
        coords: [36.8601, 10.1934],
        style: {
          fill: "#28ab55"
        }
      },
      {
        name: "Sfax, Tunisie",
        coords: [34.7406, 10.7603],
        style: {
          fill: "#f3616d"
        }
      }
    ],
    onRegionTooltipShow(event, tooltip) {
      tooltip.css({ backgroundColor: "#435ebe" });
    },
    onMarkerClick(event, index) {
      // Handle marker click if needed
    }
  });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

