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

        <!-- Message d'avertissement -->
        <div id="warning-message" class="warning-message" style="display: none; color: #e50914; background-color: rgba(229, 9, 20, 0.1); padding: 10px; margin-bottom: 20px; border-radius: 4px;"></div>

        <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" novalidate>
          <div class="form-grid">
            <div class="form-group">
              <label for="nom">Nom</label>
              <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($produit->getNom()) ?>">
              <div class="warning-message" id="nom-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea name="description" id="description"><?= htmlspecialchars($produit->getDescription()) ?></textarea>
              <div class="warning-message" id="description-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
            </div>
            <div class="form-group">
              <label for="prix">Prix</label>
              <input type="number" name="prix" id="prix" step="0.01" value="<?= htmlspecialchars($produit->getPrix()) ?>">
              <div class="warning-message" id="prix-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
            </div>
            <div class="form-group">
              <label for="quantite">Quantité</label>
              <input type="number" name="quantite" id="quantite" value="<?= htmlspecialchars($produit->getQuantite()) ?>">
              <div class="warning-message" id="quantite-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
            </div>
            <div class="form-group">
              <label for="image">Image</label>
              <input type="file" name="image" id="image" accept="image/*">
              <div class="warning-message" id="image-warning" style="display: none; color: #e50914; font-size: 0.8em; margin-top: 5px;"></div>
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

        // Validation de l'image (uniquement si une nouvelle image est sélectionnée)
        if (image) {
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
</body>
</html>