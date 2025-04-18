<?php
require_once __DIR__ . '/../../config/database.php';

$conn = config::getConnexion();

$error = "";

if (isset($_POST["submit"])) {
    $name_event = $_POST['name_event'];
    $location = $_POST['location'];
    $total_places = $_POST['total_places'];
    $price_event = $_POST['price_event'];
    $description = $_POST['description'];
    $id_film = $_POST['id_film'];
    $date_event = $_POST['date_event'];
    $id_event = $_POST['id_event'];  
    try {
        $sql = "UPDATE event 
                SET name_event = :name_event, location = :location, total_places = :total_places, price_event = :price_event, 
                    description = :description, id_film = :id_film, date_event = :date_event 
                WHERE id_event = :id_event";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name_event' => $name_event,
            ':location' => $location,
            ':total_places' => $total_places,
            ':price_event' => $price_event,
            ':description' => $description,
            ':id_film' => $id_film,
            ':date_event' => $date_event,
            ':id_event' => $id_event  
        ]);

        header("Location: index.php?msg=Event updated successfully");
        exit;
    } catch (PDOException $e) {
        $error = "Error updating event: " . $e->getMessage();
    }
}

if (!isset($_GET["id"])) {
    echo "Event ID is missing in the URL!";
    exit;
}

$id = $_GET["id"];

try {
    $stmt = $conn->prepare("SELECT * FROM event WHERE id_event = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "Event not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error fetching event: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <link href="../../public/css/add.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../backoffice/css/add.css" rel="stylesheet">
</head>
<body style="background: url('cinema.jpg') no-repeat center center fixed; background-size: cover;">

<nav class="navbar navbar-expand-lg mb-5 shadow-lg rounded-3" style="background: #8B0000; border-bottom: 4px solid #d3a5a5;">
  <div class="container justify-content-center">
    <span class="navbar-brand mb-0 h1 text-white fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 3rem;">
      🎬 Edit Event
    </span>
  </div>
</nav>

<div class="container">
<div class="text-center mb-4">
   <p class="fs-2 fw-light" style="color: #ffd700; background: linear-gradient(to right, #f4a261, #e76f51); -webkit-background-clip: text; color: transparent; font-family: 'Roboto', sans-serif; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">
      Make changes and click save to update 
   </p>
   <div style="width: 60px; height: 3px; background-color: #f4a261; margin: 10px auto;"></div>
</div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="container d-flex justify-content-center">
        <form action="" method="post" style="width:50vw; min-width:300px;">
            <div class="mb-3">
                <label class="form-label">Event Name:</label>
                <input type="text" class="form-control" name="name_event" value="<?= htmlspecialchars($row['name_event']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Location:</label>
                <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($row['location']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Places:</label>
                <input type="number" class="form-control" name="total_places" value="<?= htmlspecialchars($row['total_places']) ?>" required>
            </div>
            <label class="form-label">Price (TND):</label>
    <div class="input-group">
        <input type="number" step="0.01" class="form-control" name="price_event" required>
        <span class="input-group-text">DT</span>
    </div>
            <div class="mb-3">
                <label class="form-label">Description:</label>
                <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($row['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Film ID:</label>
                <input type="number" class="form-control" name="id_film" value="<?= htmlspecialchars($row['id_film']) ?>" required>
            </div>
            <div class="mb-3">
            <label class="form-label">Event Date & Time:</label>
<input type="datetime-local" class="form-control" name="date_event" value="<?= date('Y-m-d\TH:i', strtotime($row['date_event'])) ?>" required>

            </div>
            <input type="hidden" name="id_event" value="<?= $id ?>">

            <div>
                <button type="submit" class="btn btn-success" name="submit">Save</button>
                <a href="index.php" class="btn btn-danger">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
