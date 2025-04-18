<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Boutique</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js" integrity="sha384-3LK/3kTpDE/Pkp8gTNp2gR/2gOiwQ6QaO7Td0zV76UFJVhqLl4Vl3KL1We6q6wR9" crossorigin="anonymous"></script>
  <script defer src="scriptpage.js"></script>
  <style>
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.9);
    }

    .modal-content {
      margin: auto;
      display: block;
      width: 90%;
      max-width: 1000px;
      background-color: #141414;
      padding: 30px;
      border-radius: 12px;
      position: relative;
      top: 50%;
      transform: translateY(-50%);
      color: #ffffff;
      box-shadow: 0 0 20px rgba(255, 0, 0, 0.2);
    }

    .modal-close {
      position: absolute;
      right: 20px;
      top: 20px;
      color: #ffffff;
      font-size: 30px;
      cursor: pointer;
      z-index: 1001;
      background: none;
      border: none;
      transition: color 0.3s ease;
    }

    .modal-close:hover {
      color: #e50914;
    }

    .product-details {
      display: flex;
      gap: 40px;
      align-items: flex-start;
    }

    .product-image {
      flex: 0 0 45%;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 15px rgba(255, 0, 0, 0.1);
    }

    .product-image img {
      width: 100%;
      height: auto;
      object-fit: cover;
      border-radius: 8px;
      transition: transform 0.3s ease;
    }

    .product-image img:hover {
      transform: scale(1.05);
    }

    .product-info {
      flex: 0 0 55%;
      padding: 20px;
    }

    .product-info h2 {
      font-size: 2.2em;
      color: #ffffff;
      margin-bottom: 20px;
      border-bottom: 2px solid #e50914;
      padding-bottom: 10px;
    }

    .product-info p {
      font-size: 1.1em;
      line-height: 1.6;
      margin-bottom: 15px;
      color: #cccccc;
    }

    .product-info .price-tag {
      font-size: 1.8em;
      color: #e50914;
      font-weight: bold;
      margin: 20px 0;
    }

    .product-info .quantity-tag {
      background-color: rgba(229, 9, 20, 0.1);
      padding: 10px 15px;
      border-radius: 6px;
      display: inline-block;
      margin: 10px 0;
    }

    #modalAddToCart {
      background-color: #e50914;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 6px;
      font-size: 1.1em;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-top: 20px;
      width: auto;
    }

    #modalAddToCart:hover {
      background-color: #ff0f1a;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <!-- HEADER -->
    <header>
      <div class="movieVibeLogo">
        <a id="logo" href="#home"><img src="logo.png" alt="MovieVibe Logo" id="logo1"></a>
      </div>      
      <nav class="main-nav">                
        <a href="#home" class="active">Accueil</a>
        <a href="#boutique">Boutique</a>
        <a href="#reclamations">Réclamation</a>
      </nav>
      <nav class="sub-nav">
        <a href="#" id="notification-icon" class="notification-menu">
          <i class="fas fa-bell"></i>
          <span id="notification-count" class="notification-badge">0</span>
        </a>
        <a href="#" id="cart-icon" class="cart-menu">
          <i class="fas fa-shopping-cart"></i>
          <span id="cart-count">0</span>
        </a>
        <a href="../View/index.php" class="account-menu">
          <i class="fas fa-user-circle"></i>
          <span>Compte</span>
          <i class="fas fa-caret-down"></i>
        </a>
      </nav>      
    </header>

    <!-- Notification Modal -->
    <div id="notification-modal" class="notification-modal hidden">
      <div class="notification-header">
        <h2>Historique des Commandes</h2>
        <button id="close-notification">&times;</button>
      </div>
      <div id="notification-items" class="notification-items">
        <?php
        include_once '../Controller/CommandeController.php';
        $commandeController = new CommandeController($pdo);
        $commandes = $commandeController->getAllCommandes();
        
        if (empty($commandes)) {
            echo "<p>Aucune commande dans l'historique.</p>";
        } else {
            foreach ($commandes as $commande) {
                echo "<div class='notification-item'>";
                echo "<p><strong>Commande #" . htmlspecialchars($commande['id']) . "</strong></p>";
                echo "<p>Produit: " . htmlspecialchars($commande['id_produit']) . "</p>";
                echo "<p>Client: " . htmlspecialchars($commande['nom_client']) . "</p>";
                echo "<p>Date: " . htmlspecialchars($commande['date_commande']) . "</p>";
                echo "</div>";
            }
        }
        ?>
      </div>
    </div>

    <!-- Cart Modal -->
    <div id="cart-modal" class="cart-modal hidden">
      <div class="cart-header">
        <h2>Votre Panier</h2>
        <button id="close-cart">&times;</button>
      </div>
      <div id="cart-items" class="cart-items">
        <p>Votre panier est vide.</p>
      </div>
      <div class="cart-footer">
        <button id="checkout-button" class="btn btn-primary">Confirmer la commande</button>
      </div>
    </div>

    <!-- MAIN CONTAINER -->
    <section class="main-container" id="boutique">
      <h1>Boutique</h1>
      <p>Découvrez nos articles et ajoutez-les à votre panier.</p>

      <div class="product-grid">
        <?php
        include '../Controller/ProduitController.php';
        $controller = new ProduitController($pdo);
        $produits = $controller->getAllProduits();

        foreach ($produits as $produit): ?>
          <div class="product-item">
            <img src="../uploads/<?= htmlspecialchars($produit->getImage()) ?>" alt="<?= htmlspecialchars($produit->getNom()) ?>" style="width: 150px; height: 150px; object-fit: cover;">
            <h2><?= htmlspecialchars($produit->getNom()) ?></h2>
            <p><?= htmlspecialchars($produit->getDescription()) ?></p>
            <p><strong>Prix:</strong> <?= htmlspecialchars($produit->getPrix()) ?> €</p>
            <button class="add-to-cart" 
                    data-product-id="<?= $produit->getId() ?>" 
                    data-product-name="<?= htmlspecialchars($produit->getNom()) ?>" 
                    data-product-price="<?= htmlspecialchars($produit->getPrix()) ?>">
              Ajouter au panier
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <div id="imageModal" class="modal">
      <span class="modal-close">&times;</span>
      <div class="modal-content">
        <div class="product-details">
          <div class="product-image">
            <img id="modalImage" src="" alt="Product Image">
          </div>
          <div class="product-info">
            <h2 id="modalProductName"></h2>
            <div class="price-tag">
              <span id="modalProductPrice"></span> €
            </div>
            <div class="description-section">
              <p><strong>Description:</strong></p>
              <p id="modalProductDescription"></p>
            </div>
            <div class="quantity-tag">
              <strong>Stock disponible: </strong><span id="modalProductQuantity"></span> unités
            </div>
            <button class="add-to-cart" id="modalAddToCart">
              <i class="fas fa-shopping-cart"></i> Ajouter au panier
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- LINKS -->
    <section class="link">
      <div class="logos">
        <a href="#"><i class="fab fa-facebook-square fa-2x logo"></i></a>
        <a href="#"><i class="fab fa-instagram fa-2x logo"></i></a>
        <a href="#"><i class="fab fa-twitter fa-2x logo"></i></a>
        <a href="#"><i class="fab fa-youtube fa-2x logo"></i></a>
      </div>
      <div class="sub-links">
        <ul>
          <li><a href="#">Audio et sous-titres</a></li>
          <li><a href="#">Description audio</a></li>
          <li><a href="#">Centre d'aide</a></li>
          <li><a href="#">Cartes cadeaux</a></li>
          <li><a href="#">Relations investisseurs</a></li>
          <li><a href="#">Recrutement</a></li>
          <li><a href="#">Conditions d'utilisation</a></li>
          <li><a href="#">Confidentialité</a></li>
          <li><a href="#">Mentions légales</a></li>
          <li><a href="#">Préférences de cookies</a></li>
          <li><a href="#">Informations de l'entreprise</a></li>
          <li><a href="#">Nous contacter</a></li>
        </ul>
      </div>
    </section>

    <!-- FOOTER -->
    <footer>
      <p>&copy; 2025 MovieVibe. Tous droits réservés.</p>
    </footer>
  </div>
  <script>
    document.querySelectorAll('.product-item img').forEach(img => {
      img.addEventListener('click', function() {
        const productId = this.closest('.product-item').querySelector('.add-to-cart').dataset.productId;
        fetch(`showproduct.php?id=${productId}`)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const modal = document.getElementById('imageModal');
              const modalImg = document.getElementById('modalImage');
              const product = data.produit;
              
              modalImg.src = `../uploads/${product.image}`;
              document.getElementById('modalProductName').textContent = product.nom;
              document.getElementById('modalProductDescription').textContent = product.description;
              document.getElementById('modalProductPrice').textContent = product.prix;
              document.getElementById('modalProductQuantity').textContent = product.quantite;
              
              const modalAddToCart = document.getElementById('modalAddToCart');
              modalAddToCart.dataset.productId = product.id;
              modalAddToCart.dataset.productName = product.nom;
              modalAddToCart.dataset.productPrice = product.prix;
              
              modal.style.display = 'block';
            }
          })
          .catch(error => console.error('Erreur:', error));
      });
    });

    document.querySelector('.modal-close').addEventListener('click', function() {
      document.getElementById('imageModal').style.display = 'none';
    });

    // Fermer la modal si on clique en dehors
    window.addEventListener('click', function(event) {
      const modal = document.getElementById('imageModal');
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    });
  </script>
</body>
</html>