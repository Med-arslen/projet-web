<?php
require_once __DIR__ . '/../../controller/ReclamationController.php';

// Créer une instance du controller
$controller = new ReclamationController();

// Générer le PDF
$pdfContent = $controller->generatePDF();

// Envoyer les en-têtes HTTP appropriés
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="liste_reclamations_' . date('Y-m-d') . '.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');

// Afficher le PDF
echo $pdfContent;
exit;
?>
