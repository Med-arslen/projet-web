<?php
include '../config.php';
include '../Controller/CommandeController.php';
include '../Controller/ProduitController.php';
require_once '../vendor/autoload.php';

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;

$commandeController = new CommandeController($pdo);
$produitController = new ProduitController($pdo);

// Récupérer l'ID de la commande depuis l'URL
$id_commande = isset($_GET['id']) ? $_GET['id'] : null;

// Si pas d'ID spécifié, rediriger vers la dernière commande
if (!$id_commande) {
    $commandes = $commandeController->getAllCommandes();
    $derniereCommande = end($commandes);
    if (!$derniereCommande) {
        header('Location: page.php');
        exit;
    }
    $derniereCommande = $commandeController->getCommandeById($derniereCommande['id']);
} else {
    // Sinon, récupérer la commande spécifique
    $derniereCommande = $commandeController->getCommandeById($id_commande);
}

if (!$derniereCommande) {
    header('Location: page.php');
    exit;
}

$totalGeneral = 0;
$produitsData = [];
foreach ($derniereCommande->getProduits() as $produitCommande) {
    $produit = $produitController->getProduitById($produitCommande['id_produit']);
    $total = $produit->getPrix() * $produitCommande['quantite'];
    $totalGeneral += $total;
    $produitsData[] = [
        'nom' => $produit->getNom(),
        'quantite' => $produitCommande['quantite'],
        'prix' => $produit->getPrix(),
        'total' => $total
    ];
}

// Préparer les données pour le QR code après le calcul du total
$qrData = [
    'numero_commande' => $derniereCommande->getIdCommande(),
    'date' => $derniereCommande->getDateCommande(),
    'client' => [
        'nom' => $derniereCommande->getNomClient(),
        'adresse' => $derniereCommande->getAdresse()
    ],
    'produits' => $produitsData,
    'total' => $totalGeneral
];

// Options du QR code
$options = new QROptions([
    'quietzoneSize' => 2,
    'outputType' => QRCode::OUTPUT_MARKUP_SVG,
    'eccLevel' => QRCode::ECC_L,
]);

// Création du QR code
$qrcode = new QRCode($options);
$qrCodeImage = $qrcode->render(json_encode($qrData));

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture - MovieVibe</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
</head>
<body>
    <div class="wrapper">
        <main class="main-content">
            <div class="facture-container">
                <div class="facture-header">
                    <div class="logo">
                        <img src="logo.png" alt="MovieVibe Logo">
                        <h1>MovieVibe</h1>
                    </div>
                    <div class="facture-info">
                        <h2>Facture</h2>
                        <p>Date: <?= htmlspecialchars($derniereCommande->getDateCommande()) ?></p>
                        <p>N° Commande: <?= htmlspecialchars($derniereCommande->getIdCommande()) ?></p>
                    </div>
                </div>

                <div class="client-info">
                    <h3>Informations Client</h3>
                    <p><strong>Nom:</strong> <?= htmlspecialchars($derniereCommande->getNomClient()) ?></p>
                    <p><strong>Adresse:</strong> <?= htmlspecialchars($derniereCommande->getAdresse()) ?></p>
                </div>

                <div class="commande-details">
                    <h3>Détails de la Commande</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($derniereCommande->getProduits() as $produitCommande) {
                                $produit = $produitController->getProduitById($produitCommande['id_produit']);
                                $total = $produit->getPrix() * $produitCommande['quantite'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($produit->getNom()) ?></td>
                                <td><?= htmlspecialchars($produitCommande['quantite']) ?></td>
                                <td><?= htmlspecialchars($produit->getPrix()) ?> €</td>
                                <td><?= number_format($total, 2) ?> €</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Total TTC</strong></td>
                                <td><strong><?= number_format($totalGeneral, 2) ?> €</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Ajouter le QR code avant les boutons d'action -->
                <div class="qr-code-container">
                    <h3>Scanner pour plus d'informations</h3>
                    <img src="<?= $qrCodeImage ?>" alt="QR Code de la facture">
                </div>

                <div class="facture-actions">
                    <a href="page.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                </div>
            </div>
        </main>
    </div>

    <style>
        .facture-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 800px;
        }

        .facture-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo img {
            width: 60px;
            height: auto;
        }

        .facture-info {
            text-align: right;
        }

        .client-info, .commande-details {
            margin-bottom: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(255, 255, 255, 0.05);
        }

        tfoot td {
            border-top: 2px solid rgba(255, 255, 255, 0.1);
        }

        .facture-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }

        .qr-code-container {
            text-align: center;
            margin: 2rem 0;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .qr-code-container img {
            max-width: 200px;
            height: auto;
        }

        @media print {
            body {
                background: white;
                color: black;
            }

            .facture-container {
                background: white;
                box-shadow: none;
            }

            .facture-actions {
                display: none;
            }

            th {
                background: #f5f5f5;
            }

            th, td {
                border-bottom: 1px solid #ddd;
            }

            tfoot td {
                border-top: 2px solid #ddd;
            }
        }
    </style>
</body>
</html>