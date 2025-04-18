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
        <a href="#" data-tab="produits" class="tab-button active"><i class="fas fa-box"></i> Produits</a>
        <a href="#" data-tab="commandes" class="tab-button"><i class="fas fa-shopping-cart"></i> Commandes</a>
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
          <div class="form-actions">
            <a href="ajouterproduit.php" class="btn btn-primary">
              <i class="fas fa-plus"></i> Ajouter un produit
            </a>
          </div>
        </div>

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

        // Fonction de validation du formulaire
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

        // Écouteurs d'événements pour validation en temps réel
        document.getElementById('nom').addEventListener('input', () => {
            validateField('nom');
        });
        document.getElementById('description').addEventListener('input', () => {
            validateField('description');
        });
        document.getElementById('prix').addEventListener('input', () => {
            validateField('prix');
        });
        document.getElementById('quantite').addEventListener('input', () => {
            validateField('quantite');
        });
        document.getElementById('image').addEventListener('change', () => {
            validateField('image');
        });

        // Fonction pour valider un champ spécifique
        function validateField(fieldName) {
            const field = document.getElementById(fieldName);
            const warning = document.getElementById(`${fieldName}-warning`);
            let isValid = true;
            let message = '';

            switch(fieldName) {
                case 'nom':
                    const nom = field.value.trim();
                    if (nom.length < 3) {
                        message = 'Le nom du produit doit contenir au moins 3 caractères.';
                        isValid = false;
                    } else if (nom.length > 50) {
                        message = 'Le nom du produit ne doit pas dépasser 50 caractères.';
                        isValid = false;
                    }
                    break;

                case 'description':
                    const description = field.value.trim();
                    if (description.length < 10) {
                        message = 'La description doit contenir au moins 10 caractères.';
                        isValid = false;
                    } else if (description.length > 255) {
                        message = 'La description ne doit pas dépasser 255 caractères.';
                        isValid = false;
                    }
                    break;

                case 'prix':
                    const prix = parseFloat(field.value);
                    if (isNaN(prix) || prix <= 0) {
                        message = 'Le prix doit être un nombre positif.';
                        isValid = false;
                    }
                    break;

                case 'quantite':
                    const quantite = parseInt(field.value);
                    if (isNaN(quantite) || quantite < 0) {
                        message = 'La quantité doit être un nombre positif ou nul.';
                        isValid = false;
                    }
                    break;

                case 'image':
                    const image = field.value;
                    if (image) {
                        const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.webp)$/i;
                        if (!allowedExtensions.exec(image)) {
                            message = 'Format d\'image invalide. Seuls les formats JPG, JPEG, PNG, GIF et WEBP sont acceptés.';
                            isValid = false;
                        }
                    }
                    break;
            }

            warning.textContent = message;
            warning.style.display = isValid ? 'none' : 'block';
        }
      </script>
    </main>
  </div>
</body>
</html>
