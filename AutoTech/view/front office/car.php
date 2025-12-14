<?php
require_once __DIR__ . '/../../controller/VehiculeController.php';

$vehiculeController = new VehiculeController();
$vehicules = $vehiculeController->getAllVehicules();
?>
<!DOCTYPE html>
<html lang="fr">


  <head>
    <title>AutoTech</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">

    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">

    
  </head>
  <body data-theme="dark">
    
	  <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="index.php"><img src="images/off_logo.png" alt="logo.png" id="img_logo"></a>
	      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
        aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> Menu
	      </button>

        <div class="collapse navbar-collapse" id="ftco-nav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a href="index.php" class="nav-link">Accueil</a></li>
            <li class="nav-item"><a href="about.html" class="nav-link">À propos</a></li>
            <li class="nav-item"><a href="services.html" class="nav-link">Services</a></li>
            <li class="nav-item active"><a href="car.php" class="nav-link">Voitures</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Venez me chercher!</a></li>
            <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li>
            <li class="nav-item"><a href="#" class="nav-link"><span class="icon icon-user"></span></a></li>
          </ul>
        </div>
	    </div>
	  </nav>
    <!-- END nav -->
    
    <section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image: url('images/deal3.webp');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-end justify-content-start">
          <div class="col-md-9 ftco-animate pb-5">
          	<p class="breadcrumbs">
              <span class="mr-2">
                <a href="index.php">Accueil <i class="ion-ios-arrow-forward"></i></a>
              </span>
              <span>Voitures <i class="ion-ios-arrow-forward"></i></span>
            </p>
            <h1 class="mb-3 bread">Réserver ou Acheter votre voiture</h1>
          </div>
        </div>
      </div>
    </section>
		
    <!-- LISTE DYNAMIQUE DES VOITURES -->
		<section class="ftco-section bg-light">
    	<div class="container">
    		<div class="row">

        <?php if (!empty($vehicules)): ?>
          <?php foreach ($vehicules as $v): ?>
    			<div class="col-md-4 mb-4">
    				<div class="car-wrap rounded ftco-animate">
    					<div class="img rounded d-flex align-items-end"
                 style="background-image: url('<?= 
                    !empty($v['image_principale'])
                      ? "../../uploads/" . htmlspecialchars($v['image_principale'])
                      : "images/car-1.jpg"; // image par défaut
                 ?>');">
    					</div>
    					<div class="text">
    						<h2 class="mb-0">
                  <a href="car-single.php?id=<?= $v['id_vehicule'] ?>">
                    <?= htmlspecialchars($v['marque'] . ' ' . $v['modele']) ?>
                  </a>
                </h2>
    						<div class="d-flex mb-3">
	    						<span class="cat"><?= htmlspecialchars($v['annee']) ?></span>
	    						<p class="price ml-auto">
                    <!-- Tu pourras remplacer par un vrai prix plus tard -->
                    <?= number_format((int)$v['kilometrage'], 0, ',', ' ') ?> km
                  </p>
    						</div>
    						<p class="d-flex mb-0 d-block">
                <!-- Boutons juste visuels pour l’instant -->
<a href="#=<?= $v['id_vehicule'] ?>" class="btn btn-primary py-2 mr-1">
    Réserver
</a>
                <a href="car-single.php?id=<?= $v['id_vehicule'] ?>" 
                   class="btn btn-secondary py-2 ml-1">
                  Détails
                </a>
                </p>
    					</div>
    				</div>
    			</div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12 text-center">
            <p class="mt-4 mb-0 text-muted">Aucun véhicule n’est disponible pour le moment.</p>
          </div>
        <?php endif; ?>

    		</div>

        <!-- Pagination (statique pour l’instant, tu pourras faire une vraie pagination plus tard) -->
    		<div class="row mt-5">
          <div class="col text-center">
            <div class="block-27">
              <ul>
                <li><a href="#">&lt;</a></li>
                <li class="active"><span>1</span></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">&gt;</a></li>
              </ul>
            </div>
          </div>
        </div>
    	</div>
    </section>
    
    <!-- FOOTER identique -->
    <footer class="ftco-footer ftco-bg-dark ftco-section">
      <div class="container">
        <!-- ton footer original ici (inchangé) -->
        <div class="row mb-5">
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">
                <a href="#" class="logo">
                  <img src="images/off_logo.png" alt="logo.png" id="img_logo">
                </a>
              </h2>
              <p>Autotech est conçu pour centraliser et simplifier l'expérience automobile dans un environnement digital de pointe, répondant à la demande croissante d'efficacité et de transparence.</p>
              <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
                <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4 ml-md-5">
              <h2 class="ftco-heading-2">Informations</h2>
              <ul class="list-unstyled">
                <li><a href="#" class="py-2 d-block">À propos</a></li>
                <li><a href="#" class="py-2 d-block">Services</a></li>
                <li><a href="#" class="py-2 d-block">Termes et Conditions</a></li>
                <li><a href="#" class="py-2 d-block">Garantie du Meilleur Prix</a></li>
                <li><a href="#" class="py-2 d-block">Politique de Confidentialité et Cookies</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Support Client</h2>
              <ul class="list-unstyled">
                <li><a href="#" class="py-2 d-block">FAQ</a></li>
                <li><a href="#" class="py-2 d-block">Option de Paiement</a></li>
                <li><a href="#" class="py-2 d-block">Conseils de Réservation</a></li>
                <li><a href="#" class="py-2 d-block">Comment ça marche</a></li>
                <li><a href="#" class="py-2 d-block">Nous Contacter</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Vous avez des Questions?</h2>
              <div class="block-23 mb-3">
                <ul>
                  <li><span class="icon icon-map-marker"></span><span class="text">Esprit, Ariana sogra, Ariana, Tunisie</span></li>
                  <li><a href="#"><span class="icon icon-phone"></span><span class="text">+216 33 856 909</span></a></li>
                  <li><a href="#"><span class="icon icon-envelope"></span><span class="text">AutoTech@gmail.tn</span></a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">
            <p>
              Copyright &copy;
              <script>document.write(new Date().getFullYear());</script> Tous droits réservés | AutoTech
            </p>
          </div>
        </div>
      </div>
    </footer>
    

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen">
    <svg class="circular" width="48px" height="48px">
      <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/>
      <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/>
    </svg>
  </div>

  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.animateNumber.min.js"></script>
  <script src="js/bootstrap-datepicker.js"></script>
  <script src="js/jquery.timepicker.min.js"></script>
  <script src="js/scrollax.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
  <script src="js/google-map.js"></script>
  <script src="js/main.js"></script>
    
  </body>
</html>
