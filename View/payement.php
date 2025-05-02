<?php
require_once '../vendor/autoload.php';
require_once '../config.php';
require_once '../Controller/CommandeController.php';

session_start();

// Vérifier si le total existe dans la session
if (!isset($_SESSION['total'])) {
    header('Location: panier.php');
    exit;
}

// Configurer la clé API Stripe
\Stripe\Stripe::setApiKey('sk_test_51RHl9bFR5WWKGtO9XG6pNMQdrn5rgWaYwtJbLysl8bxz5sD9baMKPVNG45pAw6jDgGNMVzGGYjaPJgrIpXwiSrVZ00g9PUFlf8');

// Vérifier si on a un payment_intent_id (retour après paiement)
if (isset($_GET['payment_intent'])) {
    try {
        $payment_intent = \Stripe\PaymentIntent::retrieve($_GET['payment_intent']);
        if ($payment_intent->status === 'succeeded') {
            // Utiliser la connexion PDO déjà établie dans config.php
            global $pdo;
                
            // Créer la commande dans la base de données
            $commandeController = new CommandeController($pdo);
            
            // Décode le panier pour obtenir les produits
            $cartData = json_decode($_SESSION['cart'], true);
            
            // Créer une commande pour chaque produit dans le panier
            foreach ($cartData as $item) {
                $commandeController->createCommande(
                    $item['id'],
                    $_SESSION['nom_client'] ?? '',
                    $_SESSION['adresse'] ?? '',
                    $item['quantity']
                );
            }
            
            // Vider le panier et les données de session
            unset($_SESSION['cart']);
            unset($_SESSION['total']);
            unset($_SESSION['nom_client']);
            unset($_SESSION['adresse']);
            
            // Afficher un message de succès et rediriger
            echo "<script>
                alert('Paiement réussi ! Votre commande a été confirmée.');
                localStorage.removeItem('cart');
                window.location.href = 'page.php';
            </script>";
            exit;
        }
    } catch(Exception $e) {
        $error = $e->getMessage();
    }
}

try {
    // Créer l'intention de paiement avec le montant du panier
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => (int)($_SESSION['total'] * 100), // Conversion en centimes
        'currency' => 'eur',
    ]);
    $clientSecret = $paymentIntent->client_secret;
} catch(Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .payment-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        #card-element {
            margin-bottom: 24px;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background: white;
        }
        #card-errors {
            color: #fa755a;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }
        #submit {
            background:rgb(141, 31, 55);
            color: #ffffff;
            border-radius: 4px;
            border: 0;
            padding: 12px 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: block;
            width: 100%;
            transition: all 0.2s ease;
        }
        #submit:hover {
            filter: contrast(115%);
        }
        #submit:disabled {
            opacity: 0.5;
            cursor: default;
        }
        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-top: 3px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <main class="main-content">
            <section class="payment-section">
                <div class="content-header">
                    <h1>Paiement sécurisé</h1>
                    <p class="subtitle">Montant à payer : <?php echo number_format($_SESSION['total'], 2); ?> €</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form id="payment-form" class="payment-form">
                    <div id="card-element">
                        <!-- Elements Stripe sera injecté ici -->
                    </div>
                    <div id="card-errors" role="alert"></div>
                    <button type="submit" id="submit">
                        <div class="spinner" id="spinner"></div>
                        <span id="button-text">Payer maintenant</span>
                    </button>
                </form>
            </section>
        </main>
    </div>

    <script>
        const stripe = Stripe('pk_test_51RHl9bFR5WWKGtO9FfhqMdx9tisZRhYjPpDEaysHba0YWyOvAZIbwZKOpT2dUZMR0kii31s4GzQEsmFSAcywE4uH00inBdw3HO');
        const elements = stripe.elements();
        const card = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });

        card.mount('#card-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit');
        const spinner = document.getElementById('spinner');
        const buttonText = document.getElementById('button-text');

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            submitButton.disabled = true;
            spinner.style.display = 'inline-block';
            buttonText.style.display = 'none';

            try {
                const {paymentIntent, error} = await stripe.confirmCardPayment('<?php echo $clientSecret; ?>', {
                    payment_method: {
                        card: card,
                        billing_details: {
                            name: '<?php echo htmlspecialchars($_SESSION['nom_client'] ?? ''); ?>'
                        }
                    }
                });

                if (error) {
                    const errorElement = document.getElementById('card-errors');
                    errorElement.textContent = error.message;
                    submitButton.disabled = false;
                    spinner.style.display = 'none';
                    buttonText.style.display = 'block';
                } else if (paymentIntent.status === 'succeeded') {
                    // Rediriger vers la même page avec l'ID du paiement
                    window.location.href = 'payement.php?payment_intent=' + paymentIntent.id;
                }
            } catch (e) {
                console.error('Erreur:', e);
                const errorElement = document.getElementById('card-errors');
                errorElement.textContent = "Une erreur est survenue lors du traitement du paiement.";
                submitButton.disabled = false;
                spinner.style.display = 'none';
                buttonText.style.display = 'block';
            }
        });
    </script>
</body>
</html>