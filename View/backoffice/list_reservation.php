<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    select[name="status"] {
      width: 200px;
      padding: 10px;
      font-size: 16px;
      border: 2px solid #ccc;
      border-radius: 8px;
      background-color: #f9f9f9;
      color: #333;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }
    select[name="status"]:focus {
      border-color: #4CAF50;
      box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
    }
    select[name="status"] option {
      padding: 10px;
      background-color: #f9f9f9;
      color: #333;
    }
    select[name="status"] option[selected] {
      background-color: #4CAF50;
      color: white;
    }
    select[name="status"] option:hover {
      background-color: #e5e5e5;
    }

    :root {
      --clr-primary: #E50914;
      --clr-primary-light: #F2F2F2;
      --clr-primary-dark: #B30000;
      --clr-gray100: #f9fbff;
      --clr-gray200: #f0f4f8;
      --clr-gray300: #D1D9E6;
      --clr-gray400: #B0B8D0;
      --clr-gray500: #4F546C;
      --clr-gray600: #2A324B;
      --clr-link: #2962FF;
      --clr-hover-bg: rgba(229, 9, 20, 0.8);
      --clr-white: #ffffff;
      --clr-dark: #1e1e1e;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #ffe5e0, #fff9e5);
      min-height: 100vh;
      color: #333;
      display: flex;
    }

    .wrapper {
      display: flex;
      width: 100%;
    }

    .sidebar {
      width: 220px;
      background-color: #050505;
      color: white;
      padding: 20px;
      flex-shrink: 0;
    }

    .logo {
      text-align: center;
    }

    .logo img {
      width: 60px;
      margin-bottom: 10px;
    }

    .logo h2 {
      font-size: 20px;
      margin: 0;
    }

    .menu a {
      display: flex;
      align-items: center;
      padding: 10px;
      margin-top: 15px;
      text-decoration: none;
      color: #fff;
      border-radius: 8px;
      transition: 0.3s;
      cursor: pointer;
    }

    .menu a i {
      margin-right: 10px;
    }

    .menu a:hover,
    .menu a.active {
      background-color: #ac7b7b;
      transform: scale(1.05);
    }

    .sidebar-footer {
      margin-top: 20px;
    }

    .quit-button {
      width: 100%;
      background-color: transparent;
      border: 1px solid white;
      padding: 10px;
      color: white;
      border-radius: 5px;
      transition: 0.3s;
    }

    .quit-button:hover {
      background-color: red;
    }

    .content {
      flex: 1;
      padding: 40px;
    }

    .section-heading {
      font-size: 2.5rem;
      font-weight: 800;
      text-align: center;
      color: var(--clr-primary);
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: var(--clr-white);
      border-radius: 10px;
      overflow: hidden;
      margin-top: 30px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    thead {
      background-color: var(--clr-primary-light);
      color: var(--clr-dark);
    }

    th, td {
      padding: 15px 20px;
      text-align: left;
    }

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

    .amount {
      text-align: right;
      font-weight: bold;
      color: var(--clr-primary-dark);
    }

    .search-form {
      max-width: 400px;
      margin: 0 auto 20px auto;
    }

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
  </style>
</head>

<body style="background: url('../backoffice/assets/imgs/cinema.jpg') no-repeat center center fixed; background-size: cover;">

  <div class="wrapper">
    <aside class="sidebar">
      <div class="logo">
        <img src="../backoffice/assets/imgs/logo.png" alt="Logo MovieVibe" />
        <h2>MovieVibe</h2>
      </div>
      <nav class="menu">
        <a data-attr="client"><i class="fas fa-user"></i> Client</a>
        <a data-attr="event" class="has-submenu" onclick="toggleSubMenu(this)">
          <i class="fas fa-calendar"></i> Événements
          <i class="fas fa-chevron-down submenu-icon"></i>
        </a>
        <div class="submenu" style="display: none;">
          <a id="newEventBtn" data-attr="listeReservation"><i class="fas fa-list"></i> Liste des Réservations</a>
          <a href="./index.php"><i class="fas fa-plus-circle"></i> Nouveaux Événements</a>
        </div>
        <a data-attr="reclamation"><i class="fas fa-bell"></i> Réclamation</a>
        <a data-attr="produit"><i class="fas fa-shopping-cart"></i> Produits</a>
        <a data-attr="film"><i class="fas fa-film"></i> Films</a>
        <div class="sidebar-footer">
          <button id="quitBtn" class="quit-button"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
        </div>
      </nav>
    </aside>

    <div class="content">
    <div style="text-align: right; position: relative; margin-bottom: 10px;">
 
</div>

      <h2 class="section-heading">Your Reservation History</h2>

      <!-- Search Form -->
      <form method="GET" class="search-form">
        <div class="input-group">
          <input type="text" name="user_id" class="form-control" placeholder="Enter User ID" value="<?= isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id']) : '' ?>">
          <button class="btn btn-outline-danger" type="submit">Search</button>
        </div>
      </form>
      <a href="generate_pdf.php" target="_blank" class="btn btn-danger" style="float: right; margin-bottom: 10px;">
  Télécharger en PDF (Serveur)
</a>



      <table>
        <thead>
          <tr>
            <th>User Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Event Name</th>
            <th>Reservation Type</th>
            <!-- Reservation Date header becomes a clickable sort link -->
            <th>
              <a href="?<?= isset($_GET['user_id']) ? 'user_id=' . urlencode($_GET['user_id']) . '&' : '' ?>sort=<?= (isset($_GET['sort']) && $_GET['sort'] === 'date_desc') ? 'date' : 'date_desc' ?>" style="text-decoration:none; color:inherit;">
                Reservation Date <?= (isset($_GET['sort']) && $_GET['sort'] === 'date_desc') ? '🔼' : '🔽' ?>
              </a>
            </th>
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
          $reservationController = new ReservController();

          $reservations = $reservationController->getReservations();

          if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
              $reservations = array_filter($reservations, function ($res) {
                  return $res['id_user'] == $_GET['user_id'];
              });
          }

          // Sorting by date based on GET parameter
          if (isset($_GET['sort'])) {
              if ($_GET['sort'] === 'date') {
                  usort($reservations, function ($a, $b) {
                      return strtotime($a['date_reserv']) <=> strtotime($b['date_reserv']);
                  });
              } elseif ($_GET['sort'] === 'date_desc') {
                  usort($reservations, function ($a, $b) {
                      return strtotime($b['date_reserv']) <=> strtotime($a['date_reserv']);
                  });
              }
          }

          if (empty($reservations)) {
              echo '<tr><td colspan="10" style="text-align:center;">No reservations found.</td></tr>';
          } else {
              foreach ($reservations as $row) {
          ?>
              <tr>
                <td><?= htmlspecialchars($row["id_user"]) ?></td>
                <td><a href="view_reservation.php?id=<?= $row['id_reserv'] ?>"><?= htmlspecialchars($row["name"]) ?></a></td>
                <td><?= htmlspecialchars($row["email"]) ?></td>
                <td><?= htmlspecialchars($row["name_event"]) ?></td>
                <td><?= htmlspecialchars($row["type"]) ?></td>
                <td><?= htmlspecialchars($row["date_reserv"]) ?></td>
                <td><?= htmlspecialchars($row["nb_people"]) ?></td>
                <td class="amount"><?= htmlspecialchars($row["price"]) ?> DT</td>
                <td>
                  <form action="update_reservation_status.php" method="POST">
                    <input type="hidden" name="id_reserv" value="<?= $row["id_reserv"] ?>">
                    <select name="status" onchange="this.form.submit()">
                      <option value="canceled" <?= $row['state'] == 'canceled' ? 'selected' : '' ?>>Canceled</option>
                      <option value="confirmed" <?= $row['state'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                      <option value="pending" <?= $row['state'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                  </form>
                </td>
                <td>
                  <form action="cancelReservation.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?');">
                    <input type="hidden" name="id" value="<?= $row["id_reserv"] ?>">
                    <button type="submit" class="btn btn-link link-dark p-0" title="Cancel Reservation">
                      <i class="fa-solid fa-trash fs-5"></i>
                    </button>
                  </form>
                </td>
              </tr>
          <?php } } ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <script>
    function toggleSubMenu(element) {
      const submenu = element.nextElementSibling;
      if (submenu && submenu.classList.contains("submenu")) {
        const isVisible = submenu.style.display === "block";
        submenu.style.display = isVisible ? "none" : "block";
        const icon = element.querySelector(".submenu-icon");
        if (icon) icon.style.transform = isVisible ? "rotate(0deg)" : "rotate(180deg)";
      }
    }
  </script>



</body>
</html>
