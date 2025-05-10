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

// Récupérer les paramètres de l'URL
$id_commande = isset($_GET['id']) ? $_GET['id'] : null;
$client_name = isset($_GET['client']) ? $_GET['client'] : null;

// Variables pour stocker les commandes
$commandes = [];
$derniereCommande = null;

if ($client_name) {
    // Récupérer toutes les commandes du client
    $all_commandes = $commandeController->getAllCommandes();
    $commandes = array_filter($all_commandes, function($commande) use ($client_name) {
        return strcasecmp($commande['nom_client'], $client_name) === 0;
    });
    
    if (empty($commandes)) {
        $_SESSION['error_message'] = "Aucune facture trouvée pour le client : " . htmlspecialchars($client_name);
        header('Location: index.php?tab=commandes');
        exit;
    }
    
    // Trier les commandes par date (la plus récente en premier)
    usort($commandes, function($a, $b) {
        return strtotime($b['date_commande']) - strtotime($a['date_commande']);
    });
    
    $derniereCommande = $commandeController->getCommandeById($commandes[0]['id']);
} else if ($id_commande) {
    // Récupérer une commande spécifique
    $derniereCommande = $commandeController->getCommandeById($id_commande);
    $commandes = [$derniereCommande];
} else {
    // Si pas d'ID ni de client spécifié, rediriger vers la dernière commande
    $all_commandes = $commandeController->getAllCommandes();
    $derniere = end($all_commandes);
    if (!$derniere) {
        header('Location: page.php');
        exit;
    }
    $derniereCommande = $commandeController->getCommandeById($derniere['id']);
    $commandes = [$derniereCommande];
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
<body>    <div class="wrapper">
        <main class="main-content">
            <?php if (count($commandes) > 1): ?>
            <div class="factures-list">
                <h2>Factures de <?= htmlspecialchars($client_name) ?></h2>
                <div class="factures-grid">
                    <?php foreach ($commandes as $cmd): 
                        $cmdObj = $commandeController->getCommandeById($cmd['id']);
                        $totalCmd = 0;
                        foreach ($cmdObj->getProduits() as $p) {
                            $prod = $produitController->getProduitById($p['id_produit']);
                            $totalCmd += $prod->getPrix() * $p['quantite'];
                        }
                    ?>
                        <div class="facture-card" onclick="window.location.href='facture.php?id=<?= $cmd['id'] ?>'">
                            <div class="facture-card-header">
                                <h3>Facture #<?= $cmd['id'] ?></h3>
                                <p class="date"><?= $cmd['date_commande'] ?></p>
                            </div>
                            <div class="facture-card-body">
                                <p><strong>Total:</strong> <?= number_format($totalCmd, 2) ?> €</p>
                                <p><strong>Adresse:</strong> <?= htmlspecialchars($cmd['adresse']) ?></p>
                            </div>
                            <div class="facture-card-footer">
                                <button class="btn btn-primary">Voir détails</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="factures-actions">
                    <a href="index.php?tab=commandes" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            <?php else: ?>
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
                </div>                <div class="facture-actions">
                    <a href="page.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Imprimer
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div><style>
        .factures-list {
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
        }

        .factures-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .facture-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 1.5rem;
            cursor: pointer;
            transition: transform 0.3s, background-color 0.3s;
        }

        .facture-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
        }

        .facture-card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .facture-card-header h3 {
            margin: 0 0 0.5rem 0;
            color: #e50914;
        }

        .date {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin: 0;
        }

        .facture-card-body {
            padding: 1rem 0;
        }

        .facture-card-body p {
            margin: 0.5rem 0;
        }

        .facture-card-footer {
            margin-top: 1rem;
            text-align: right;
        }

        .factures-actions {
            margin-top: 2rem;
            display: flex;
            justify-content: flex-start;
        }

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