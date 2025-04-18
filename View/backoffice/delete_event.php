<?php

require_once '../../controllers/eventcontroller.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $eventController = new EventController();
    $eventController->deleteEvent($_GET['id']);
    header('Location: index.php?msg=Event deleted');
    exit;
} else {
    echo "Missing event ID!";
}
?>
 
/*$db = new dbConnect();
$conn = $db->getDbCon();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM event WHERE id_event = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: index.php?msg=Event deleted successfully");
            exit;
        } else {
            echo "Failed to delete the event.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Event ID is missing in the URL!";
}*/
?>
