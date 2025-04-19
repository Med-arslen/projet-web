<?php
require('../vendor/autoload.php');
include_once '../config.php';
include_once '../Controller/CommandeController.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Récupérer les commandes
$commandeController = new CommandeController($pdo);
$commandes = $commandeController->getAllCommandes();

// Regrouper les commandes par client et adresse
$groupedOrders = [];
foreach ($commandes as $commande) {
    $key = $commande['nom_client'] . '-' . $commande['adresse'];
    if (!isset($groupedOrders[$key])) {
        $groupedOrders[$key] = [
            'client' => $commande['nom_client'],
            'adresse' => $commande['adresse'],
            'commandes' => []
        ];
    }
    $groupedOrders[$key]['commandes'][] = [
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
            color: #e50914;
        }
        .client-group {
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .client-info {
            background-color: #f8f8f8;
            padding: 10px;
            margin-bottom: 10px;
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
        <h1>Rapport des Commandes Groupées</h1>
        <p>Date de génération : ' . date('d/m/Y H:i') . '</p>
    </div>';

foreach ($groupedOrders as $group) {
    $html .= '
    <div class="client-group">
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