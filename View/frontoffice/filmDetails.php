<?php
include 'C:\xampp\htdocs\crudweb\config.php';
include 'C:\xampp\htdocs\crudweb\Controller\FilmController.php';

$filmController = new FilmController($pdo);

$id_film = $_GET['id_film'] ?? null;
if (!$id_film) {
    die("Film ID is required.");
}

$film = $filmController->getFilmById($id_film);
if (!$film) {
    die("Film not found.");
}

include 'C:\xampp\htdocs\crudweb\Controller\CommentaireController.php';

$commentaireController = new CommentaireController($pdo);
$comments = $commentaireController->getCommentairesByFilm($id_film);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Détails du Film</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .details-container {
      max-width: 800px;
      margin: 50px auto;
      background: var(--surface);
      padding: 20px;
      border-radius: var(--border-radius);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      color: var(--light);
    }

    .details-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .details-header h1 {
      color: var(--primary);
      font-size: 2rem;
    }

    .details-content {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .details-content img {
      max-width: 100%;
      border-radius: var(--border-radius);
    }

    .details-content p {
      font-size: 1rem;
      line-height: 1.5;
      color: var(--text-secondary);
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: var(--primary);
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    .comments-section {
      margin-top: 30px;
      padding: 20px;
      background: var(--surface);
      border-radius: var(--border-radius);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .comments-section h2 {
      color: var(--primary);
      margin-bottom: 15px;
    }

    .comments-list {
      list-style: none;
      padding: 0;
    }

    .comment-item {
      margin-bottom: 15px;
      padding: 10px;
      border-bottom: 1px solid var(--divider);
    }

    .comment-item:last-child {
      border-bottom: none;
    }

    .comment-item p {
      margin: 5px 0;
    }
  </style>
</head>
<body>
<header>
      <div class="movieVibeLogo">
        <a id="logo" href="page.php"><img src="../logo.png" alt="MovieVibe Logo" id="logo1"></a>
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
  <div class="details-container">
    <div class="details-header">
      <h1><?= htmlspecialchars($film->getTitre()) ?></h1>
    </div>
    <div class="details-content">
      <?php if ($film->getPhoto()): ?>
        <img src="../uploads/<?= htmlspecialchars($film->getPhoto()) ?>" alt="<?= htmlspecialchars($film->getTitre()) ?>">
      <?php else: ?>
        <p>Pas de photo disponible.</p>
      <?php endif; ?>
      <p><strong>Genre:</strong> <?= htmlspecialchars($film->getGenre()) ?></p>
      <p><strong>Année de sortie:</strong> <?= htmlspecialchars($film->getAnneeSortie()) ?></p>
      <p><strong>Durée:</strong> <?= htmlspecialchars($film->getDuree()) ?></p>
      <p><strong>Âge recommandé:</strong> <?= htmlspecialchars($film->getAgeRecommande()) ?>+</p>
    </div>
    <div class="comments-section">
      <h2>Commentaires</h2>
      <?php if (count($comments) > 0): ?>
        <ul class="comments-list">
          <?php foreach ($comments as $comment): ?>
            <li class="comment-item">
              <p><strong><?= htmlspecialchars($comment->getAuteur()) ?>:</strong> <?= htmlspecialchars($comment->getContenu()) ?></p>
              <p><em>Note:</em> <?= htmlspecialchars($comment->getNote()) ?>/10</p>
              <p><small>Posté le: <?= htmlspecialchars($comment->getDateCommentaire()) ?></small></p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Aucun commentaire pour ce film.</p>
      <?php endif; ?>
    </div>
    <a href="page.php" class="back-link">← Retour à la liste des films</a>
  </div>
</body>
</html>
