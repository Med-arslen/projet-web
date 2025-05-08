<?php

require_once '../../controllers/Reservcontroller.php';

if (isset($_POST['id']) && !empty($_POST['id'])) {
    $reservController = new ReservController();
    $reservController->deleteReservation($_POST['id']);
    header('Location: historique.php?msg=Reservation deleted');
    exit;
} else {
    echo "Missing reservation ID!";
}

?>