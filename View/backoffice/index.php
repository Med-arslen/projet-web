<?php
require_once("../../config/database.php");
require_once("../../controller/ReclamationController.php");

$conn = config::getConnexion();
$stats = ReclamationController::getStatistiquesTypeRec($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Réclamations - MovieVibe</title>
  <link rel="stylesheet" href="../backoffice/css/style.css"/>
  <script defer src="../js/script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .btn-action {
      padding: 4px 8px;
      border-radius: 5px;
    }
    .btn-edit i {
      color: #198754;
    }
    .btn-delete i {
      color: #dc3545;
    }
    form input, form textarea {
      margin-bottom: 10px;
      padding: 8px;
      width: 100%;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    form button {
      margin-top: 10px;
      padding: 10px 15px;
      background-color: #343a40;
      color: white;
      border: none;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <aside class="sidebar">
      <div class="logo">
        <img src="../backoffice/assets/img/logo.png" alt="Logo MovieVibe" />
        <h2>MovieVibe</h2>
      </div>
      <nav class="menu">
        <a data-attr="client"><i class="fa-solid fa-user"></i> Client</a>
        <a data-attr="event" class="has-submenu" onclick="toggleSubMenu(this)">
          <i class="fa-solid fa-calendar"></i> Événements
          <i class="fa-solid fa-chevron-down submenu-icon"></i>
        </a>
        <div class="submenu" style="display: none;">
          <a data-attr="listeReservation"><i class="fa-solid fa-list"></i> Liste des Réservations</a>
          <a id="newEventBtn" href="#"><i class="fa-solid fa-plus-circle"></i> Nouveaux Événements</a>
        </div>
        <a data-attr="reclamation" class="active"><i class="fa-solid fa-bell"></i> Réclamation</a>
        <a data-attr="feedback" href="listefed.php"><i class="fa-solid fa-comment"></i> Feedback</a>
        <a data-attr="produit"><i class="fa-solid fa-cart-shopping"></i> Produits</a>
        <a data-attr="film"><i class="fa-solid fa-film"></i> Films</a>
        <button id="quitBtn"><i class="fa-solid fa-right-from-bracket"></i> Quitter</button>
      </nav>
    </aside>

    <main class="main-content">
      <section class="content">
        <div id="reclamationSection">
          <div class="content-header">
            <h1>📢 Liste des Réclamations</h1>
          </div>
          <header class="top-bar">
            <div class="search-zone">
              <input type="text" id="searchEventInput" placeholder="🔍 Rechercher une réclamation..." onkeyup="filterTable()" />
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
            <a href="add_rec.php" class="btn btn-dark py-2 px-4">
              <i class="fas fa-plus-circle me-2"></i> Ajouter Réclamation
            </a>
          </div>
          <a href="tri.php" class="btn btn-dark py-2 px-4">
            <i class="fas fa-plus-circle me-2"></i> Trier Réclamation
          </a>
        </div>
      
        <hr style="display: none;">
        <div class="table-box">
          <div class="table-responsive">
            <table id="eventTable" class="table table-hover text-center">
              <thead class="table-dark">
                <tr>
                  <th scope="col">ID</th>
                  <th scope="col">Nom&Prenom</th>
                  <th scope="col">Email</th>
                  <th scope="col">NomFilm</th>
                  <th scope="col">Type réclamation</th>
                  <th scope="col">Detail</th>
                  <th scope="col">Reponse</th>
                  <th scope="col">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM reclamationn";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                  <tr>
                    <td><?= htmlspecialchars($row["id_rec"]) ?></td>
                    <td><?= htmlspecialchars($row["nomprenom"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["nomfilm"]) ?></td>
                    <td><?= htmlspecialchars($row["type_rec"]) ?></td>
                    <td><?= htmlspecialchars($row["detail"]) ?></td>
                    <td><?= htmlspecialchars($row["reponse_rec"]) ?></td>
                    <td>
                      <a href="edit_rec.php?id=<?= $row["id_rec"] ?>" class="link-dark"><i class="fa-solid fa-pen-to-square fs-5 me-3"></i></a>
                      <a href="delete_rec.php?id=<?= $row["id_rec"] ?>" class="link-dark"><i class="fa-solid fa-trash fs-5"></i></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Ajoute le bouton et le conteneur pour afficher le graphique -->
        <div class="text-center my-4">
          <button class="btn btn-primary" onclick="toggleStats()">📊 Afficher les Statistiques</button>
        </div>
        <div id="statsContainer" style="width: 400px; margin: auto; display: none;">
          <canvas id="statsChart"></canvas>
        </div>
        
      </section>
    </main>
  </div>

  <script>
    function toggleSubMenu(element) {
      const submenu = element.nextElementSibling;
      const isVisible = submenu.style.display === "block";
      submenu.style.display = isVisible ? "none" : "block";
      const icon = element.querySelector(".submenu-icon");
      if (icon) icon.style.transform = isVisible ? "rotate(0deg)" : "rotate(180deg)";
    }

    function filterTable() {
      const input = document.getElementById("searchEventInput").value.toLowerCase();
      const rows = document.querySelectorAll("#eventTable tbody tr");

      rows.forEach(row => {
        const cells = Array.from(row.getElementsByTagName("td"));
        const match = cells.some(td => td.textContent.toLowerCase().includes(input));
        row.style.display = match ? "" : "none";
      });
    }

    function toggleStats() {
      const container = document.getElementById("statsContainer");
      container.style.display = container.style.display === "none" ? "block" : "none";
    }

    const ctx = document.getElementById('statsChart').getContext('2d');
    const chartData = {
      labels: <?= json_encode(array_column($stats, 'type_rec')) ?>,
      datasets: [{
        data: <?= json_encode(array_column($stats, 'total')) ?>,
        backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#8bc34a'],
        borderWidth: 1
      }]
    };

    const statsChart = new Chart(ctx, {
      type: 'pie',
      data: chartData,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          },
          title: {
            display: true,
            text: 'Répartition des types de réclamations'
          }
        }
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
