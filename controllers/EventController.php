<?php
include_once __DIR__ . '/../models/event.php'; 


include_once __DIR__ . '/../config/database.php'; 
class EventController{
    public function showEvent($id)
{
    $sql = "SELECT * FROM event WHERE id_event = :id_event";
    $db = config::getConnexion();
    try {
        $query = $db->prepare($sql);
        $query->execute([':id_event' => $id]);

        $event = $query->fetch(PDO::FETCH_ASSOC);
        return $event;
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
}

    public function listEvent()
{
    $sql = "SELECT * FROM event";
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

    function updateEvent($event, $id)
{
    var_dump($event); 
    
    try {
        $db = config::getConnexion();  

        $query = $db->prepare(
            'UPDATE event SET 
                name_event = :name_event,
                location = :location,
                total_places = :total_places,
                price_event = :price_event,
                description = :description,
                id_film = :id_film,
                date_event = :date_event
            WHERE id_event = :id_event'
        );

        $query->execute([
            'id_event' => $id,  
            'name_event' => $event->getNameEvent(),
            'location' => $event->getLocation(),
            'total_places' => $event->getTotalPlaces(),
            'price_event' => $event->getPriceEvent(),
            'description' => $event->getDescription(),
            'id_film' => $event->getIdFilm(),
            'date_event' => $event->getDateEvent()->format('Y-m-d')  
        ]);

        echo $query->rowCount() . " records UPDATED successfully <br>";  
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();  
    }
}

function addEvent($event)
{
    var_dump($event); 
    
    $sql = "INSERT INTO event (name_event, location, total_places, price_event, description, id_film, date_event)
            VALUES (:name_event, :location, :total_places, :price_event, :description, :id_film, :date_event)";
    
    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->execute([
            'name_event'   => $event->getNameEvent(),
            'location'     => $event->getLocation(),
            'total_places' => $event->getTotalPlaces(),
            'price_event'  => $event->getPriceEvent(),
            'description'  => $event->getDescription(),
            'id_film'      => $event->getIdFilm(),

            'date_event'   => $event->getDateEvent()->format('Y-m-d H:i:s') 
        ]);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

    

    public function deleteEvent($id)
    {
        $sql = "DELETE FROM event WHERE id_event = :id";
        $db = config::getConnexion();

        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    /*public function getEventPlaces($id)
    {
        $sql = "SELECT total_places FROM event WHERE id_event = :id_event";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([':id_event' => $id]);
    
            $total_places = $query->fetch(PDO::FETCH_ASSOC);
            return $total_places;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
   
        public function getEventDetails($eventId) {
            global $conn;
    
            $sql = "SELECT event.*, film.title, film.photo_path 
                    FROM event 
                    JOIN film ON event.id_film = film.id_film 
                    WHERE event.id_event = :event_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $stmt->execute();
    
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            return $event;
        }
    }*/
     public function placeAvailability($eventId){
        $sql = "SELECT total_places,price_event FROM event WHERE id_event = :id";
    $db = config::getConnexion();
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        $liste = $stmt->fetch(PDO::FETCH_ASSOC);
        return $liste;
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
    }
}
    
?>
    



