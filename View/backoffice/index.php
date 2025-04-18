<?php
require_once "../../config/database.php";

$conn = config::getConnexion();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>event - MovieVibe</title>
  <link href="../backoffice/css/style.css" rel="stylesheet">
  <script defer src="script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: url('../backoffice/assets/imgs/cinema.jpg') no-repeat center center fixed; background-size: cover;">
>
  <div class="wrapper">
    .
    <aside class="sidebar">
      <div class="logo">
        <img src="../backoffice/assets/imgs/logo.png" alt="Logo MovieVibe" />
        <h2>MovieVibe</h2>
      </div>
      <nav class="menu">
        <a class="active" data-attr="client"><i class="fas fa-user"></i> Client</a>

        <a data-attr="event" class="has-submenu" onclick="toggleSubMenu(this)">
          <i class="fas fa-calendar"></i> Événements
          <i class="fas fa-chevron-down submenu-icon"></i>
        </a>
        <div class="submenu" style="display: none;">
          <a data-attr="listeReservation"><i class="fas fa-list"></i> Liste des Réservations</a>
          <a id="newEventBtn" href="#"><i class="fas fa-plus-circle"></i> Nouveaux Événements</a>
        </div>

        <a data-attr="reclamation"><i class="fas fa-bell"></i> Réclamation</a>
        <a data-attr="produit"><i class="fas fa-shopping-cart"></i> Produits</a>
        <a data-attr="film"><i class="fas fa-film"></i> Films</a>

        <button id="quitBtn"><i class="fas fa-sign-out-alt"></i> Quitter</button>
      </nav>
    </aside>

    <main class="main-content">
      <section class="content">
        <div id="nouveauxEvenementSection" style="display: none;">
          <div class="content-header">
            <h1>📅 List of events</h1>
          </div>
          <header class="top-bar">
            <div class="search-zone">
              <input type="text" id="searchEventInput" placeholder="🔍 Rechercher un événement..." />
            </div>
          </header>

          <div class="container">
            <?php if (isset($_GET["msg"])): ?>
              <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET["msg"]) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>
            <div class="text-center mb-4">
              <a href="add_event.php" class="btn btn-dark py-2 px-4">
                <i class="fas fa-plus-circle me-2"></i> Add Event
              </a>
            </div>
          </div>

          <hr style="display: none;">
          <div class="table-box">
            <div class="table-responsive">
              <table id="eventTable" class="table table-hover text-center">
                <thead class="table-dark">
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Location</th>
                    <th scope="col">Places</th>
                    <th scope="col">Price</th>
                    <th scope="col">Description</th>
                    <th scope="col">Film ID</th>
                    <th scope="col">Event Date</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sql = "SELECT * FROM event";
                  $stmt = $conn->query($sql);
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  ?>
                    <tr>
                      <td><?= htmlspecialchars($row["id_event"]) ?></td>
                      <td><?= htmlspecialchars($row["name_event"]) ?></td>
                      <td><?= htmlspecialchars($row["location"]) ?></td>
                      <td><?= htmlspecialchars($row["total_places"]) ?></td>
                      <td><?= htmlspecialchars($row["price_event"]) ?> DT</td> <!-- Price with DT -->
                      <td><?= htmlspecialchars($row["description"]) ?></td>
                      <td><?= htmlspecialchars($row["id_film"]) ?></td>
                      <td><?= date("Y-m-d H:i", strtotime($row["date_event"])) ?></td> <!-- ✅ Date and time -->
                      <td>
                        <a href="edit_event.php?id=<?= $row["id_event"] ?>" class="link-dark"><i class="fa-solid fa-pen-to-square fs-5 me-3"></i></a>
                        <a href="delete_event.php?id=<?= $row["id_event"] ?>" class="link-dark"><i class="fa-solid fa-trash fs-5"></i></a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </main>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  

</body>
</html>
