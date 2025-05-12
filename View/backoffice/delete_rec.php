<?php
require_once  '../../controller/ReclamationController.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    try {
        $ReclamationController = new ReclamationController();
        $ReclamationController->deleteReclamation($_GET['id']);
        header('Location: index.php?msg=Réclamation supprimée avec succès');
        exit();
    } catch (Exception $e) {
        header('Location: index.php?msg=Erreur lors de la suppression : ' . $e->getMessage());
        exit();
    }
} else {
    header('Location: index.php?msg=ID de réclamation manquant');
    exit();
}
?>
