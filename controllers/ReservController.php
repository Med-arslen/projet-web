<?php
include_once __DIR__ . '/../models/reserv.php';
include_once __DIR__ . '/../config/stripe_config.php';
include_once __DIR__ . '/../stripe/init.php'; // Adjust path as necessary
require_once '../../config/database.php';  // Adjust path as necessary

class ReservController {

    
    function addReservation($reservation)
    {
        var_dump($reservation); 
        
        $sql = "INSERT INTO reservation (date_reserv, event_id, nb_people, price, `state`, `type`, user_id)
                VALUES (:date_reserv, :event_id, :nb_people, :price, :state, :type, :user_id)";
        
        $db = config::getConnexion();
    
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'date_reserv'   => $reservation->getDateReservation()->format('Y-m-d H:i:s'),
                'event_id'     => $reservation->getIdEvent(),
                'nb_people'     => $reservation->getNbPlaces(),
                'price'         => $reservation->getPrice(),
                'state'         => $reservation->getState(),
                'type'          => $reservation->getType(),
                'user_id'       => $reservation->getIdClient()
            ]);
            return $query->rowCount() > 0; // Return true if the reservation was added successfully

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    public function makeReservation($eventId, $name, $email, $phone, $typeReservation, $numPeople) {
        global $conn;

        $sql = "SELECT total_places, price_event FROM event WHERE id_event = :event_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            return "Event not found!";
        }

        $availablePlaces = $event['total_places'];
        $eventPrice = $event['price_event'];

        if ($numPeople > $availablePlaces) {
            return "Not enough spots available!";
        }

        $newAvailablePlaces = $availablePlaces - $numPeople;
        $sql = "UPDATE event SET total_places = :new_available_places WHERE id_event = :event_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':new_available_places', $newAvailablePlaces, PDO::PARAM_INT);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "INSERT INTO reservations (event_id, name, email, phone, type_reservation, num_people) 
                VALUES (:event_id, :name, :email, :phone, :type_reservation, :num_people)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':type_reservation', $typeReservation, PDO::PARAM_STR);
        $stmt->bindParam(':num_people', $numPeople, PDO::PARAM_INT);
        $stmt->execute();

        return "Reservation successful!";
    }

    public function listResevation()
{
    $sql = "SELECT * FROM reservation";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);

        $query->execute();
        $liste = $query->fetch(PDO::FETCH_ASSOC);

        return $liste;
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}

public function deleteReservation($id)
{
    $sql = "DELETE FROM reservation WHERE id_reserv = :id";
    $db = config::getConnexion();

    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
}
public function getReservations() {

    // Prepare the SQL statement to fetch reservation details
    $sql = "SELECT id_reserv, user.id_user, user.name, email, name_event, reservation.type, date_reserv, nb_people, price, reservation.state 
            FROM user 
            INNER JOIN reservation ON user.id_user = reservation.user_id 
                INNER JOIN event ON event.id_event = reservation.event_id";
try {
    $conn = config::getConnexion();

    //$stmt = $conn->prepare($sql);
   // $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    //$stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
    $stmt = $conn->query($sql);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    return false;

}
    
        
}

public function updateReservationState($id, $state) {
    $sql = "UPDATE reservation SET `state` = :state WHERE id_reserv = :id";
    $db = config::getConnexion();

    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':state', $state, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0; // Return true if the reservation state was updated successfully
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
        return false;
    }
}

public function getReservationById($id) {
    $sql = "SELECT * FROM reservation WHERE id_reserv = :id";
    $db = config::getConnexion();
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Return the reservation details
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
        return false;
    }
}


public function pay()
{
    // This function should handle the payment process
    // You can use Stripe's API to create a payment intent and confirm the payment
    // For example:
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    /*$intent = \Stripe\PaymentIntent::create([
        'amount' => 1000, // Amount in cents
        'currency' => 'usd',
        'payment_method_types' => ['card'],
    ]);*/

    $token = $_POST['stripeToken'];
    $reservationId = $_POST['id_reservation'];
    $reservation = $this->getReservationById($reservationId);
    try {
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $reservation->getPrice(), // Amount in cents
            'currency' => 'usd',
            //'payment_method_types' => ['card'],
            'source' => $token,
            'description' => 'Reservation ID: ' . $reservationId,
            
            'metadata' => [
                'reservation_id' => $reservationId
            ]
        ]);
        $reservation->setState('paid');
        $this->updateReservationState($reservationId, 'paid');
    

        // Confirm the payment
        $intent->confirm($token);

        // Update the reservation state to "paid"
        $this->updateReservationState($reservationId, 'paid');
    } catch (Exeption $e) {
        echo 'Error: ' . $e->getMessage();
    }


    return $intent;
}

}

?>
