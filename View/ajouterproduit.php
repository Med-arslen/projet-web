<?php
include '../config.php';
include '../Controller/ProduitController.php';

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

    $controller = new ProduitController($pdo);
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
  <meta name="description" content="MovieVibe - Ajouter un produit"/>
  <meta name="theme-color" content="#141414"/>
  <title>Ajouter un Produit - MovieVibe</title>
  <link rel="icon" href="logo.png" type="image/png">
  <link rel="stylesheet" href="style.css"/>
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
        <a href="index.php" class="tab-button"><i class="fas fa-box"></i> Retour</a>
      </nav>
      <div class="sidebar-footer">
        <button id="quitBtn" class="quit-button" onclick="window.location.href='../View/page.php';">
          <i class="fas fa-sign-out-alt"></i> Déconnexion
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <section class="tab-content active">
        <div class="content-header">
          <div class="title-section">
            <h1>Ajouter un Produit</h1>
            <p class="subtitle">Remplissez le formulaire pour ajouter un nouveau produit</p>
          </div>
        </div>
        
        <div id="warning-message" class="warning-message" style="display: none; color: #e50914; background-color: rgba(229, 9, 20, 0.1); padding: 10px; margin-bottom: 20px; border-radius: 4px;"></div>

        <form name="produitForm" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" novalidate>
          <div class="form-grid">
            <div class="form-group">
              <label for="nom">Nom du produit</label>
              <input type="text" name="nom" id="nom">
              <div class="warning-message" id="nom-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea name="description" id="description"></textarea>
              <div class="warning-message" id="description-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
            </div>
            <div class="form-group">
              <label for="prix">Prix</label>
              <input type="number" name="prix" id="prix" step="0.01">
              <div class="warning-message" id="prix-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
            </div>
            <div class="form-group">
              <label for="quantite">Quantité</label>
              <input type="number" name="quantite" id="quantite">
              <div class="warning-message" id="quantite-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
            </div>
            <div class="form-group">
              <label for="image">Image</label>
              <input type="file" name="image" id="image">
              <div class="warning-message" id="image-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Enregistrer
            </button>
          </div>
        </form>
      </section>
    </main>
  </div>

  <script>
    function validateForm() {
        const nom = document.getElementById('nom').value.trim();
        const description = document.getElementById('description').value.trim();
        const prix = parseFloat(document.getElementById('prix').value);
        const quantite = parseInt(document.getElementById('quantite').value);
        const image = document.getElementById('image').value;
        let isValid = true;

        // Réinitialiser tous les messages d'erreur
        document.querySelectorAll('.warning-message').forEach(div => {
            div.style.display = 'none';
            div.textContent = '';
        });

        // Validation du nom
        if (nom.length < 3) {
            document.getElementById('nom-warning').textContent = 'Le nom du produit doit contenir au moins 3 caractères.';
            document.getElementById('nom-warning').style.display = 'block';
            isValid = false;
        } else if (nom.length > 50) {
            document.getElementById('nom-warning').textContent = 'Le nom du produit ne doit pas dépasser 50 caractères.';
            document.getElementById('nom-warning').style.display = 'block';
            isValid = false;
        }

        // Validation de la description
        if (description.length < 10) {
            document.getElementById('description-warning').textContent = 'La description doit contenir au moins 10 caractères.';
            document.getElementById('description-warning').style.display = 'block';
            isValid = false;
        } else if (description.length > 255) {
            document.getElementById('description-warning').textContent = 'La description ne doit pas dépasser 255 caractères.';
            document.getElementById('description-warning').style.display = 'block';
            isValid = false;
        }

        // Validation du prix
        if (isNaN(prix) || prix <= 0) {
            document.getElementById('prix-warning').textContent = 'Le prix doit être un nombre positif.';
            document.getElementById('prix-warning').style.display = 'block';
            isValid = false;
        }

        // Validation de la quantité
        if (isNaN(quantite) || quantite < 0) {
            document.getElementById('quantite-warning').textContent = 'La quantité doit être un nombre positif ou nul.';
            document.getElementById('quantite-warning').style.display = 'block';
            isValid = false;
        }

        // Validation de l'image
        if (!image) {
            document.getElementById('image-warning').textContent = 'Veuillez sélectionner une image pour le produit.';
            document.getElementById('image-warning').style.display = 'block';
            isValid = false;
        } else {
            const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.webp)$/i;
            if (!allowedExtensions.exec(image)) {
                document.getElementById('image-warning').textContent = 'Format d\'image invalide. Seuls les formats JPG, JPEG, PNG, GIF et WEBP sont acceptés.';
                document.getElementById('image-warning').style.display = 'block';
                isValid = false;
            }
        }

        return isValid;
    }
  </script>
</body>
</html>