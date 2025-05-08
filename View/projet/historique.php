<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>
    <link rel="stylesheet" href="../projet/css/his.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
<!-- Bootstrap 5.3 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Color Palette */
/* Keep the navbar as is */


body {
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #ffe5e0, #fff9e5); /* Soft red to pale yellow */
  min-height: 100vh;
  background-attachment: fixed;
  background-repeat: no-repeat;
  color: #333;
}


.section-heading {
  font-size: 2.5rem;
  font-weight: 800;
  text-align: center;
  color: var(--clr-primary);
  margin: 80px auto 40px auto;
  position: relative;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-family: 'Poppins', sans-serif;
  text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.1);
}

.section-heading::after {
  content: '';
  display: block;
  width: 100px;
  height: 4px;
  margin: 15px auto 0;
  background: linear-gradient(to right, var(--clr-primary), var(--clr-primary-light));
  border-radius: 5px;
  animation: growLine 1s ease-in-out;
}

/* Optional animation for underline */
@keyframes growLine {
  0% {
    width: 0;
    opacity: 0;
  }
  100% {
    width: 100px;
    opacity: 1;
  }
}


:root {
  --clr-primary: #E50914; /* Netflix Red */
  --clr-primary-light: #F2F2F2;
  --clr-primary-dark: #B30000;
  --clr-gray100: #f9fbff;
  --clr-gray200: #f0f4f8;
  --clr-gray300: #D1D9E6;
  --clr-gray400: #B0B8D0;
  --clr-gray500: #4F546C;
  --clr-gray600: #2A324B;
  --clr-link: #2962FF;
  --clr-hover-bg: rgba(229, 9, 20, 0.8); /* Red with slight transparency */
  --clr-white: #ffffff;
  --clr-dark: #1e1e1e;
}

/* Global Reset */
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
.menu-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: black;
  padding: 10px 20px; /* Adequate padding to make navbar neat */
  height: 80px; /* Slightly taller navbar to accommodate larger logo */
}

.netflixLogo img {
  height: 70px; /* Increased logo size */
}

ul {
  list-style: none;
  display: flex;
  margin: 0;
  padding: 0;
}

ul li {
  margin-right: 25px; /* Increased spacing for better separation */
  position: relative;
}

ul li a {
  color: white;
  text-decoration: none;
  padding: 12px 20px; /* Larger padding for better visual appeal */
  font-size: 18px; /* Larger font size for titles */
  display: block;
  transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transition for hover */
}

ul li a:hover {
  background-color: red; /* Red background on hover */
  color: white; /* White color for text on hover */
}

/* Dropdown Styles */
li:hover .dropdown-menu {
  display: block; /* Show dropdown on hover */
}

.dropdown-menu {
  display: none;
  position: absolute;
  background-color: #333;
  min-width: 160px;
  box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
  z-index: 1;
}

.dropdown-menu ul {
  margin: 0;
  padding: 0;
}

.dropdown-menu li {
  padding: 8px;
}

.dropdown-menu li a {
  color: white;
  text-decoration: none;
  display: block;
  transition: background-color 0.3s ease;
}

.dropdown-menu li a:hover {
  background-color: red; /* Red background for hover in dropdown */
}

/* Account link at the corner */
.account-corner {
  margin-left: auto;
}

.account-corner a {
  color: white;
  text-decoration: none;
  padding: 12px 20px;
  font-size: 18px; /* Larger font size for Account link */
}

.account-corner a:hover {
  background-color: red; /* Red background on hover */
  color: white;
}

/* Adjusting spacing and alignment */
ul li:last-child {
  margin-right: 0;
}
.sub-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #000000; /* Adjust background if needed */
  padding: 10px 20px;
}

.sub-nav a {
  color: white;
  text-decoration: none;
  font-size: 20px; /* Adjust icon size */
  padding: 8px;
}

.sub-nav a:hover {
  background-color: red; /* Background color on hover */
  border-radius: 50%; /* Make the hover effect circular */
}

.sub-nav-logo {
  font-size: 24px; /* Adjust the icon size */
}

.netflixLogo {
  grid-area: nt;
  object-fit: cover;
  width: 100px;
  transform: translateY(+10);
}
.netflixLogo img {  
  height: 90px;     
}

#logo {
  color: #E50914;  
  margin: 0; 
  padding: 0; 
}

/* Table styles (increased size) */
table {
  width: 90%;
  max-width: 1200px;
  border-collapse: collapse;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
  background-color: var(--clr-white);
  text-align: left;
  border-radius: 10px;
  overflow: hidden;
  margin: 100px auto 0 auto; /* Top margin + center horizontally */
}

thead {
  background-color: var(--clr-primary-light);
  color: var(--clr-dark);
  text-transform: uppercase;
  font-weight: bold;

  text-transform: uppercase;
  letter-spacing: 0.05rem;
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
  letter-spacing: 1px;
}

th,
td {
  padding: 15px 20px;
}

th {
 /* Larger font size for table headers */
    font-weight: 700;
    font-size: 1.1rem;
  font-weight: 700;
  padding: 20px 25px;
  border-bottom: 3px solid var(--clr-primary);
  border-radius: 10px 10px 0 0;
  background-color: rgba(255, 255, 255, 0.05); /* subtle highlight */
}

td {
  font-size: 1rem;
  color: var(--clr-gray600);
}

a {
  text-decoration: none;
  color: var(--clr-link);
  font-weight: 600;
  transition: color 0.3s ease;
}

a:hover {
  color: var(--clr-primary-dark);
}

/* Row Styling */
tr:nth-child(even) {
  background-color: var(--clr-gray200);
}

tr:nth-child(odd) {
  background-color: var(--clr-gray300);
}

tr:hover {
  background-color: var(--clr-primary-light);
  transform: scale(1.02);
  transition: all 0.3s ease;
}

/* Status Badges */
.status {
  display: inline-block;
  padding: 5px 15px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 600;
  text-align: center;
  text-transform: capitalize;
}

.status-pending {
  background-color: rgba(255, 204, 0, 0.1);
  color: #ffcc00;
}

.status-paid {
  background-color: rgba(0, 204, 0, 0.1);
  color: #00cc00;
}

.status-unpaid {
  background-color: rgba(255, 51, 51, 0.1);
  color: #ff3333;
}

/* Amount Styling */
.amount {
  text-align: right;
  font-weight: bold;
  color: var(--clr-primary-dark);
}

/* Action Buttons */
button,
a.btn-link {
  background-color: transparent;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  padding: 8px 15px;
  transition: color 0.3s ease, transform 0.3s ease;
}

button:hover,
a.btn-link:hover {
  color: var(--clr-primary);
  transform: scale(1.1);
}

button {
  color: var(--clr-primary-dark);
}

.btn-link {
  color: var(--clr-primary-dark);
  text-decoration: none;
}

.btn-link:hover {
  color: var(--clr-primary);
  text-decoration: underline;
}

/* Smooth Transitions */
* {
  transition: all 0.3s ease;
}
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../projet/css/his.css">
</head>
<body>
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
    
   <!-- Historique Table to display reservations -->
   <h2 class="section-heading">Your Reservation History</h2>

   <table border="1">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Event Name</th>
            <th>Reservation Type</th>
            <th>Reservation Date</th>
            <th>Number of Tickets</th>
            <th>Total Price (DT)</th>
            <th>State</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        require_once '../../config/database.php';
        require_once '../../controllers/Reservcontroller.php';

        $sql = "SELECT id_reserv, user.name, email, name_event, reservation.type, date_reserv, nb_people, price, reservation.state 
                FROM user 
                INNER JOIN reservation ON user.id_user = reservation.user_id 
                INNER JOIN event ON event.id_event = reservation.event_id";
        $conn = config::getConnexion();

        $stmt = $conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <tr>
                <td><a href="view_reservation.php?id=<?= $row['id_reserv'] ?>"><?= htmlspecialchars($row["name"]) ?></a></td>
                <td><?= htmlspecialchars($row["email"]) ?></td>
                <td><?= htmlspecialchars($row["name_event"]) ?></td>
                <td><?= htmlspecialchars($row["type"]) ?></td>
                <td><?= htmlspecialchars($row["date_reserv"]) ?></td>
                <td><?= htmlspecialchars($row["nb_people"]) ?></td>
                <td class="amount"><?= htmlspecialchars($row["price"]) ?> DT</td>
                <td>
                <?php
if ($row['state'] == 'canceled') {
    echo '<p class="status status-unpaid">canceled</p>';
} elseif ($row['state'] == 'confirmed') {
    echo '<p class="status status-paid">confirmed</p>';
} elseif ($row['state'] == 'pending') {
    echo '<p class="status status-pending">Pending</p>';
} else {
    echo '<p class="status status-unknown">Unknown Status</p>';
}
?>

                </td>
                <td>
                    <form action="cancel_reservation.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row["id_reserv"] ?>">
                        <button type="submit" class="btn btn-link link-dark p-0" title="Cancel Reservation">
                            <i class="fa-solid fa-trash fs-5"></i>
                        </button>
                    </form>
                    <a href="view_reservation.php?id=<?= $row['id_reserv'] ?>" class="btn btn-link link-dark p-0" title="View Reservation">
                        <i class="fa-solid fa-eye fs-5"></i>
                    </a>
               <!--     <button
    type="button"
    class="btn btn-link link-dark p-0"
    title="Process Stripe Payment"
    onclick="showStripeForm(<?= $row['id_reserv'] ?>, <?= $row['price'] * 100 ?>)"
>
    <i class="fa-brands fa-cc-stripe fs-5"></i>
</button>
-->
<button
    type="button"
    class="btn btn-link link-dark p-0"
    title="Process Stripe Payment"
    data-reservation-id="<?= $row['id_reserv'] ?>"
    data-amount="<?= $row['price'] ?>"
>
    <i class="fa-brands fa-cc-stripe fs-5"></i>
</button>

                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
  

<script src="script.js"></script>


<script>
function showStripeForm(reservationId, amountInCents) {
    // Remove old form if it exists
    const oldForm = document.getElementById('stripeForm');
    if (oldForm) oldForm.remove();

    // Create the form dynamically
    const form = document.createElement('form');
    form.setAttribute('id', 'stripeForm');
    form.setAttribute('action', 'payment.php'); // goes to payment.php to show Stripe form
    form.setAttribute('method', 'POST');

    // Add hidden inputs
    const reservationInput = document.createElement('input');
    reservationInput.type = 'hidden';
    reservationInput.name = 'id_reservation';
    reservationInput.value = reservationId;

    const amountInput = document.createElement('input');
    amountInput.type = 'hidden';
    amountInput.name = 'amount';
    amountInput.value = amountInCents;

    form.appendChild(reservationInput);
    form.appendChild(amountInput);

    document.body.appendChild(form);
    form.submit(); // Submit to payment.php (displays Stripe button)
}
<?php

require_once  '../../config/stripe_config.php';

$publishableKey = STRIPE_PUBLISHABLE_KEY; // Get the publishable key from your config
?>
</script>
<script src="https://js.stripe.com/v3/"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Initialize Stripe with your publishable key
    const stripe = Stripe("<?= $publishableKey ?>"); // Use your publishable key here

    document.querySelectorAll('.btn-link').forEach(button => {
      button.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default action

        const reservationId = this.getAttribute('data-reservation-id');
        const amount = this.getAttribute('data-amount') ;

        fetch('pay_reservation.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ reservationId, amount }),
        })

        .then(response => response.json())
        .then(data => {
          if (data.error) {
            alert(data.error);
          } else {
            // Redirect to Stripe Checkout
            window.location.href = data.url;
          }
        })
        .catch(error => console.error('Error:', error));

      });
    });
  });
  


</script>



</body>

</html>