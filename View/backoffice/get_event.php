<?php
require_once "../../config/database.php";

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID is required']);
    exit;
}

try {
    $conn = config::getConnexion();
    $stmt = $conn->prepare("SELECT * FROM event WHERE id_event = :id");
    $stmt->execute(['id' => $_GET['id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo json_encode(['success' => false, 'error' => 'Event not found']);
        exit;
    }

    // Format date for better display
    $event['date_event'] = date('Y-m-d H:i', strtotime($event['date_event']));
    
    echo json_encode(['success' => true, 'event' => $event]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}