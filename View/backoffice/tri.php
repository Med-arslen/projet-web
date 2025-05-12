<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/ReclamationController.php';

// Créer une instance du contrôleur
$ReclamationController = new ReclamationController();

$sort_order = 'DESC';
$reclamations = $ReclamationController->getReclamationsSorted($sort_order);

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    echo json_encode($reclamations);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tri des Réclamations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css"/>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="logo">
                <img src="assets/img/logo.png" alt="Logo MovieVibe" />
                <h2>MovieVibe</h2>
            </div>
            <nav class="menu">
                <a href="../client/listeClient.php"><i class="fa-solid fa-user"></i> Client</a>
                <a href="#" class="has-submenu" onclick="toggleSubMenu(this)">
                    <i class="fa-solid fa-calendar"></i> Événements
                    <i class="fa-solid fa-chevron-down submenu-icon"></i>
                </a>
                <div class="submenu" style="display: none;">
                    <a href="../event/listeReservation.php"><i class="fa-solid fa-list"></i> Liste des Réservations</a>
                    <a href="../event/add_event.php"><i class="fa-solid fa-plus-circle"></i> Nouveaux Événements</a>
                </div>
                <a href="index.php" class="active"><i class="fa-solid fa-bell"></i> Réclamation</a>
                <a href="listefed.php"><i class="fa-solid fa-comment"></i> Feedback</a>
                <a href="../produit/listeProduit.php"><i class="fa-solid fa-cart-shopping"></i> Produits</a>
                <a href="../film/listeFilm.php"><i class="fa-solid fa-film"></i> Films</a>
                <button id="quitBtn"><i class="fa-solid fa-right-from-bracket"></i> Quitter</button>
            </nav>
        </aside>

        <main class="main-content">
            <section class="content">
                <div class="content-header">
                    <h1>📢 Tri des Réclamations</h1>
                </div>

                <header class="top-bar">
                    <button type="button" id="descButton" class="btn btn-dark">
                        <i class="fas fa-sort-amount-down"></i> Trier par ID
                    </button>
                </header>

                <div class="table-box">
                    <div class="table-responsive">
                        <table id="eventTable" class="table table-hover text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nom et Prénom</th>
                                    <th>Email</th>
                                    <th>Film</th>
                                    <th>Type de réclamation</th>
                                    <th>Détails</th>
                                    <th>Réponse</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reclamations as $reclamationn): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reclamationn['id_rec']); ?></td>
                                        <td><?php echo htmlspecialchars($reclamationn['nomprenom']); ?></td>
                                        <td><?php echo htmlspecialchars($reclamationn['email']); ?></td>
                                        <td><?php echo htmlspecialchars($reclamationn['nomfilm']); ?></td>
                                        <td><?php echo htmlspecialchars($reclamationn['type_rec']); ?></td>
                                        <td><?php echo htmlspecialchars($reclamationn['detail']); ?></td>
                                        <td><?php echo htmlspecialchars($reclamationn['reponse_rec']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSubMenu(element) {
            const submenu = element.nextElementSibling;
            submenu.style.display = submenu.style.display === "none" ? "block" : "none";
        }
    </script>
    <script src="js/tri.js"></script>
</body>
</html>
