<?php
require_once '../../controller/feedbackController.php';
$lang = $_GET['lang'] ?? 'fr';
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <title>Merci - MovieVibe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #111;
            color: #fff;
            padding-top: 80px;
        }

        /* Header et Navigation */
        .main-header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #111;
            z-index: 1000;
            padding: 10px 0;
            border-bottom: 1px solid #333;
        }

        .menu-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .netflixLogo {
            display: flex;
            align-items: center;
        }

        .netflixLogo img {
            width: 40px;
            margin-right: 10px;
        }

        ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }

        ul li {
            margin-left: 20px;
        }

        ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        ul li a:hover {
            color: #f5f5f5;
        }

        .reclamation {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #222;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            padding: 10px;
            z-index: 100;
            min-width: 150px;
        }

        .dropdown-menu a {
            display: block;
            padding: 8px 12px;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .dropdown-menu a:hover {
            background-color: #333;
        }

        .lang-selector {
            margin-left: 20px;
        }

        .lang-selector select {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        .thank-you-container {
            text-align: center;
            background-color: #222;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 40px auto;
        }

        .thank-you-title {
            font-size: 2em;
            color: #ff0000; /* Changement de la couleur en rouge */
            margin-bottom: 20px;
        }

        .thank-you-message {
            font-size: 1.2em;
            color: #ccc;
            margin-bottom: 30px;
        }

        .return-button {
            display: inline-block;
            background-color: #8B0000;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .return-button:hover {
            background-color: #a00;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <nav class="menu-bar">
            <div class="netflixLogo">
                <a id="logo" href="#home">
                    <img src="logo.png" alt="Logo Movie Vibe" id="logo1">
                </a>
            </div>    
            <ul>
        <li><a href="page.html"><?= feedbackController::traduction_form('Accueil', $lang) ?></a></li>
        <li><a href="../page.php"><?= feedbackController::traduction_form('Boutique', $lang) ?></a></li>
        <li><a href="event.html"><?= feedbackController::traduction_form('Evénement', $lang) ?></a></li>
                <li class="reclamation">
                    <a href="#" onclick="event.preventDefault(); toggleDropdown();">
                        <?= feedbackController::traduction_form('Réclamation', $lang) ?>
                        <span class="arrow">▼</span>
                    </a>
                    <div class="dropdown-menu" id="dropdown-menu">
                        <a href="historique.php"><?= feedbackController::traduction_form('Historique', $lang) ?></a>
                    </div>
                </li>
            </ul>
            <div class="lang-selector">
                <form method="GET">
                    <select name="lang" onchange="this.form.submit()">
                        <option value="fr" <?= $lang === 'fr' ? 'selected' : '' ?>>Français</option>
                        <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
                        <option value="es" <?= $lang === 'es' ? 'selected' : '' ?>>Español</option>
                        <option value="ar" <?= $lang === 'ar' ? 'selected' : '' ?>>العربية</option>
                    </select>
                </form>
            </div>
        </nav>
    </header>

    <div class="thank-you-container">
        <h1 class="thank-you-title"><?= feedbackController::traduction_form("Merci pour votre feedback !", $lang) ?></h1>
        <p class="thank-you-message"><?= feedbackController::traduction_form("Nous apprécions grandement votre contribution qui nous aidera à améliorer nos services.", $lang) ?></p>
        <a href="page.html" class="return-button"><?= feedbackController::traduction_form("Retour à l'accueil", $lang) ?></a>
    </div>

    <script>
        function toggleDropdown() {
            const menu = document.getElementById('dropdown-menu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        window.addEventListener('click', function(e) {
            const target = e.target;
            if (!target.closest('.reclamation')) {
                const menu = document.getElementById('dropdown-menu');
                if (menu) menu.style.display = 'none';
            }
        });
    </script>
</body>
</html>
