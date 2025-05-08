<?php
// listEvents.php
require_once "../../config/database.php";

try {
    // Getting database connection
    $pdo = config::getConnexion();  // Ensure this is correct
    
    // SQL query to fetch event and associated film details
    $stmt = $pdo->query("SELECT event.id_event, event.name_event, event.date_event, film.title, film.photo_path
                         FROM event
                         JOIN film ON event.id_film = film.id_film");
                         
    // Fetch all results
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return events as JSON
    echo json_encode($events);
} catch (PDOException $e) {
    // Error handling
    echo json_encode(["error" => "Error fetching events: " . $e->getMessage()]);
}
?>
