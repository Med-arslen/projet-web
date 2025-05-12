<?php
require_once "../../config/database.php";
$conn = config::getConnexion();
$activePage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" href="../backoffice/assets/img/logo.png" type="image/png">
  <title>Feedback - MovieVibe</title>

  <link rel="stylesheet" href="../backoffice/css/style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .btn-action {
      padding: 4px 8px;
      border-radius: 5px;
    }
    .btn-edit i { color: #198754; }
    .btn-delete i { color: #dc3545; }
    .menu a.active {
      background-color: #ffd700;
      color: #000;
      font-weight: bold;
    }
    #chartContainer {
      display: none;
      justify-content: center;
      margin-top: 20px;
    }
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 25px;
      background-color: #28a745;
      color: white;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      z-index: 1000;
      opacity: 1;
      transition: opacity 0.5s ease-in-out;
    }
    .notification.fade-out {
      opacity: 0;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <?php if (isset($_GET["success"])): ?>
    <div id="notification" class="notification">
      Feedback ajouté avec succès !
    </div>
    <?php endif; ?>
    <aside class="sidebar">
      <div class="logo">
        <img src="../backoffice/assets/img/logo.png" alt="Logo MovieVibe" />
        <h2>MovieVibe</h2>
      </div>
      <nav class="menu">
        <a href="../client/listeClient.php" class="<?= $activePage == 'listeClient.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-user"></i> Client
        </a>
                <a href="../film/listeFilm.php" class="<?= $activePage == 'listeFilm.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-film"></i> Films
        </a>
                <a href="../index.php"><i class="fa-solid fa-cart-shopping"></i> Produits</a>

        <a href="#" class="has-submenu" onclick="toggleSubMenu(this)">
          <i class="fa-solid fa-calendar"></i> Événements
          <i class="fa-solid fa-chevron-down submenu-icon"></i>
        </a>
        <div class="submenu" style="display: none;">
          <a href="../event/listeReservation.php" class="<?= $activePage == 'listeReservation.php' ? 'active' : '' ?>">Liste des Réservations</a>
          <a href="../event/add_event.php" class="<?= $activePage == 'add_event.php' ? 'active' : '' ?>">Nouveaux Événements</a>
        </div>
        <a href="index.php" class="<?= $activePage == 'index.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-bell"></i> Réclamation
        </a>
        <a href="listefed.php" class="<?= $activePage == 'listefed.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-comment"></i> Feedback
        </a>
        </a>

        <button id="quitBtn"><i class="fa-solid fa-right-from-bracket"></i> Quitter</button>
      </nav>
    </aside>

    <main class="main-content">
      <section class="content">
        <div id="reclamationSection">
          <div class="content-header">
            <h1>📢 Liste des Feedback</h1>
          </div>
          <?php if (isset($_GET["msg"])): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET["msg"]) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

          <header class="top-bar">
            <div class="search-zone">
              <input type="text" id="searchEventInput" placeholder="🔍 Rechercher un feedback..." onkeyup="filterTable()" />
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
              <a href="add_fed.php" class="btn btn-dark py-2 px-4">
                <i class="fas fa-plus-circle me-2"></i> Ajouter Feedback
              </a>
              <a href="generate_pdf.php" class="btn btn-danger py-2 px-4">
                <i class="fas fa-file-pdf me-2"></i> Générer PDF
              </a>
            </div>

            <div class="table-box">
              <div class="table-responsive">
                <table id="eventTable" class="table table-hover text-center">
                  <thead class="table-dark">
                    <tr>
                      <th>ID FEEDBACK</th>
                      <th>Analyse</th>
                      <th>Conseil</th>
                      <th>Temps</th>
                      <th>Simplicité</th>
                      <th>ID REC</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT f.id_fed, f.analyse, f.conseil, f.temps, f.simplicite, r.id_rec
                            FROM reclamationn r
                            LEFT JOIN feedbackk f ON r.id_rec = f.id_rec";
                    $stmt = $conn->query($sql);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                      <tr>
                        <td><?= htmlspecialchars($row["id_fed"]) ?></td>
                        <td><?= htmlspecialchars($row["analyse"]) ?></td>
                        <td><?= htmlspecialchars($row["conseil"]) ?></td>
                        <td><?= htmlspecialchars($row["temps"]) ?></td>
                        <td><?= htmlspecialchars($row["simplicite"]) ?></td>
                        <td><?= htmlspecialchars($row["id_rec"]) ?></td>
                        <td>
                          <?php if (!empty($row["id_fed"])): ?>
                            <a href="edit_fed.php?id=<?= $row["id_rec"] ?>" class="btn-action btn-edit" title="Modifier">
                              <i class="fa-solid fa-pen-to-square fs-5 me-2"></i>
                            </a>
                            <a href="delete_fed.php?id=<?= $row["id_rec"] ?>" class="btn-action btn-delete" title="Supprimer">
                              <i class="fa-solid fa-trash fs-5"></i>
                            </a>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- BOUTON STAT -->
            <div class="text-center mt-4">
              <button class="btn btn-warning" onclick="toggleStats()">
                📊 Afficher les Statistiques
              </button>
            </div>

            <!-- CHART -->
            <div id="chartContainer" class="text-center">
              <canvas id="feedbackChart" width="200" height="200"></canvas>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Notification auto-disparition
    document.addEventListener('DOMContentLoaded', function() {
      const notification = document.getElementById('notification');
      if (notification) {
        setTimeout(() => {
          notification.classList.add('fade-out');
          setTimeout(() => {
            notification.remove();
          }, 500);
        }, 4000);
      }
    });

    function toggleSubMenu(element) {
      const submenu = element.nextElementSibling;
      submenu.style.display = submenu.style.display === "block" ? "none" : "block";
      const icon = element.querySelector(".submenu-icon");
      if (icon) icon.style.transform = submenu.style.display === "block" ? "rotate(180deg)" : "rotate(0deg)";
    }

    function filterTable() {
      const input = document.getElementById("searchEventInput").value.toLowerCase();
      const rows = document.querySelectorAll("#eventTable tbody tr");
      rows.forEach(row => {
        const match = Array.from(row.getElementsByTagName("td")).some(td =>
          td.textContent.toLowerCase().includes(input)
        );
        row.style.display = match ? "" : "none";
      });
    }

    function toggleStats() {
      const chartContainer = document.getElementById("chartContainer");
      chartContainer.style.display = chartContainer.style.display === "none" ? "flex" : "none";
    }

    // Graphique statique exemple
    document.addEventListener("DOMContentLoaded", function () {
      const ctx = document.getElementById("feedbackChart").getContext("2d");
      new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: ["Positifs", "Neutres", "Négatifs"],
          datasets: [{
            label: "Feedback",
            data: [5, 3, 2], // <-- exemple statique à remplacer par dynamique plus tard
            backgroundColor: ["#198754", "#ffc107", "#dc3545"],
            hoverOffset: 4,
          }],
        },
        options: {
          responsive: false,
          plugins: {
            legend: {
              position: "bottom",
            }
          }
        },
      });
    });
  </script>
</body>
</html>
