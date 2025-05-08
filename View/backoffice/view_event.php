<?php
require_once "../../config/database.php";

$conn = config::getConnexion();

if (!isset($_GET['id'])) {
    header("Location: index.php?msg=No event selected");
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT event.*, film.title, film.photo_path 
        FROM event 
        JOIN film ON event.id_film = film.id_film 
        WHERE event.id_event = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: index.php?msg=Event not found");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Event</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f9;
      display: flex;
      height: 100vh;
    }

    .wrapper {
      display: flex;
      width: 100%;
    }

    .sidebar {
      width: 250px;
      background-color: #050505;
      color: white;
      padding: 20px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      overflow-y: auto;
    }

    .logo {
      text-align: center;
    }

    .logo img {
      width: 60px;
      margin-bottom: 10px;
    }

    .logo h2 {
      font-size: 20px;
      margin: 0;
    }

    .menu a {
      display: flex;
      align-items: center;
      padding: 10px;
      margin-top: 15px;
      text-decoration: none;
      color: #fff;
      border-radius: 8px;
      transition: 0.3s;
      cursor: pointer;
    }

    .menu a i {
      margin-right: 10px;
    }

    .menu a:hover,
    .menu a.active {
      background-color: #ac7b7b;
      transform: scale(1.05);
    }

    .main-content {
      margin-left: 250px;
      padding: 40px;
      flex: 1;
      background-color: #f4f6f9;
      min-height: 100vh;
      overflow-y: auto;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      padding: 30px;
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    h2 {
      font-size: 2rem;
      font-weight: bold;
      color: #333;
      margin-bottom: 20px;
    }

    h4 {
      font-size: 1.5rem;
      color: #555;
      margin-top: 10px;
    }

    .text-center img {
      max-width: 300px;
      width: 100%;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .text-center p {
      font-size: 1.2rem;
      color: #999;
    }

    .table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
    }

    .table th, .table td {
      padding: 12px;
      text-align: left;
      border: 1px solid #ddd;
      color: #333;
      background-color: #fafafa;
    }

    .table th {
      background-color: #f0f0f0;
      font-weight: bold;
    }

    .table tr:hover {
      background-color: #f1f1f1;
    }

    .btn-secondary {
      background-color: #6c757d;
      color: white;
      padding: 10px 20px;
      font-size: 1rem;
      border-radius: 5px;
      text-decoration: none;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
    }
  </style>
</head>

<!-- Use a fallback background color in case the image doesn't load -->
<body style="background: url('../backoffice/assets/imgs/cinema.jpg') no-repeat center center fixed; background-color: rgba(0, 0, 0, 0.5); background-size: cover;">

<div class="wrapper">
  <aside class="sidebar">
    <div class="logo">
      <img src="../backoffice/assets/imgs/logo.png" alt="Logo MovieVibe" />
      <h2>MovieVibe</h2>
    </div>
    <nav class="menu">
      <a data-attr="client"><i class="fas fa-user"></i> Client</a>

      <a data-attr="event" class="has-submenu" onclick="toggleSubMenu(this)">
        <i class="fas fa-calendar"></i> Événements
        <i class="fas fa-chevron-down submenu-icon"></i>
      </a>
      <div class="submenu" style="display: none;">
        <a href="./list_reservation.php" data-attr="listeReservation"><i class="fas fa-list"></i> Liste des Réservations</a>
        <a id="newEventBtn" href="#"><i class="fas fa-plus-circle"></i> Nouveaux Événements</a>
      </div>

      <a data-attr="reclamation"><i class="fas fa-bell"></i> Réclamation</a>
      <a data-attr="produit"><i class="fas fa-shopping-cart"></i> Produits</a>
      <a data-attr="film"><i class="fas fa-film"></i> Films</a>

      <div class="sidebar-footer mt-4">
        <button id="quitBtn" class="btn btn-danger w-100"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
      </div>
    </nav>
  </aside>

  <div class="main-content">
    <div class="container mt-5">
      <h2 class="mb-4 text-center">Event Details</h2>

      <div class="text-center mb-4">
        <?php if ($event['photo_path']): ?>
            <img src="<?= htmlspecialchars($event['photo_path']) ?>" alt="Film Image" class="img-fluid">
        <?php else: ?>
            <p>No image available for this film.</p>
        <?php endif; ?>
        <h4 class="mt-3"><?= htmlspecialchars($event['title']) ?></h4>
      </div>

      <table class="table table-bordered">
        <tr><th>ID</th><td><?= htmlspecialchars($event['id_event']) ?></td></tr>
        <tr><th>Title</th><td><?= htmlspecialchars($event['name_event']) ?></td></tr>
        <tr><th>Location</th><td><?= htmlspecialchars($event['location']) ?></td></tr>
        <tr><th>Total Places</th><td><?= htmlspecialchars($event['total_places']) ?></td></tr>
        <tr><th>Price</th><td><?= htmlspecialchars($event['price_event']) ?> DT</td></tr>
        <tr><th>Description</th><td><?= nl2br(htmlspecialchars($event['description'])) ?></td></tr>
        <tr><th>Film ID</th><td><?= htmlspecialchars($event['id_film']) ?></td></tr>
        <tr><th>Date & Time</th><td><?= date("Y-m-d H:i", strtotime($event['date_event'])) ?></td></tr>
      </table>

      <div class="text-center mt-4">
        <a href="index.php" class="btn btn-secondary">Back to Events</a>
      </div>
    </div>
  </div>
</div>

<script>
function toggleSubMenu(element) {
  const submenu = element.nextElementSibling;
  if (submenu && submenu.classList.contains("submenu")) {
    const isVisible = submenu.style.display === "block";
    submenu.style.display = isVisible ? "none" : "block";

    const icon = element.querySelector(".submenu-icon");
    if (icon) {
      icon.style.transform = isVisible ? "rotate(0deg)" : "rotate(180deg)";
    }
  }
}
</script>
</body>
</html>
