<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ReservController.php';
require_once __DIR__ . '/../../models/reserv.php'; 

if (isset($_POST["submit"])) {
    $id_event  = $_POST['event_id'];
    $type     = $_POST['type_reservation'];
    $num_people = $_POST['num_people'];
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0; // Default to 0 if not set

    $id_client = $_POST['id_client'];
     
    //reservation date which is sysdate
    $reservation_date = new DateTime(); // Current date and time
    $reservation_date->setTimezone(new DateTimeZone('Africa/Tunis')); // Set timezone to Africa/Tunis
    // Format the rseservation date to 'Y-m-d H:i:s'
    $formatted_date = $reservation_date->format('Y-m-d H:i:s');
 
          
    $state = "pending"; // Default state
    $reservation = new Reservation(
        null, // ID will be auto-incremented
        $id_client,
        $id_event,
        $num_people,
        new DateTime(),
        $state,
        $type,
        $price
    );
    $reservController = new ReservController();
    $result = $reservController->addReservation($reservation);

    header("Location: historique.php?msg=reservation created successfully");
    exit;
}
?>