<?php
require('../vendor/autoload.php');
include_once '../config.php';
include_once '../Controller/CommandeController.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

// Définir les régions de Tunisie
$regions_nord = ['Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Bizerte', 'Nabeul', 'Zaghouan', 'Béja', 'Jendouba', 'Le Kef', 'Siliana'];
$regions_sud = ['Sousse', 'Monastir', 'Mahdia', 'Kairouan', 'Kasserine', 'Sidi Bouzid', 'Gabès', 'Médenine', 'Tataouine', 'Tozeur', 'Kébili', 'Sfax', 'Gafsa'];

// Récupérer les commandes
$commandeController = new CommandeController($pdo);
$commandes = $commandeController->getAllCommandes();

// Fonction pour générer un QR code en SVG
function generateQRCode($data) {
    $renderer = new ImageRenderer(
        new RendererStyle(400),
        new SvgImageBackEnd()
    );
    $writer = new Writer($renderer);
    return $writer->writeString($data);
}

// Regrouper les commandes par client, adresse et région
$groupedOrders = [
    'nord' => [],
    'sud' => []
];

foreach ($commandes as $commande) {
    $key = $commande['nom_client'] . '-' . $commande['adresse'];
    $region = 'sud'; // Par défaut

    // Déterminer la région
    foreach ($regions_nord as $ville) {
        if (stripos($commande['adresse'], $ville) !== false) {
            $region = 'nord';
            break;
        }
    }

    if (!isset($groupedOrders[$region][$key])) {
        $groupedOrders[$region][$key] = [
            'client' => $commande['nom_client'],
            'adresse' => $commande['adresse'],
            'commandes' => []
        ];
    }
    $groupedOrders[$region][$key]['commandes'][] = [
        'id' => $commande['id'],
        'produit' => $commande['id_produit'],
        'quantite' => $commande['quantite'],
        'date' => $commande['date_commande']
    ];
}

// Créer le contenu HTML du PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport des Commandes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .region-title {
            background-color: #e50914;
            color: white;
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .client-group {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            position: relative;
        }
        .client-info {
            background-color: #f8f8f8;
            padding: 10px;
            margin-bottom: 10px;
        }
        .qr-code {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 100px;
            height: 100px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #e50914;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="data:image/png;base64,' . base64_encode(file_get_contents('../View/logo.png')) . '" class="logo">
        <h1>Rapport des Commandes Groupées</h1>
        <p>Date de génération : ' . date('d/m/Y H:i') . '</p>
    </div>';

// Générer le contenu pour chaque région
foreach (['nord', 'sud'] as $region) {
    $html .= '<div class="region-title"><h2>' . ucfirst($region) . ' de la Tunisie</h2></div>';
    
    foreach ($groupedOrders[$region] as $key => $group) {
        // Générer le QR Code pour ce groupe de commandes en SVG
        $qrData = json_encode([
            'client' => $group['client'],
            'adresse' => $group['adresse'],
            'commandes' => $group['commandes']
        ]);
        $qrCodeSvg = generateQRCode($qrData);
        
        $html .= '
        <div class="client-group">
            <div class="qr-code">' . $qrCodeSvg . '</div>
            <div class="client-info">
                <h2>Client: ' . htmlspecialchars($group['client']) . '</h2>
                <p>Adresse: ' . htmlspecialchars($group['adresse']) . '</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID Commande</th>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($group['commandes'] as $commande) {
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars($commande['id']) . '</td>
                        <td>' . htmlspecialchars($commande['produit']) . '</td>
                        <td>' . htmlspecialchars($commande['quantite']) . '</td>
                        <td>' . htmlspecialchars($commande['date']) . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
        </div>';
    }
}

$html .= '
</body>
</html>';

// Configurer DomPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Envoyer le PDF au navigateur
$dompdf->stream('rapport_commandes.pdf', array('Attachment' => true));
?>