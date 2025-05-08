<?php
require_once '../../vendor/autoload.php';
require_once '../../config/stripe_config.php';
require_once '../../config/database.php';

\Stripe\Stripe::setApiKey(STRIPE_API_KEY);

if (!isset($_GET['session_id'])) {
    echo "Invalid session.";
    exit;
}

$session_id = $_GET['session_id'];

try {
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    $reservationId = $session->client_reference_id;

    // Update the reservation state to "confirmed"
    $conn = config::getConnexion();
    $stmt = $conn->prepare("UPDATE reservation SET state = 'confirmed' WHERE id_reserv = ?");
    $stmt->execute([$reservationId]);

    echo "<h2>Payment Successful!</h2>";
    echo "<p>Reservation ID: $reservationId has been confirmed.</p>";
    echo '<a href="historique.php">Return to Reservations</a>';

} catch (\Stripe\Exception\ApiErrorException $e) {
    echo "Stripe error: " . $e->getMessage();
}
 catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
 catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>