<?php
include '../config.php';
include '../Controller/ProduitController.php';
$controller = new ProduitController($pdo);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - MovieVibe</title>
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <script src="https://use.fontawesome.com/releases/v5.1.0/js/all.js"></script>
</head>
<body>
    <div class="wrapper">
        <!-- HEADER -->
        <header>
            <div class="movieVibeLogo">
                <a href="page.php"><img src="logo.png" alt="MovieVibe Logo" id="logo1"></a>
            </div>
            <nav class="main-nav">
                <a href="page.php">Retour à la boutique</a>
            </nav>
        </header>

        <!-- MAIN CONTAINER -->
        <main class="main-content">
            <section class="cart-section">
                <h1>Mon Panier</h1>
                <div id="cart-items" class="cart-list">
                    <!-- Le panier sera chargé ici dynamiquement -->
                </div>
                <div class="cart-summary">
                    <div class="total">
                        <h3>Total: <span id="cart-total">0.00</span> €</h3>
                    </div>
                    <div class="cart-actions">
                        <button id="clear-cart" class="btn btn-secondary">Vider le panier</button>
                        <button id="checkout" class="btn btn-primary">Commander</button>
                    </div>
                </div>
            </section>
        </main>

        <!-- FOOTER -->
        <footer>
            <p>&copy; 2025 MovieVibe. Tous droits réservés.</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const clearCartBtn = document.getElementById('clear-cart');
            const checkoutBtn = document.getElementById('checkout');
            
            function loadCart() {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                cartItems.innerHTML = '';
                
                if (cart.length === 0) {
                    cartItems.innerHTML = '<p class="empty-cart">Votre panier est vide</p>';
                    return;
                }

                let total = 0;
                cart.forEach((item, index) => {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    
                    const cartItem = document.createElement('div');
                    cartItem.className = 'cart-item';
                    cartItem.innerHTML = `
                        <div class="item-details">
                            <h3>${item.name}</h3>
                            <p>Prix unitaire: ${item.price.toFixed(2)} €</p>
                        </div>
                        <div class="item-quantity">
                            <button class="quantity-btn" onclick="updateQuantity(${index}, ${item.quantity - 1})">-</button>
                            <span>${item.quantity}</span>
                            <button class="quantity-btn" onclick="updateQuantity(${index}, ${item.quantity + 1})">+</button>
                        </div>
                        <div class="item-total">
                            <p>${itemTotal.toFixed(2)} €</p>
                            <button class="remove-item" onclick="removeItem(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    cartItems.appendChild(cartItem);
                });
                
                cartTotal.textContent = total.toFixed(2);
            }

            window.updateQuantity = function(index, newQuantity) {
                if (newQuantity < 1) return;
                
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart[index].quantity = newQuantity;
                localStorage.setItem('cart', JSON.stringify(cart));
                loadCart();
            };

            window.removeItem = function(index) {
                const cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                loadCart();
            };

            clearCartBtn.addEventListener('click', function() {
                if (confirm('Voulez-vous vraiment vider votre panier ?')) {
                    localStorage.removeItem('cart');
                    loadCart();
                }
            });

            checkoutBtn.addEventListener('click', function() {
                window.location.href = 'addCommande.php';
            });

            // Charger le panier au chargement de la page
            loadCart();
        });
    </script>

    <style>
        .cart-section {
            padding: 2rem;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            margin: 2rem auto;
            max-width: 1200px;
        }

        .cart-list {
            margin: 2rem 0;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        .item-details {
            flex: 2;
        }

        .item-quantity {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .quantity-btn {
            background: #e50914;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 4px;
            cursor: pointer;
        }

        .item-total {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .remove-item {
            background: none;
            border: none;
            color: #e50914;
            cursor: pointer;
        }

        .empty-cart {
            text-align: center;
            color: #666;
            padding: 2rem;
        }

        .cart-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 4px;
        }

        .cart-actions {
            display: flex;
            gap: 1rem;
        }
    </style>
</body>
</html>