<?php
include '../config.php';
include '../Controller/ProduitController.php';

if (isset($_GET['id'])) {
    $id_produit = $_GET['id'];
    $controller = new ProduitController($pdo);
    $produit = $controller->getProduitById($id_produit);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'] ?? '';
        $description = $_POST['description'] ?? '';
        $prix = $_POST['prix'] ?? 0;
        $quantite = $_POST['quantite'] ?? 0;
        $imageName = $produit->getImage();

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $imageName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
        }

        $controller->updateProduit($id_produit, $nom, $description, $prix, $quantite, $imageName);
        header('Location: index.php');
        exit;
    }
} else {
    echo "ID du produit non spécifié.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier un Produit</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrapper">
    <main class="main-content">
      <section class="produit-section">
        <div class="content-header">
          <h1>Modifier un Produit</h1>
          <p class="subtitle">Veuillez modifier les informations ci-dessous</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
          <div class="form-grid">
            <div class="form-group">
              <label for="nom">Nom</label>
              <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($produit->getNom()) ?>" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea name="description" id="description" required><?= htmlspecialchars($produit->getDescription()) ?></textarea>
            </div>
            <div class="form-group">
              <label for="prix">Prix</label>
              <input type="number" name="prix" id="prix" step="0.01" value="<?= htmlspecialchars($produit->getPrix()) ?>" required>
            </div>
            <div class="form-group">
              <label for="quantite">Quantité</label>
              <input type="number" name="quantite" id="quantite" value="<?= htmlspecialchars($produit->getQuantite()) ?>" required>
            </div>
            <div class="form-group">
              <label for="image">Image</label>
              <input type="file" name="image" id="image">
              <p>Image actuelle : <img src="../uploads/<?= htmlspecialchars($produit->getImage()) ?>" alt="Image du produit" width="50"></p>
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>
</html>