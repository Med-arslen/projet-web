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

    <!-- External CSS -->
    <link href="../backoffice/css/add.css" rel="stylesheet">
    <link href="../backoffice/css/style.css" rel="stylesheet">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional Inline Styling -->
    <style>
       body::before {
  content: "";
  position: fixed;
  inset: 0;
  z-index: 0;
}

.wrapper {
  display: flex;
  height: 100vh;
  position: relative;
  z-index: 1;
}

/* SIDEBAR */
.sidebar {
  width: 220px;
  background-color: #050505;
  color: white;
  padding: 20px;
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
}

.menu a i {
  margin-right: 10px;
}

.menu a:hover,
.menu a.active {
  background-color: #ac7b7b;
  transform: scale(1.05);
}
    </style>
</head>
<body style="background: url('../backoffice/assets/imgs/cinema.jpg') no-repeat center center fixed; background-size: cover;">

<div class="wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <img src="../backoffice/assets/imgs/logo.png" alt="Logo MovieVibe" />
            <h2>MovieVibe</h2>
        </div>
        <nav class="menu">
            <a class="" data-attr="client"><i class="fas fa-user"></i> Client</a>
            <a data-attr="event" class="has-submenu" onclick="toggleSubMenu(this)">
                <i class="fas fa-calendar"></i> Événements
                <i class="fas fa-chevron-down submenu-icon"></i>
            </a>
            <div class="submenu" style="display: none;">
                <a data-attr="listeReservation"><i class="fas fa-list"></i> Liste des Réservations</a>
                <a id="newEventBtn" href="index.php"><i class="fas fa-plus-circle"></i> Nouveaux Événements</a>

            </div>
            <a data-attr="reclamation"><i class="fas fa-bell"></i> Réclamation</a>
            <a data-attr="produit"><i class="fas fa-shopping-cart"></i> Produits</a>
            <a data-attr="film"><i class="fas fa-film"></i> Films</a>
            <button id="quitBtn"><i class="fas fa-sign-out-alt"></i> Quitter</button>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="main-content">

        <nav class="navbar navbar-expand-lg mb-5 shadow-lg rounded-3" style="background: #8B0000; border-bottom: 4px solid #d3a5a5;">
            <div class="container justify-content-center">
                <span class="navbar-brand mb-0 h1 text-white fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 3rem;">
                    🎬 Add New Event
                </span>
            </div>
        </nav>

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
                    <input type="text" class="form-control" name="name_event" >
                    <span class="text-danger" id="error_name_event"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Location:</label>
                    <input type="text" class="form-control" name="location" >
                    <span class="text-danger" id="error_location"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Places:</label>
                    <input type="number" class="form-control" name="total_places" >
                    <span class="text-danger" id="error_total_places"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price (TND):</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="price_event" >
                        <span class="input-group-text">DT</span>
                    </div>
                    <span class="text-danger" id="error_price_event"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <textarea class="form-control" name="description" rows="3" ></textarea>
                    <span class="text-danger" id="error_description"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Film ID:</label>
                    <input type="number" class="form-control" name="id_film" >
                    <span class="text-danger" id="error_id_film"></span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Event Date & Time:</label>
                    <input type="datetime-local" class="form-control" name="date_event" >
                    <span class="text-danger" id="error_date_event"></span>
                </div>
                <div>
                    <button type="submit" class="btn btn-success" name="submit">Save</button>
                    <a href="index.php" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>

    </div> <!-- End main content -->

</div> <!-- End wrapper -->

<script src="../../public/js/add.js"></script>
<script>
document.querySelector("form").addEventListener("submit", function(e) {
    const name = document.querySelector('[name="name_event"]').value.trim();
    const location = document.querySelector('[name="location"]').value.trim();
    const totalPlaces = parseInt(document.querySelector('[name="total_places"]').value);
    const price = parseFloat(document.querySelector('[name="price_event"]').value);
    const description = document.querySelector('[name="description"]').value.trim();
    const filmId = parseInt(document.querySelector('[name="id_film"]').value);
    const dateEvent = new Date(document.querySelector('[name="date_event"]').value);
    const now = new Date();

    if (!name || !/[a-zA-Zàâçéèêëîïôûùüÿñæœ\s]/i.test(name)) {
        document.getElementById('error_name_event').textContent = "Event name is required.";
        e.preventDefault();
        return;
    }

    if (!location ){
        document.getElementById('error_location').textContent = "Location is required.";
        e.preventDefault();
        return;
    }
    if (!description ){
        document.getElementById('error_description').textContent = "Description is required.";
        e.preventDefault();
        return;
    }
    if (isNaN(totalPlaces) || totalPlaces <= 0) {
        document.getElementById('error_total_places').textContent = "Enter a valid number of places.";
        e.preventDefault();
        return;
    }

    if (isNaN(price) || price <= 0) {
        document.getElementById('error_price_event').textContent = "Enter a valid price.";
        e.preventDefault();
        return;
    }

    if (isNaN(filmId) || filmId <= 0) {
        document.getElementById('error_id_film').textContent = "Film ID doit etre positive";
        e.preventDefault();
        return;
    }

    if (isNaN(dateEvent.getTime()) || dateEvent <= now) {
        document.getElementById('error_date_event').textContent = "Date & time are required in the future";
        e.preventDefault();
        return;
    }
});

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
document.addEventListener("DOMContentLoaded", function () {
      const newEventBtn = document.getElementById("newEventBtn");
      const newEventSection = document.getElementById("nouveauxEvenementSection");

      const showTable = localStorage.getItem("showNewEvents");
      if (showTable === "true") {
        newEventSection.style.display = "block";
      } else {
        newEventSection.style.display = "none";
      }

      newEventBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const isVisible = newEventSection.style.display === "block";
        newEventSection.style.display = isVisible ? "none" : "block";
        localStorage.setItem("showNewEvents", !isVisible);
      });
    });
</script>

</body>
</html>
