<?php
require '../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configuration de Dompdf
$options = new Options();
$options->set('defaultFont', 'Poppins');
$dompdf = new Dompdf($options);

// Commence à capturer le contenu HTML
ob_start();
?>

<!-- Contenu HTML à convertir -->
<h2 style="text-align:center; color:#E50914;">Historique des Réservations</h2>
<table style="width:100%; border-collapse: collapse;" border="1" cellpadding="8">
  <thead>
    <tr style="background-color: #f0f0f0;">
      <th>User ID</th>
      <th>Nom</th>
      <th>Email</th>
      <th>Événement</th>
      <th>Type</th>
      <th>Date Réservation</th>
      <th>Nombre Tickets</th>
      <th>Prix Total (DT)</th>
      <th>État</th>
    </tr>
  </thead>
  <tbody>
    <?php
    require_once '../../config/database.php';
    require_once '../../controllers/Reservcontroller.php';
    $reservationController = new ReservController();
    $reservations = $reservationController->getReservations();

    foreach ($reservations as $res) {
        echo "<tr>
                <td>{$res['id_user']}</td>
                <td>{$res['name']}</td>
                <td>{$res['email']}</td>
                <td>{$res['name_event']}</td>
                <td>{$res['type']}</td>
                <td>{$res['date_reserv']}</td>
                <td>{$res['nb_people']}</td>
                <td>{$res['price']} DT</td>
                <td>{$res['state']}</td>
              </tr>";
    }
    ?>
  </tbody>
</table>

<?php
$html = ob_get_clean();

// Charge le HTML dans Dompdf
$dompdf->loadHtml($html);

// Options de rendu
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Télécharge le PDF
$dompdf->stream("reservations.pdf", ["Attachment" => true]);
exit;
?>
