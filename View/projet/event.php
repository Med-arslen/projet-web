<!DOCTYPE html>
<?php
require_once "../../config/database.php";

require_once '../../controllers/eventcontroller.php';
$conn = config::getConnexion();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
<!-- Bootstrap 5.3 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

<!--<link rel="stylesheet" href="../../public/css/style.css">-->
    <link rel="stylesheet" href="../projet/css/event.css">
 
    <title>Document</title>
</head>
<b style="background: url('../backoffice/cinema.jpg') no-repeat center center fixed; background-size: cover;">
  <header>
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
                        <li><a href="./historique.html">History</a></li>
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
</header>
<section id="fond2">
    <div id="hero-carousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
          </div>
        <div class="carousel-inner">
          <div class="carousel-item active c-item">
            <img src="../../public/img/pexels-quark-studio-2507025_720x.webp" class="d-block w-100 c-img" alt="slide 1">
            
              <div class="carousel-caption top-0 mt-4 d-none d-md-block text-center">
                <p class="mt-5 fs-3 text-uppercase text-warning" style="font-family: 'Arial', sans-serif;">Watch Movies Under the Stars</p>
                <h1 class="display-5 fw-bolder text-capitalize text-light" style="font-family: 'Roboto', sans-serif;">
                  Bring a blanket, grab some snacks, and enjoy a magical outdoor cinema experience.
                </h1>
                
              </div>
              
          </div>
          <div class="carousel-item c-item">
            <img src="../../public/img/popcorn.jpg" class="d-block w-100 c-img" alt="slide 2">
            <div class="carousel-caption top-0 mt-4 d-none d-md-block text-center">
                <p class="mt-5 fs-3 text-uppercase text-warning" style="font-family: 'Arial', sans-serif;">Snacks, Lights, Action!</p>
                <h1 class="display-5 fw-bolder text-capitalize text-light" style="font-family: 'Roboto', sans-serif;">
                    Grab your favorite treats and enjoy the perfect movie night atmosphere.
                </h1>
               
              </div>
              
          </div>
          <div class="carousel-item c-item">
            <img src="../../public/img/peoplewatching.webp" class="d-block w-100 c-img" alt="slide 3">
            <div class="carousel-caption top-0 mt-4 d-none d-md-block text-center">
                <p class="mt-5 fs-3 text-uppercase text-warning" style="font-family: 'Arial', sans-serif; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);">
                  Capture Moments, Create Memories
                </p>
                <h1 class="display-4 fw-bolder text-capitalize text-light" style="font-family: 'Roboto', sans-serif; line-height: 1.4; letter-spacing: 1px; text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.7);">
                  Your next favorite memory starts here.
                  Check Out Our Exciting Events!
                </h1>
              </div>
              
              
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
      </div>

      <div class="events ">
        <h5>Explore Our Upcoming Events!</h5>
      </div>

      <div class="card-container">
<?php
$sql = "SELECT event.*, film.title, film.photo_path 
        FROM `event` 
        JOIN film ON `event`.id_film = film.id_film";
$stmt = $conn->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
?>
  <div class="card">
    <div class="image">
      <img src="<?= $row['photo_path'] ?>" alt="Film Image">
    </div>

    <div class="info"><strong>Event:</strong> <?= $row['name_event'] ?></div>
    <div class="info"><strong>Description:</strong> <?= $row['description'] ?></div>
    <div class="info price"><strong>Price:</strong> <?= $row['price_event'] ?> DT</div>
    <div class="info"><strong>Date:</strong> <?= $row['date_event'] ?></div>
    <div class="info"><strong>Location:</strong> <?= $row['location'] ?></div>
    <div class="info"><strong>Number of Places:</strong> <?= $row['total_places'] ?></div>
    <div class="info"><strong>Film Title:</strong> <?= $row['title'] ?></div>

    <div>
      <button onclick='showReservationForm(<?= json_encode($row) ?>)'>Reserve NOW</button>
    </div>
  </div>
<?php } ?>
</div>

<!-- Reservation Form (Initially Hidden) -->
<?php

require_once '../../controllers/eventcontroller.php';


if (isset($_POST["event_id"])) {
  $eventId = $_POST['event_id'];
    $eventController = new EventController();
    $result=$eventController->placeAvailability($eventId);
}
  
  
?>
<div id="reservationForm" class="reservation-form" style="display: none;">
  <div class="form-container">
    <span class="close-btn" onclick="closeReservationForm()">&times;</span>
    <h2>Reserve Your Spot</h2>
    
    <form action="submit_reservation.php" method="POST" id="reservationFormContent">
      <input type="hidden" id="eventId" name="event_id">

      <label for="name">Name:</label>
      <input type="text" id="name" name="name" disabled value="Mira">

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" disabled value="mirabk@gmail.com">

      <label for="phone">Phone:</label>
      <input type="text" id="phone" name="phone" disabled value="123654789">

      <label for="type_reservation">Type of Reservation:</label>
      <select id="type_reservation" name="type_reservation" onchange="updatePrice()">
        <option value="vip">VIP</option>
        <option value="standard">Standard</option>
        <option value="premium">Premium</option>
        <option value="special_event">Special Event</option>
      </select>

      <label for="people">Number of Tickets:</label>
      <input type="number" id="people" name="num_people" value="1" min="1" onchange="updatePrice()" required>

      <p id="errorMessage" style="color:red; display:none;">No available spots for this reservation.</p>
      
      <p>  <span id="notavailableplace"></span></p>
      <p>Date: <span id="eventDate"></span></p>
      <p>Price: <span id="price"></span> DT</p>

      <button type="submit" id="submit_button">Submit Reservation</button>
    </form>
</div>
</div>

    <!-- Success Message -->
    <div id="successMessage" style="display:none; text-align:center; font-size:18px; color:#4CAF50;">
        <p>Success! Your reservation has been successfully submitted.</p>
    </div>
</div>

     

        <div class="content-box">
            <div class="projection-grid">
              <!-- Introduction -->
              <div class="grid-item intro">
                <h2>Key Figures of Our Projections</h2>
                <p class="lead">
                  Our commitment to innovation, performance, and outdoor cinematic experiences drives us to excel in organizing cultural events.
                </p>
              </div>
        
              <!-- First Stat Block -->
              <div class="grid-item stat-block">
                <p class="stat-number">500,000 Viewers</p>
                <p>
                  Our viewers have enjoyed unforgettable moments through our unique and immersive film screenings.
                </p>
                <div class="progress-bar-container">
                  <div class="progress-bar" style="width: 80%"></div>
                </div>
              </div>
        
              <!-- Second Stat Block -->
              <div class="grid-item stat-block">
                <p class="stat-number">+300,000</p>
                <p>
                  We're proud to have welcomed over 50,000 happy attendees at our open-air film screenings.
                </p>
                <div class="progress-bar-container">
                  <div class="progress-bar" style="width: 45%"></div>
                </div>
              </div>
        
              <!-- Donut Chart Block -->
              <div class="grid-item chart-block">
                <canvas class="donut-chart"></canvas>
                <p class="chart-percentage">75%</p>
                <p class="chart-description">
                  80% of our audience returns every summer, showing their love for our outdoor screenings.
                </p>
              </div>
            </div>
          </div>

</section>

 
  
  
     
  
</body>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="./js/event.js"></script>





  

  











</html>