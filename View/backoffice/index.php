<?php
require_once("../../config/database.php");
require_once("../../controller/ReclamationController.php");

// Initialisation de la connexion à la base de données
$conn = config::getConnexion();

// Récupération des statistiques avant le rendu HTML
$statsTypeRec = [];
try {
    $sqlStats = "SELECT type_rec, COUNT(*) as total FROM reclamationn GROUP BY type_rec";
    $stmtStats = $conn->query($sqlStats);
    $statsTypeRec = $stmtStats->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $statsTypeRec = [];
}

// Traitement QR code
if (isset($_GET['qrcode_id'])) {
    $id_rec = filter_var($_GET['qrcode_id'], FILTER_VALIDATE_INT);
    if ($id_rec === false) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'ID de réclamation invalide'
        ]);
        exit();
    }

    try {
        $response = ReclamationController::generateQRCodeData($conn, $id_rec);
        header('Content-Type: application/json');
        echo $response;
        exit();
    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Une erreur est survenue : ' . $e->getMessage()
        ]);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réclamations - MovieVibe</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Scripts nécessaires -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <style>
        #qrModal .modal-content {
            border-radius: 8px;
            overflow: hidden;
        }
        #qrModal .modal-header {
            border: none;
            padding: 0.5rem 1rem;
        }
        #qrModal .modal-body {
            padding: 1rem;
            background: white;
            text-align: center;
        }
        #qrCodeContainer {
            background: white;
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
        });

        function showQRCode(id) {
            const qrContainer = document.getElementById('qrCodeContainer');
            qrContainer.innerHTML = '';
            
            fetch(`index.php?qrcode_id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.error || 'Erreur lors de la génération du QR code');
                    }

                    const rec = data.reclamation;
                    const qrText = `ID: ${rec.id_rec}
Nom: ${rec.nomprenom}
Email: ${rec.email}
Film: ${rec.nomfilm}
Type: ${rec.type_rec}
Detail: ${rec.detail}
Reponse: ${rec.reponse_rec || 'Aucune réponse'}`;

                    new QRCode(qrContainer, {
                        text: qrText,
                        width: 250,
                        height: 250,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.L
                    });
                    
                    window.qrModal.show();
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    qrContainer.innerHTML = `<div class="alert alert-danger">Erreur: ${error.message}</div>`;
                    window.qrModal.show();
                });
        }

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
            const display = container.style.display === "none" ? "block" : "none";
            container.style.display = display;
            
            if (display === "block" && !container.hasAttribute("data-chart-initialized")) {
                const statsData = <?php echo json_encode($statsTypeRec); ?>;
                const labels = statsData.map(item => item.type_rec);
                const data = statsData.map(item => parseInt(item.total));
                
                const ctx = document.getElementById('statsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#4BC0C0',
                                '#9966FF'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw;
                                        const total = data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                
                container.setAttribute("data-chart-initialized", "true");
            }
        }
    </script>
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
            <a href="tri.php" class="btn btn-dark py-2 px-4">
              <i class="fas fa-sort me-2"></i> Trier Réclamation
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
                      <a href="delete_rec.php?id=<?= $row["id_rec"] ?>" class="link-dark" onclick="return confirm('Voulez-vous vraiment supprimer cette réclamation ?')">
    <i class="fa-solid fa-trash fs-5 me-3"></i>
</a>


                      <a href="#" onclick="showQRCode(<?= $row['id_rec'] ?>); return false;" class="link-dark">
                          <i class="fa-solid fa-qrcode fs-5"></i>
                      </a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
        
        <div class="text-center my-4">
          <button class="btn btn-primary" onclick="toggleStats()">📊 Afficher les Statistiques</button>
        </div>
        <div id="statsContainer" style="width: 400px; margin: auto; display: none;">
          <canvas id="statsChart"></canvas>
        </div>
      </section>
    </main>
  </div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 300px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scanner le QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrError" class="alert alert-danger" style="display: none;"></div>
                <div id="qrCodeContainer" class="p-3"></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
