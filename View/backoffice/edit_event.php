<?php
require_once __DIR__ . '/../../config/database.php';

$conn = config::getConnexion();

$error = "";

if (isset($_POST["submit"])) {
    $name_event = $_POST['name_event'];
    $location = $_POST['location'];
    $total_places = $_POST['total_places'];
    $price_event = $_POST['price_event'];
    $description = $_POST['description'];
    $id_film = $_POST['id_film'];
    $date_event = $_POST['date_event'];
    $id_event = $_POST['id_event'];

    try {
        $sql = "UPDATE event 
                SET name_event = :name_event, location = :location, total_places = :total_places, price_event = :price_event, 
                    description = :description, id_film = :id_film, date_event = :date_event 
                WHERE id_event = :id_event";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name_event' => $name_event,
            ':location' => $location,
            ':total_places' => $total_places,
            ':price_event' => $price_event,
            ':description' => $description,
            ':id_film' => $id_film,
            ':date_event' => $date_event,
            ':id_event' => $id_event
        ]);

        header("Location: index.php?msg=Event updated successfully");
        exit;
    } catch (PDOException $e) {
        $error = "Error updating event: " . $e->getMessage();
    }
}

if (!isset($_GET["id"])) {
    echo "Event ID is missing in the URL!";
    exit;
}

$id = $_GET["id"];

try {
    $stmt = $conn->prepare("SELECT * FROM event WHERE id_event = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "Event not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error fetching event: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../backoffice/css/add.css" rel="stylesheet">
    <style>
        .wrapper {
            display: flex;
            height: 100vh;
            position: relative;
            z-index: 1;
        }

        .sidebar {
            width: 220px;
            background-color: #050505;
            color: white;
            padding: 20px;
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

        .submenu {
            margin-left: 15px;
            margin-top: 10px;
        }

        .submenu a {
            font-size: 0.95rem;
            background-color: #1a1a1a;
            margin-top: 8px;
            transition: 0.3s;
        }

        .submenu a:hover {
            background-color: #ac7b7b;
            transform: scale(1.03);
        }

        #quitBtn {
            margin-top: 25px;
            background-color:rgb(143, 51, 42);
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 8px;
            width: 100%;
            text-align: left;
            transition: 0.3s;
        }

        #quitBtn:hover {
            background-color: #ac7b7b;
            transform: scale(1.05);
            cursor: pointer;
        }

        .container-fluid {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
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
            <a class="" data-attr="client"><i class="fas fa-user"></i> Client</a>

            <a data-attr="event" class="has-submenu" onclick="toggleSubMenu(this)">
                <i class="fas fa-calendar"></i> Événements
                <i class="fas fa-chevron-down submenu-icon"></i>
            </a>
            <div class="submenu" style="display: none;">
                <a data-attr="listeReservation"><i class="fas fa-list"></i> Liste des Réservations</a>
                <a id="newEventBtn" href="../backoffice/index.php"><i class="fas fa-plus-circle"></i> Nouveaux Événements</a>
            </div>

            <a data-attr="reclamation"><i class="fas fa-bell"></i> Réclamation</a>
            <a data-attr="produit"><i class="fas fa-shopping-cart"></i> Produits</a>
            <a data-attr="film"><i class="fas fa-film"></i> Films</a>

            <button id="quitBtn"><i class="fas fa-sign-out-alt"></i> Quitter</button>
        </nav>
    </aside>

    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg mb-5 shadow-lg rounded-3" style="background: #8B0000; border-bottom: 4px solid #d3a5a5;">
            <div class="container justify-content-center">
                <span class="navbar-brand mb-0 h1 text-white fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 3rem;">
                    🎜 Edit Event
                </span>
            </div>
        </nav>

        <div class="container">
            <div class="text-center mb-4">
                <p class="fs-2 fw-light" style="color: #ffd700; background: linear-gradient(to right, #f4a261, #e76f51); -webkit-background-clip: text; color: transparent; font-family: 'Roboto', sans-serif; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">
                    Make changes and click save to update
                </p>
                <div style="width: 60px; height: 3px; background-color: #f4a261; margin: 10px auto;"></div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="container d-flex justify-content-center">
                <form action="" method="post" style="width:50vw; min-width:300px;">
                    <div class="mb-3">
                        <label class="form-label">Event Name:</label>
                        <input type="text" class="form-control" name="name_event" value="<?= htmlspecialchars($row['name_event']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location:</label>
                        <input type="text" class="form-control" name="location" value="<?= htmlspecialchars($row['location']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Places:</label>
                        <input type="number" class="form-control" name="total_places" value="<?= htmlspecialchars($row['total_places']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (TND):</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control" name="price_event" value="<?= htmlspecialchars($row['price_event']) ?>" required>
                            <span class="input-group-text">DT</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description:</label>
                        <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($row['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Film ID:</label>
                        <input type="number" class="form-control" name="id_film" value="<?= htmlspecialchars($row['id_film']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Date & Time:</label>
                        <input type="datetime-local" class="form-control" name="date_event" value="<?= date('Y-m-d\TH:i', strtotime($row['date_event'])) ?>" required>
                    </div>
                    <input type="hidden" name="id_event" value="<?= $id ?>">
                    <div>
                        <button type="submit" class="btn btn-success" name="submit">Save</button>
                        <a href="index.php" class="btn btn-danger">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSubMenu(el) {
        const submenu = el.nextElementSibling;
        if (submenu && submenu.classList.contains('submenu')) {
            submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
        }
    }
</script>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
