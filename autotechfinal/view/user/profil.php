<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();

// Protection: Vérifier connexion
if (!$userController->estConnecte()) {
    header('Location: ../auth/login.php');
    exit();
}

$utilisateur = $userController->getUtilisateurConnecte();
$message = '';
$messageType = '';

// ======================
// UPDATE PROFIL
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'update_profil'
) {
    $photo_profil = null;

    // Upload photo
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] === 0) {
        $uploadDir = __DIR__ . '/../../uploads/profils/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($_FILES['photo_profil']['name'], PATHINFO_EXTENSION);
        $photo_profil = 'profil_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;

        move_uploaded_file(
            $_FILES['photo_profil']['tmp_name'],
            $uploadDir . $photo_profil
        );
    }

    $result = $userController->updateProfil(
        $_SESSION['user_id'],
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $_POST['adresse'],
        $_POST['ville'],
        $_POST['code_postal'],
        $photo_profil
    );

    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';

    // Reload user
    $utilisateur = $userController->getUtilisateurConnecte();
}

// ======================
// CHANGE PASSWORD
// ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'change_password'
) {
    $result = $userController->changerMotDePasse(
        $_SESSION['user_id'],
        $_POST['ancien_mot_de_passe'],
        $_POST['nouveau_mot_de_passe']
    );

    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'danger';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil - AutoTech</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/animate.css">
    <link rel="stylesheet" href="../../assets/css/flaticon.css">
    <link rel="stylesheet" href="../../assets/css/icomoon.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body data-theme="dark">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../public/index.php">
            <img src="../../images/off_logo.png" alt="logo" style="height:40px;">
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="../public/index.php">Accueil</a></li>
                <li class="nav-item active"><a class="nav-link" href="profil.php">Mon Profil</a></li>
                <li class="nav-item"><a class="nav-link" href="../auth/logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-wrap hero-wrap-2 js-fullheight" style="background-image:url('../../images/bg_1.jpg');">
    <div class="overlay"></div>
    <div class="container">
        <div class="row js-fullheight align-items-end">
            <div class="col-md-9 pb-5">
                <h1 class="mb-3 bread">Mon Profil</h1>
            </div>
        </div>
    </div>
</section>

<!-- CONTENT -->
<section class="ftco-section bg-light">
    <div class="container">

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- PROFILE HEADER -->
        <div class="row mb-4">
            <div class="col-md-12 text-center bg-white p-4 rounded">

                <?php
                $photo = $utilisateur['photo_profil'] ?? '';
                $photoFile = __DIR__ . '/../../uploads/profils/' . $photo;
                ?>

                <?php if (!empty($photo) && file_exists($photoFile)): ?>
                    <img src="../../uploads/profils/<?= htmlspecialchars($photo) ?>"
                         style="width:100px;height:100px;border-radius:50%;object-fit:cover;">
                <?php else: ?>
                    <div style="
                        width:100px;
                        height:100px;
                        border-radius:50%;
                        background:#667eea;
                        color:white;
                        font-size:36px;
                        font-weight:700;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        margin:0 auto 10px;
                    ">
                        <?= strtoupper(substr($utilisateur['prenom'],0,1) . substr($utilisateur['nom'],0,1)) ?>
                    </div>
                <?php endif; ?>

                <h3><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></h3>
                <p class="text-muted"><?= htmlspecialchars($utilisateur['email']) ?></p>
            </div>
        </div>

        <div class="row">

            <!-- UPDATE PROFIL -->
            <div class="col-md-6">
                <div class="bg-white p-4 rounded">
                    <h4>Informations personnelles</h4>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_profil">

                        <div class="form-group">
                            <label>Nom</label>
                            <input class="form-control" name="nom"
                                   value="<?= htmlspecialchars($utilisateur['nom']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Prénom</label>
                            <input class="form-control" name="prenom"
                                   value="<?= htmlspecialchars($utilisateur['prenom']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Téléphone</label>
                            <input class="form-control" name="telephone"
                                   value="<?= htmlspecialchars($utilisateur['telephone'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Adresse</label>
                            <textarea class="form-control" name="adresse"><?= htmlspecialchars($utilisateur['adresse'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Ville</label>
                            <input class="form-control" name="ville"
                                   value="<?= htmlspecialchars($utilisateur['ville'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Code Postal</label>
                            <input class="form-control" name="code_postal"
                                   value="<?= htmlspecialchars($utilisateur['code_postal'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Photo de profil</label>
                            <input type="file" class="form-control" name="photo_profil" accept="image/png, image/jpeg, image/jpg, image/webp">

                        </div>

                        <button class="btn btn-primary w-100">Mettre à jour</button>
                    </form>
                </div>
            </div>

            <!-- PASSWORD -->
            <div class="col-md-6">
                <div class="bg-white p-4 rounded">
                    <h4>Changer mot de passe</h4>
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">

                        <div class="form-group">
                            <label>Ancien mot de passe</label>
                            <input type="password" class="form-control" name="ancien_mot_de_passe">
                        </div>

                        <div class="form-group">
                            <label>Nouveau mot de passe</label>
                            <input type="password" class="form-control" name="nouveau_mot_de_passe">
                        </div>

                        <button class="btn btn-primary w-100">Changer</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<script src="../../assets/js/jquery.min.js"></script>
<script src="../../assets/js/bootstrap.min.js"></script>
</body>
</html>
