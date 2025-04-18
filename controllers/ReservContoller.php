<?php
include_once 'models/reserv.php'; // Include the Reservation model

class ReservController {

    
    public function addReservation($event_id, $people_count, $user_details) {
 
        $eventController = new EventController();
        $result = $eventController->checkAvailableSpots($event_id, $people_count); 
        if ($result['status'] === 'success') {

            $reservModel = new Reserv();
            $reservModel->createReservation($event_id, $people_count, $user_details);
            echo json_encode(["status" => "success", "message" => "Reservation successfully created!"]);
        } else {
         
            echo json_encode($result);
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
}
?>
