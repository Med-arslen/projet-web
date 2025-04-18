<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/EventController.php';
require_once __DIR__ . '/../../models/event.php'; 

if (isset($_POST["submit"])) {
   
    $name_event   = $_POST['name_event'];
    $location     = $_POST['location'];
    $total_places = $_POST['total_places'];
    $price_event  = $_POST['price_event'];
    $description  = $_POST['description'];
    $id_film      = $_POST['id_film'];
    $date_event_input = $_POST['date_event'];  

    $datetime = DateTime::createFromFormat('Y-m-d\TH:i', $date_event_input);  

  
    $date_event = $datetime ? $datetime->format('Y-m-d H:i:s') : null;  

   
    $id_event = null;

  
    $event = new Event(
        $id_event,
        $name_event,
        $location,
        $total_places,
        $price_event,
        $description,
        $id_film,
        new DateTime($date_event)
    );

    $eventController = new EventController();
    $eventController->addEvent($event);


    header("Location: index.php?msg=Event created successfully");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Event</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../backoffice/css/add.css" rel="stylesheet">
    
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: url('cinema.jpg') no-repeat center center fixed; background-size: cover;">


<nav class="navbar navbar-expand-lg mb-5 shadow-lg rounded-3" style="background: #8B0000; border-bottom: 4px solid #d3a5a5;">
    <div class="container justify-content-center">
        <span class="navbar-brand mb-0 h1 text-white fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 3rem;">
            🎬 Add New Event
        </span>
    </div>
</nav>


<div class="container">
    <div class="text-center mb-4">
        <p class="fs-2 fw-light" style="color: #ffd700; background: linear-gradient(to right, #f4a261, #e76f51); -webkit-background-clip: text; color: transparent; font-family: 'Roboto', sans-serif; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">
            Fill in the details below
        </p>
        <div style="width: 60px; height: 3px; background-color: #f4a261; margin: 10px auto;"></div>
    </div>

    <div class="container d-flex justify-content-center">
        <form action="" method="post" style="width:50vw; min-width:300px;">
            <div class="mb-3">
                <label class="form-label">Event Name:</label>
                <input type="text" class="form-control" name="name_event" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Location:</label>
                <input type="text" class="form-control" name="location" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Places:</label>
                <input type="number" class="form-control" name="total_places" required>
            </div>
            <div class="mb-3">
    <label class="form-label">Price (TND):</label>
    <div class="input-group">
        <input type="number" step="0.01" class="form-control" name="price_event" required>
        <span class="input-group-text">DT</span>
    </div>
</div>
            <div class="mb-3">
                <label class="form-label">Description:</label>
                <textarea class="form-control" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Film ID:</label>
                <input type="number" class="form-control" name="id_film" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Event Date & Time:</label>
                <input type="datetime-local" class="form-control" name="date_event" required>
            </div>
            <div>
                <button type="submit" class="btn btn-success" name="submit">Save</button>
                <a href="index.php" class="btn btn-danger">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="../../public/js/add.js"></script>
</body>  
</html>
