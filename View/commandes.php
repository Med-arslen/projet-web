<?php
include '../config.php';
include '../Controller/CommandeController.php';

$controller = new CommandeController($pdo);
$commandes = $controller->getAllCommandes();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gestion des Commandes</title>
  <link rel="icon" href="logo.png" type="image/png">
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
  <div class="wrapper">
    <main class="main-content">
      <section class="commandes-section">
        <div class="content-header">
          <h1>Gestion des Commandes</h1>
          <p class="subtitle">Vue d'ensemble des commandes</p>
        </div>

        <div class="table-container">
          <table class="animated-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Produit</th>
                <th>Client</th>
                <th>Adresse</th>
                <th>Date</th>
                <th>Quantité</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($commandes as $commande): ?>
              <tr>
                <td><?= htmlspecialchars($commande['id']) ?></td>
                <td><?= htmlspecialchars($commande['id_produit']) ?></td>
                <td><?= htmlspecialchars($commande['nom_client']) ?></td>
                <td><?= htmlspecialchars($commande['adresse']) ?></td>
                <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                <td><?= htmlspecialchars($commande['quantite']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</body>
</html>