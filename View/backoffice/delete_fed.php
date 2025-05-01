<?php
require_once '../../controller/feedbackController.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_rec = $_GET['id']; // c'est bien l'id de la réclamation

    $feedbackController = new feedbackController();
    $feedbackController->deleteFeedback($id_rec);

    header('Location: listefed.php?msg=Feedbackk supprimé');
    exit;
} else {
    echo "ID de la réclamation manquant !";
}
?>
