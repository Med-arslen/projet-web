<?php
require_once "../../config/database.php";
$conn = config::getConnexion();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR Code - MovieVibe</title>
    <link href="../backoffice/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body style="background: url('../backoffice/assets/imgs/cinema.jpg') no-repeat center center fixed; background-size: cover;">
    <div class="wrapper">
        <aside class="sidebar">
            <div class="logo">
                <img src="../backoffice/assets/imgs/logo.png" alt="Logo MovieVibe" />
                <h2>MovieVibe</h2>
            </div>
            <nav class="menu">
                <!-- Same sidebar menu as index.php -->
                <a data-attr="client"><i class="fas fa-user"></i> Client</a>
                <a data-attr="event" class="has-submenu" onclick="toggleSubMenu(this)">
                    <i class="fas fa-calendar"></i> Événements
                    <i class="fas fa-chevron-down submenu-icon"></i>
                </a>
                <div class="submenu" style="display: none;">
                    <a href="./list_reservation.php" data-attr="listeReservation"><i class="fas fa-list"></i> Liste des Réservations</a>
                    <a href="index.php" id="newEventBtn"><i class="fas fa-plus-circle"></i> Nouveaux Événements</a>
                </div>
                <a data-attr="reclamation"><i class="fas fa-bell"></i> Réclamation</a>
                <a data-attr="produit"><i class="fas fa-shopping-cart"></i> Produits</a>
                <a data-attr="film"><i class="fas fa-film"></i> Films</a>
                <button id="quitBtn"><i class="fas fa-sign-out-alt"></i> Quitter</button>
            </nav>
        </aside>

        <main class="main-content">
            <div class="container mt-4">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h3 class="mb-0">Scanner QR Code</h3>
                            </div>
                            <div class="card-body">
                                <div id="reader"></div>
                                <div id="result" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            try {
                const eventData = decodedText.split('\n');
                const eventId = eventData[0];

                // Fetch event data from database
                fetch(`get_event.php?id=${eventId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.error || 'Failed to fetch event data');
                        }

                        const event = data.event;
                        const resultHtml = `
                            <div class="alert alert-success">
                                <h4>Event Details:</h4>
                                <p><strong>Event Name:</strong> ${event.name_event}</p>
                                <p><strong>Location:</strong> ${event.location}</p>
                                <p><strong>Total Places:</strong> ${event.total_places}</p>
                                <p><strong>Price:</strong> ${event.price_event} DT</p>
                                <p><strong>Description:</strong> ${event.description}</p>
                                <p><strong>Date:</strong> ${event.date_event}</p>
                                <a href="view_event.php?id=${event.id_event}" class="btn btn-primary mt-3">
                                    <i class="fas fa-eye"></i> View Full Details
                                </a>
                            </div>
                        `;
                        document.getElementById('result').innerHTML = resultHtml;
                    })
                    .catch(error => {
                        document.getElementById('result').innerHTML = `
                            <div class="alert alert-danger">
                                ${error.message}
                            </div>
                        `;
                    });
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

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: { width: 250, height: 250 } }
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

        // Sidebar submenu toggle
        function toggleSubMenu(element) {
            const submenu = element.nextElementSibling;
            if (submenu && submenu.classList.contains("submenu")) {
                submenu.style.display = submenu.style.display === "block" ? "none" : "block";
            }
        }
    </script>
</body>
</html>