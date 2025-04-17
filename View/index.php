<?php
include 'C:\xampp\htdocs\projetweb\config.php';
include 'C:\xampp\htdocs\projetweb\Controller\ProduitController.php';
include 'C:\xampp\htdocs\projetweb\Controller\CommandeController.php';

$controller = new ProduitController($pdo);
$produits = $controller->getAllProduits();

$commandeController = new CommandeController($pdo);
$commandes = $commandeController->getAllCommandes();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $quantite = $_POST['quantite'] ?? 0;
    $imageName = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    }

    $controller->createProduit($nom, $description, $prix, $quantite, $imageName);
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="MovieVibe - Système de gestion de produits"/>
  <meta name="theme-color" content="#141414"/>
  <title>Gestion des Produits - MovieVibe</title>
  <link rel="stylesheet" href="style.css"/>
  <script defer src="script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
</head>
<body>
  <div class="wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="logo">
        <img src="logo.png" alt="Logo MovieVibe" />
        <h2>MovieVibe</h2>
      </div>
      <nav class="menu">
        <a data-attr="produits" class="active"><i class="fas fa-box"></i> Produits et Commandes</a>
      </nav>
      <div class="sidebar-footer">
        <button id="quitBtn" class="quit-button" onclick="window.location.href='../View/page.php';">
          <i class="fas fa-sign-out-alt"></i> Déconnexion
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <!-- TOP BAR -->
      <header class="top-bar"></header>

      <!-- Section Produits -->
      <section class="tab-content active" id="produits">
        <div class="content-header">
          <div class="title-section">
            <h1>Gestion des Produits</h1>
            <p class="subtitle">Vue d'ensemble de votre catalogue</p>
          </div>
        </div>
        <form name="produitForm" method="POST" enctype="multipart/form-data">
          <div class="form-grid">
            <div class="form-group">
              <label for="nom">Nom du produit</label>
              <input type="text" name="nom" id="nom" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea name="description" id="description" required></textarea>
            </div>
            <div class="form-group">
              <label for="prix">Prix</label>
              <input type="number" name="prix" id="prix" step="0.01" required>
            </div>
            <div class="form-group">
              <label for="quantite">Quantité</label>
              <input type="number" name="quantite" id="quantite" required>
            </div>
            <div class="form-group">
              <label for="image">Image</label>
              <input type="file" name="image" id="image">
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Enregistrer
            </button>
          </div>
        </form>

        <!-- Produits Table -->
        <div class="table-container">
          <table id="produitsTable" class="animated-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Image</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($produits as $produit): ?>
              <tr>
                <td><?= htmlspecialchars($produit->getId()) ?></td>
                <td><?= htmlspecialchars($produit->getNom()) ?></td>
                <td><?= htmlspecialchars($produit->getDescription()) ?></td>
                <td><?= htmlspecialchars($produit->getPrix()) ?></td>
                <td><?= htmlspecialchars($produit->getQuantite()) ?></td>
                <td><img src="../uploads/<?= htmlspecialchars($produit->getImage()) ?>" alt="Image du produit" width="50"></td>
                <td>
                  <a href="updateproduit.php?id=<?= $produit->getId() ?>" class="btn-edit">Modifier</a>
                  <a href="deleteproduit.php?id=<?= $produit->getId() ?>" class="btn-delete" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Section Commandes -->
      <section class="tab-content" id="commandes">
        <div class="content-header">
          <div class="title-section">
            <h1>Gestion des Commandes</h1>
            <p class="subtitle">Vue d'ensemble des commandes</p>
          </div>
        </div>

        <div class="table-container">
          <table id="commandesTable" class="animated-table">
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

      <script>
        // Script pour basculer entre les onglets Produits et Commandes
        document.querySelectorAll('.tab-button').forEach(button => {
          button.addEventListener('click', () => {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            button.classList.add('active');
            document.getElementById(button.dataset.tab).classList.add('active');
          });
        });
      </script>
    </main>
  </div>
</body>
</html>
