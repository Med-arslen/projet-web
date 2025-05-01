<?php
require_once '../../controller/ReclamationController.php';

// Créer une instance du contrôleur
$ReclamationController = new ReclamationController();

// Il n'est plus nécessaire de vérifier $_GET['sort'] car on veut uniquement un tri décroissant
// On définit directement 'DESC' comme ordre de tri
$sort_order = 'DESC'; // Tri décroissant par défaut

// Récupérer les réclamations triées
$reclamations = $ReclamationController->getReclamationsSorted($sort_order);

// Si la requête est en AJAX, retourner les résultats en JSON
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    echo json_encode($reclamations);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieVibe</title>
    <link rel="stylesheet" href="tri.css">
    <script defer src="../js/tri.js"></script>
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <img src="../backoffice/assets/img/logo.png" alt="Logo MovieVibe" />
      
      </div>
      <nav class="menu">
        <a href="../client/listeClient.php" class="<?= $activePage == 'listeClient.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-user"></i> Client
        </a>

        <a href="#" class="has-submenu" onclick="toggleSubMenu(this)">
          <i class="fa-solid fa-calendar"></i> Événements
          <i class="fa-solid fa-chevron-down submenu-icon"></i>
        </a>
        <div class="submenu" style="display: none;">
          <a href="../event/listeReservation.php" class="<?= $activePage == 'listeReservation.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-list"></i> Liste des Réservations
          </a>
          <a href="../event/add_event.php" class="<?= $activePage == 'add_event.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-plus-circle"></i> Nouveaux Événements
          </a>
        </div>

        <a href="index.php" class="<?= $activePage == 'index.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-bell"></i> Réclamation
        </a>

        <a href="listefed.php" class="<?= $activePage == 'listefed.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-comment"></i> Feedback
        </a>

        <a href="../produit/listeProduit.php" class="<?= $activePage == 'listeProduit.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-cart-shopping"></i> Produits
        </a>

        <a href="../film/listeFilm.php" class="<?= $activePage == 'listeFilm.php' ? 'active' : '' ?>">
          <i class="fa-solid fa-film"></i> Films
        </a>

        <button id="quitBtn"><i class="fa-solid fa-right-from-bracket"></i> Quitter</button>
      </nav>
    </aside>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg mb-5 shadow-lg rounded-3" style="background: #8B0000; border-bottom: 4px solid #d3a5a5;">
        <div class="container justify-content-center">
            <span class="navbar-brand mb-0 h1 text-white fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 3rem;">
                🎬 Trier les réclamations
            </span>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container">
        <h2 class="text-center mb-4">Liste des réclamations triées</h2>
        <button type="button" id="descButton" class="btn btn-danger mb-4">Trier du plus grand au plus petit</button>
        
        <table>
            <thead>
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
</body>
</html>
