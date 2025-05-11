<?php
require_once '../../config/database.php';
require_once '../../controller/ReclamationController.php';

$stats = ReclamationController::getStatistiquesReponseRec();
$lang = $_GET['lang'] ?? 'fr'; // par défaut

// Configuration de la pagination
$items_per_page = 5; // Nombre d'éléments par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Récupérer le nombre total de réclamations
$conn = config::getConnexion();
$total_query = "SELECT COUNT(*) as total FROM reclamationn";
$total_stmt = $conn->query($total_query);
$total_records = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_records / $items_per_page);

// Requête avec pagination
$sql = "SELECT * FROM reclamationn LIMIT :offset, :items_per_page";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title><?= ReclamationController::traduction_historique('Votre Historique de Réclamations', $lang) ?></title>
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

        [lang="ar"] {
            direction: rtl;
            text-align: right;
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
                <li><a href="page.html"><?= ReclamationController::traduction_historique('Home', $lang) ?></a></li>
                <li><a href="#"><?= ReclamationController::traduction_historique('Catalogue', $lang) ?></a></li>
                <li><a href="event.html"><?= ReclamationController::traduction_historique('Events', $lang) ?> <i class="fas fa-caret-down"></i></a></li>
                <li><a href="#"><?= ReclamationController::traduction_historique('Achat', $lang) ?></a></li>
                <li><a href="./reclamation.php"><?= ReclamationController::traduction_historique('Réclamation', $lang) ?></a></li>
            </ul>
            <div class="lang-selector">
                <form method="get" action="">
                    <select name="lang" onchange="this.form.submit()">
                        <option value="fr" <?= $lang == 'fr' ? 'selected' : '' ?>>Français</option>
                        <option value="en" <?= $lang == 'en' ? 'selected' : '' ?>>English</option>
                        <option value="es" <?= $lang == 'es' ? 'selected' : '' ?>>Español</option>
                        <option value="ar" <?= $lang == 'ar' ? 'selected' : '' ?>>العربية</option>
                    </select>
                </form>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <h2 class="text-center mb-4"><?= ReclamationController::traduction_historique('Votre Historique de Réclamations', $lang) ?></h2>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?= ReclamationController::traduction_historique('Nom Prénom', $lang) ?></th>
                    <th><?= ReclamationController::traduction_historique('Email', $lang) ?></th>
                    <th><?= ReclamationController::traduction_historique('Film Concerné', $lang) ?></th>
                    <th><?= ReclamationController::traduction_historique('Type de Problème', $lang) ?></th>
                    <th><?= ReclamationController::traduction_historique('Détails', $lang) ?></th>
                    <th><?= ReclamationController::traduction_historique('Réponse', $lang) ?></th>
                </tr>
            </thead>
            
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row["nomprenom"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["nomfilm"]) ?></td>
                    <td><?= htmlspecialchars($row["type_rec"]) ?></td>
                    <td><?= htmlspecialchars($row["detail"]) ?></td>
                    <td><?= htmlspecialchars($row["reponse_rec"]) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page-1 ?>&lang=<?= $lang ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&lang=<?= $lang ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page+1 ?>&lang=<?= $lang ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <div id="statistiques" class="container mb-5 d-none">
            <h4 class="text-center"><?= ReclamationController::traduction_historique('Statistiques des Réponses', $lang) ?></h4>
            <div class="canvas-container">
                <canvas id="pieChart"></canvas>
            </div>
        </div>

        <div class="text-center my-4">
            <button class="btn btn-outline-primary" onclick="toggleStats()">
                <?= ReclamationController::traduction_historique('Afficher / Masquer les Statistiques', $lang) ?>
            </button>
        </div>
    </div>

    <script>
        function toggleStats() {
            const statsDiv = document.getElementById('statistiques');
            statsDiv.classList.toggle('d-none');
        }

        const data = {
            labels: [
                "<?= ReclamationController::traduction_historique('Avec réponse', $lang) ?>",
                "<?= ReclamationController::traduction_historique('Sans réponse', $lang) ?>"
            ],
            datasets: [{
                label: "<?= ReclamationController::traduction_historique('Statistiques des Réponses', $lang) ?>",
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
