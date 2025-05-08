<?php
require_once "../../config/database.php";
$conn = config::getConnexion();

if (isset($_GET['qrcode_event_id'])) {
    header('Content-Type: application/json');
    
    try {
        $stmt = $conn->prepare("SELECT * FROM event WHERE id_event = :id");
        $stmt->execute(['id' => $_GET['qrcode_event_id']]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            echo json_encode(['success' => false, 'error' => 'Event not found']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'evenement' => $event
        ]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Initialize variables
$search = "";
$results = [];
$sortOrder = "ASC"; // default value to prevent undefined variable

// Update sort order if provided
if (isset($_GET['order']) && ($_GET['order'] === "ASC" || $_GET['order'] === "DESC")) {
    $sortOrder = $_GET['order'];
}

// Search and sort logic
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = htmlspecialchars($_GET['search']);
    $sql = "SELECT * FROM event WHERE name_event LIKE :search ORDER BY date_event $sortOrder";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['search' => "%$search%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT * FROM event ORDER BY date_event $sortOrder";
    $stmt = $conn->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>event - MovieVibe</title>
  <link href="../backoffice/css/style.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script src="https://unpkg.com/html5-qrcode"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script defer src="script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #qrModal .modal-content {
      border-radius: 8px;
      overflow: hidden;
    }
    #qrModal .modal-header, #scannerModal .modal-header {
      border: none;
      padding: 0.5rem 1rem;
    }
    #qrModal .modal-body, #scannerModal .modal-body {
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
    #reader {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
    }
    #result {
      margin-top: 1rem;
    }
  </style>
</head>
<body style="background: url('../backoffice/assets/imgs/cinema.jpg') no-repeat center center fixed; background-size: cover;">

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

      <div class="sidebar-footer">
        <button id="quitBtn" class="quit-button"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
      </div>
        </nav>
  </aside>

  <main class="main-content">
    <section class="content">
      <div id="nouveauxEvenementSection">
        <div class="content-header">
          <h1>📅 Liste des événements</h1>
        </div>

        <header class="top-bar">
          <form method="GET" action="">
            <div class="search-zone d-flex align-items-center">
              <input type="text" name="search" id="searchEventInput" placeholder="🔍 Rechercher un événement..." value="<?= htmlspecialchars($search) ?>" class="form-control" style="max-width:300px;"/>
              <button type="submit" class="btn btn-primary ms-2">Rechercher</button>
            </div>
          </form>
        </header>

        <div class="container mt-4">
          <?php if (isset($_GET["msg"])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <?= htmlspecialchars($_GET["msg"]) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <div class="text-center mb-4">
            <a href="add_event.php" class="btn btn-dark py-2 px-4">
              <i class="fas fa-plus-circle me-2"></i> Ajouter un événement
            </a>
           
          </div>
        </div>

        <div class="table-box">
          <div class="table-responsive">
            <table id="eventTable" class="table table-hover text-center">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Titre</th>
                  <th>Emplacement</th>
                  <th>Places</th>
                  <th>Prix</th>
                  <th>Description</th>
                  <th>ID Film</th>
                  <th>
                    Date Événement
                    <a href="?<?php 
                      $newOrder = ($sortOrder === 'ASC') ? 'DESC' : 'ASC'; 
                      echo 'order=' . $newOrder . '&search=' . urlencode($search);
                    ?>">
                      <i class="fas <?= ($sortOrder === 'ASC') ? 'fa-sort-up' : 'fa-sort-down' ?>" style="color:white; margin-left:5px;"></i>
                    </a>
                  </th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($results) > 0): ?>
                  <?php foreach ($results as $row): ?>
                    <tr>
                      <td><?= htmlspecialchars($row["id_event"]) ?></td>
                      <td><?= htmlspecialchars($row["name_event"]) ?></td>
                      <td><?= htmlspecialchars($row["location"]) ?></td>
                      <td><?= htmlspecialchars($row["total_places"]) ?></td>
                      <td><?= htmlspecialchars($row["price_event"]) ?> DT</td>
                      <td><?= htmlspecialchars($row["description"]) ?></td>
                      <td><?= htmlspecialchars($row["id_film"]) ?></td>
                      <td><?= date("Y-m-d H:i", strtotime($row["date_event"])) ?></td>
                      <td>
                        <a href="edit_event.php?id=<?= $row['id_event'] ?>" class="link-dark" title="Modifier">
                          <i class="fa-solid fa-pen-to-square fs-5 me-3"></i>
                        </a>
                        <a href="delete_event.php?id=<?= $row['id_event'] ?>" class="link-dark" title="Supprimer">
                          <i class="fa-solid fa-trash fs-5 me-3"></i>
                        </a>
                        <a href="view_event.php?id=<?= $row['id_event'] ?>" class="link-dark" title="Voir">
                          <i class="fa-solid fa-eye fs-5"></i>
                        </a>
                        <a href="#" onclick="showQRCode(<?= $row['id_event'] ?>); return false;" class="link-dark">
                          <i class="fa-solid fa-qrcode fs-5"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="9">Aucun événement trouvé.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

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
                <div id="qrCodeContainer" class="p-3"></div>
            </div>
        </div>
    </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="scannerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Scanner QR Code</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="reader"></div>
        <div id="result"></div>
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

// Variable globale pour le modal
let qrModal = null;

// Fonction pour afficher le QR code
async function showQRCode(id) {
    if (!qrModal) {
        qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    }

    const qrContainer = document.getElementById('qrCodeContainer');
    qrContainer.innerHTML = '';

    try {
        const response = await fetch(`index.php?qrcode_event_id=${id}`);
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        
        const data = await response.json();
        if (!data || !data.success || !data.evenement) {
            throw new Error('Données invalides');
        }

        const event = data.evenement;
        const qrText = `${event.id_event}
${event.name_event}
${event.location}
${event.total_places}
${event.price_event}
${event.description}
${event.id_film}
${event.date_event}`;

        new QRCode(qrContainer, {
            text: qrText,
            width: 250,
            height: 250,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.L
        });

        qrModal.show();
    } catch (error) {
        console.error('Erreur:', error);
        qrContainer.innerHTML = `<div class="alert alert-danger">Une erreur s'est produite: ${error.message}</div>`;
        qrModal.show();
    }
}

let scannerModal = null;
let html5QrcodeScanner = null;

function openQRScanner() {
    if (!scannerModal) {
        scannerModal = new bootstrap.Modal(document.getElementById('scannerModal'));
    }
    
    if (!html5QrcodeScanner) {
        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: { width: 250, height: 250 } }
        );
        
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }
    
    scannerModal.show();
}

function onScanSuccess(decodedText, decodedResult) {
    try {
        const eventData = decodedText.split('\n');
        const eventId = eventData[0];
        
        window.location.href = `generate_pdf.php?id=${eventId}`;
        
    } catch (error) {
        document.getElementById('result').innerHTML = `
            <div class="alert alert-danger">
                Invalid QR Code format. Please scan a valid event QR code.
            </div>
        `;
    }
}

function onScanFailure(error) {
    console.warn(`Code scan error = ${error}`);
}

// Clean up scanner when modal is closed
document.getElementById('scannerModal').addEventListener('hidden.bs.modal', function () {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
        html5QrcodeScanner = null;
        document.getElementById('result').innerHTML = '';
    }
});
</script>
<script src="../backoffice/jsback/qrcode.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
