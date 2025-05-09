<?php
require_once '../../config/database.php';
require_once '../../controller/ReclamationController.php';

$stats = ReclamationController::getStatistiquesReponseRec();
$lang = $_GET['lang'] ?? 'fr'; // par défaut
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique Réclamations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png" type="image/png">
    
    <!-- Fonts + Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>

    <!-- Bootstrap & Chart.js -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #111;
            color: #fff;
            padding-top: 100px;
        }

        .menu-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .netflixLogo img {
            width: 150px;
        }

        ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        ul li {
            margin-left: 20px;
        }

        ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
        }

        ul li a:hover {
            color: #f5f5f5;
        }

        .lang-selector form select {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 5px 10px;
            font-size: 14px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 30px;
            left: 0;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            padding: 10px;
            z-index: 100;
        }

        header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #111;
            z-index: 1000;
            padding: 10px 0;
            border-bottom: 1px solid #333;
        }

        .table {
            background-color: #222;
            color: #fff;
        }

        .table th {
            background-color: #333;
        }

        .btn-outline-primary {
            color: #fff;
            border-color: #fff;
        }

        .btn-outline-primary:hover {
            background-color: #333;
            border-color: #f5f5f5;
        }

        .canvas-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 250px;
            height: 250px;
            margin: 40px auto;
        }

        #pieChart {
            width: 200px !important;
            height: 200px !important;
        }
    </style>
</head>
<body>

    <header>
        <div class="menu-bar">
            <div class="netflixLogo">
                <a href="#"><img src="logo.png" alt="Logo Image"></a>
            </div>
            <ul>
                <li><a href="page.html">Home</a></li>
                <li><a href="#">Catalogue</a></li>
                <li><a href="event.html">Events <i class="fas fa-caret-down"></i></a></li>
                <li><a href="#">Achat</a></li>
                <li><a href="./reclamation.php">Réclamation</a></li>
            </ul>
            <div class="lang-selector">
                <form method="get" action="">
                    <select name="lang" onchange="this.form.submit()">
                        <option value="fr" <?= $lang == 'fr' ? 'selected' : '' ?>>Français</option>
                        <option value="en" <?= $lang == 'en' ? 'selected' : '' ?>>English</option>
                        <option value="es" <?= $lang == 'es' ? 'selected' : '' ?>>Español</option>
                        <option value="ar" <?= $lang == 'ar' ? 'selected' : '' ?>>Arabe</option>
                    </select>
                </form>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Votre Historique de Réclamations</h2>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nom Prénom</th>
                    <th>Email</th>
                    <th>Film Concerné</th>
                    <th>Type de Problème</th>
                    <th>Détails</th>
                    <th>Réponse</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM reclamationn";
                $conn = config::getConnexion();
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?= htmlspecialchars($row["nomprenom"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["nomfilm"]) ?></td>
                    <td><?= htmlspecialchars($row["type_rec"]) ?></td>
                    <td><?= htmlspecialchars($row["detail"]) ?></td>
                    <td><?= htmlspecialchars($row["reponse_rec"]) ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div id="statistiques" class="container mb-5 d-none">
            <h4 class="text-center">Statistiques des Réponses</h4>
            <div class="canvas-container">
                <canvas id="pieChart"></canvas>
            </div>
        </div>

        <div class="text-center my-4">
            <button class="btn btn-outline-primary" onclick="toggleStats()">Afficher / Masquer les Statistiques</button>
        </div>
    </div>

    <script>
        function toggleStats() {
            const statsDiv = document.getElementById('statistiques');
            statsDiv.classList.toggle('d-none');
        }

        const data = {
            labels: ["Avec réponse", "Sans réponse"],
            datasets: [{
                label: "Réclamations",
                data: [<?= $stats['avecReponse'] ?>, <?= $stats['sansReponse'] ?>],
                backgroundColor: ["#28a745", "#dc3545"]
            }]
        };

        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let label = tooltipItem.label;
                                let value = tooltipItem.raw;
                                let percentage = ((value / <?= $stats['total'] ?>) * 100).toFixed(2);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                maintainAspectRatio: false,
                aspectRatio: 1,
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('pieChart').getContext('2d');
            new Chart(ctx, config);
        });
    </script>

</body>
</html>
