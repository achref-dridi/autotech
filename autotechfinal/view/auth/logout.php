<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controller/UtilisateurController.php';

$userController = new UtilisateurController();
$userController->deconnecter();

header('Location: ../public/index.php');
exit();
?>
