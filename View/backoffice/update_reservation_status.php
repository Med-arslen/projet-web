<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ReservController.php';
if(isset($_POST["id_reserv"])&& isset($_POST["status"])){
    $id_reserv = $_POST["id_reserv"];
    $state = $_POST["status"];
    $reservController = new ReservController();
    $result = $reservController->updateReservationState($id_reserv, $state);
   
    if($result){
        header("Location: list_reservation.php?msg=Reservation state updated successfully");
        exit;
    }else{
        header("Location: list_reservation.php?msg=Failed to update reservation state");
        exit;
    }
}
else{
    header("Location: list_reservation.php?msg=Invalid request");
    exit;
}
?>