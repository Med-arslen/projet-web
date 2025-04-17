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
</body>
</html>