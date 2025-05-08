<?php
require_once '../../config/database.php';

$reservationId = $_GET['reservation_id'] ?? null;

echo "<h2>Payment Canceled</h2>";
if ($reservationId) {
    echo "<p>You canceled the payment for reservation #$reservationId.</p>";
}
echo '<a href="historique.php">Return to Reservations</a>';
?>