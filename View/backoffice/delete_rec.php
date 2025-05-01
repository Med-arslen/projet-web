<?php
require_once  '../../controller/ReclamationController.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $ReclamationController = new ReclamationController();
    $ReclamationController->deleteReclamation($_GET['id']);
    header('Location: index.php?msg=Reclamation deleted');
    exit;
} else {
    echo "Missing event ID!";
}
?>
