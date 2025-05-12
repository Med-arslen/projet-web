<?php
include 'C:\xampp\htdocs\crudweb\config.php';
include 'C:\xampp\htdocs\crudweb\Controller\CommentaireController.php';
include 'C:\xampp\htdocs\crudweb\Controller\FilmController.php';

$id_film = $_GET['id_film'] ?? null;
if (!$id_film) {
    die("Film ID is required.");
}

$filmController = new FilmController($pdo);
$commentaireController = new CommentaireController($pdo);

$film = $filmController->getFilmById($id_film);
if (!$film) {
    die("Film not found.");
}

$comments = $commentaireController->getCommentairesByFilm($id_film);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Commentaires - <?= htmlspecialchars($film->getTitre()) ?></title>
  <link rel="stylesheet" href="../style.css">
  <style>
    .comments-container {
      max-width: 800px;
      margin: 50px auto;
      background: var(--surface);
      padding: 20px;
      border-radius: var(--border-radius);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .comments-header {
      text-align: center;
      margin-bottom: 20px;
    }

    .comments-header h1 {
      color: var(--primary);
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
  </style>
</head>
<body>
  <div class="comments-container">
    <div class="comments-header">
      <h1>Commentaires pour <?= htmlspecialchars($film->getTitre()) ?></h1>
    </div>
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
    <a href="index.php" class="back-link">← Retour à la gestion des films</a>
  </div>
</body>
</html>
