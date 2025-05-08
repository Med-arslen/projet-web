
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>
    <link rel="stylesheet" href="../projet/css/view_reservation.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
<!-- Bootstrap 5.3 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../projet/css/his.css">
</head>
<body>
<section>
    <div class="menu-bar">
        <div class="netflixLogo">
            <a id="logo" href="#home">
                <img src="../projet/assets/imgs/logo.png" alt="Logo Image" id="logo1">
            </a>
        </div>    

        <ul>
            <li><a href="../projet/page.html">Home</a></li>
            <li><a href="#">Catalog</a></li>
            <li>
                <a href="../projet/event.php">Events <i class="fas fa-caret-down"></i></a>
                <div class="dropdown-menu">
                    <ul>
                        <li><a href="./historique.php">History</a></li>
                    </ul>
                </div>
            </li>
            <li><a href="#">Purchases</a></li>
            <li><a href="#">Support</a></li>
        </ul>

        <nav class="sub-nav">
          <a href="#"><i class="fas fa-search sub-nav-logo"></i></a>
          <a href="#"><i class="fas fa-bell sub-nav-logo"></i></a>
          <a href="#"><i class="fas fa-user sub-nav-logo"></i></a> <!-- Account icon here -->
      </nav>
    </div>
</section>

<?php
require_once '../../config/database.php';

if (isset($_GET['id'])) {
    $id_reserv = $_GET['id'];

    $conn = config::getConnexion();

    // Get full reservation information + film title and photo
    $sql = "SELECT reservation.id_reserv, user.name AS user_name, user.email, event.name_event, 
                   reservation.type, reservation.date_reserv, reservation.nb_people, 
                   reservation.price, reservation.state,
                   film.title AS film_title, film.photo_path
            FROM reservation
            INNER JOIN user ON reservation.user_id = user.id_user
            INNER JOIN event ON reservation.event_id = event.id_event
            INNER JOIN film ON event.id_film = film.id_film
            WHERE reservation.id_reserv = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_reserv]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
?>
    <div class="container">
        <h2>Reservation Details</h2>
        <div class="reservation-details">
        <h3>Film Details</h3>
            <p><strong>Film Title:</strong> <?= htmlspecialchars($row['film_title']) ?></p>
            <div class="film-image">
                <img src="<?= htmlspecialchars($row['photo_path']) ?>" alt="Film Image" style="max-width:300px;">
            </div>
            <p><strong>Name:</strong> <?= htmlspecialchars($row['user_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
            <p><strong>Event Name:</strong> <?= htmlspecialchars($row['name_event']) ?></p>
            <p><strong>Type:</strong> <?= htmlspecialchars($row['type']) ?></p>
            <p><strong>Date Reserved:</strong> <?= htmlspecialchars($row['date_reserv']) ?></p>
            <p><strong>Number of People:</strong> <?= htmlspecialchars($row['nb_people']) ?></p>
            <p><strong>Price:</strong> <?= htmlspecialchars($row['price']) ?> DT</p>
            <p><strong>Status:</strong> 
            <?php
                if ($row['state'] == 'canceled') {
                    echo '<span class="status status-unpaid">Canceled</span>';
                } elseif ($row['state'] == 'confirmed') {
                    echo '<span class="status status-paid">Confirmed</span>';
                } elseif ($row['state'] == 'pending') {
                    echo '<span class="status status-pending">Pending</span>';
                } else {
                    echo '<span class="status status-unknown">Unknown</span>';
                }
            ?>
            </p>

            <hr>

           
        </div>
    </div>
<?php
    } else {
        echo "<p>No reservation found.</p>";
    }
} else {
    echo "<p>No reservation selected.</p>";
}
?>
