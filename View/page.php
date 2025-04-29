<?php
include 'C:\xampp\htdocs\crudweb\config.php';
include 'C:\xampp\htdocs\crudweb\Controller\FilmController.php';
include 'C:\xampp\htdocs\crudweb\Controller\CommentaireController.php';

$filmController = new FilmController($pdo);
$films = $filmController->getAllFilms();

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>MovieVibe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
  <script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js"></script>
  <script src="scriptpage.js"></script>
</head>
<body>
  <div class="wrapper">
    <!-- HEADER -->
    <header>
      <div class="movieVibeLogo">
        <a id="logo" href="#home"><img src="logo.png" alt="MovieVibe Logo" id="logo1"></a>
      </div>      
      <nav class="main-nav">                
        <a href="#home" class="active">Accueil</a>
        <a href="#tvShows">Catalogue</a>
        <a href="#movies">Événement</a>
        <a href="#originals">Boutique</a>
        <a href="#reclamations">Réclamation</a>
      </nav>
      <nav class="sub-nav">
        <div class="search-container">
          <i class="fas fa-search search-icon"></i>
          <input type="text" class="search-bar" placeholder="Titres, personnes, genres">
        </div>
        <a href="#"><i class="fas fa-bell sub-nav-logo"></i></a>
        <a href="#" class="account-menu">
          <img src="https://occ-0-1432-1433.1.nflxso.net/dnm/api/v6/K6hjPJd6cR6FpVELC5Pd6ovHRSk/AAAABQnOnMxhb19v9lQZScL86ZpnI21__HC3fseilqjXbQRegistry4Ixz2m4URJi5eB1_KyqWqjgNHjlJuKPZkavs1YRYbPd5A.png?r=a41" alt="Account">
          <i class="fas fa-caret-down"></i>
        </a>
      </nav>      
    </header>

    <!-- MAIN CONTAINER -->
    <section class="main-container">
    <div class="location" id="home">
        <h1>Populaire sur MovieVibe</h1>
        <div class="movies-grid">
            <?php foreach ($films as $film): ?>
                <div class="movie-card">
                    <div class="movie-image">
                        <img src="<?= $film->getPhoto() ? '../uploads/' . htmlspecialchars($film->getPhoto()) : 'default-movie.jpg' ?>" 
                             alt="<?= htmlspecialchars($film->getTitre()) ?>">
                        <div class="movie-overlay">
                            <div class="movie-actions">
                                <button class="play-btn"><i class="fas fa-play"></i></button>
                                <button class="info-btn" onclick="toggleDetails(<?= $film->getId() ?>)">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="movie-info">
                        <div class="movie-header">
                            <h3><?= htmlspecialchars($film->getTitre()) ?></h3>
                            <span class="rating"><?= htmlspecialchars($film->getAgeRecommande()) ?>+</span>
                        </div>
                        <div class="movie-meta">
                            <span class="year"><?= htmlspecialchars($film->getAnneeSortie()) ?></span>
                            <span class="duration"><?= htmlspecialchars($film->getDuree()) ?> min</span>
                            <span class="genre"><?= htmlspecialchars($film->getGenre()) ?></span>
                        </div>
                    </div>
                    
                    <!-- Movie Details and Comment Form -->
                    <div id="details-<?= $film->getId() ?>" class="movie-details">
                        <div class="details-content">
                            <h4>Ajouter un commentaire</h4>
                            <form method="post" action="commentairegestion.php" class="comment-form">
                                <input type="hidden" name="id_film" value="<?= $film->getId() ?>">
                                <input type="text" name="auteur" placeholder="👤 Votre nom" required>
                                <input type="text" name="contenu" placeholder="✍️ Votre avis..." required>
                                <input type="number" name="note" placeholder="⭐ Note (1 à 10)" min="0" max="10" required>
                                <button type="submit"><i class="fas fa-paper-plane"></i> Envoyer</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

    <!-- FOOTER -->
    <footer>
      <p>&copy; 2025 MovieVibe. Tous droits réservés.</p>
    </footer>
  </div>
</body>
</html>
