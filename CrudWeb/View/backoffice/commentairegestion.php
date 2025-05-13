<?php
include 'C:\xampp\htdocs\arslenemna\crudweb\config.php';
include 'C:\xampp\htdocs\arslenemna\crudweb\Controller\CommentaireController.php';
include 'C:\xampp\htdocs\arslenemna\crudweb\Controller\FilmController.php';

$controller = new CommentaireController($pdo);
$filmController = new FilmController($pdo);
$error = "";

$Commentaires = $controller->getAllCommentaires(); // avec jointure
$films = $filmController->getAllFilms(); // pour le dropdown

// Calculate statistics
$totalComments = count($Commentaires);
$ratings = array_map(function($comment) { return $comment->getNote(); }, $Commentaires);
$averageRating = $totalComments > 0 ? round(array_sum($ratings) / $totalComments, 1) : 0;

// Calculate ratings distribution (0-10)
$ratingDistribution = array_fill(0, 11, 0);
foreach ($ratings as $rating) {
    $ratingDistribution[(int)$rating]++;
}

// Calculate comments per film
$commentsPerFilm = [];
foreach ($Commentaires as $comment) {
    $filmId = $comment->getIdFilm();
    $filmTitle = $filmMap[$filmId] ?? 'Unknown';
    if (!isset($commentsPerFilm[$filmTitle])) {
        $commentsPerFilm[$filmTitle] = 0;
    }
    $commentsPerFilm[$filmTitle]++;
}
arsort($commentsPerFilm); // Sort by number of comments

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Initialize error array
    $errors = [];
    
    // Validate id_film
    $id_film = isset($_POST['id_film']) ? trim($_POST['id_film']) : '';
    if (empty($id_film)) {
        $errors['id_film'] = "Veuillez sélectionner un film";
    }

    // Validate auteur
    $auteur = isset($_POST['auteur']) ? trim($_POST['auteur']) : '';
    if (empty($auteur)) {
        $errors['auteur'] = "Le nom de l'auteur est requis";
    } elseif (strlen($auteur) < 2 || strlen($auteur) > 50) {
        $errors['auteur'] = "Le nom doit contenir entre 2 et 50 caractères";
    }

    // Validate contenu
    $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
    if (empty($contenu)) {
        $errors['contenu'] = "Le contenu est requis";
    } elseif (strlen($contenu) < 10) {
        $errors['contenu'] = "Le contenu doit contenir au moins 10 caractères";
    }

    // Validate note
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
    if ($note === '') {
        $errors['note'] = "La note est requise";
    } elseif (!is_numeric($note) || $note < 0 || $note > 10) {
        $errors['note'] = "La note doit être comprise entre 0 et 10";
    }

    // If no errors, proceed with saving
    if (empty($errors)) {
        $date_commentaire = date('Y-m-d');
        if ($controller->createCommentaire($id_film, $auteur, $contenu, $note, $date_commentaire)) {
            header("Location: commentairegestion.php");
            exit;
        } else {
            $error = "Échec de la création du Commentaire.";
        }
    }
}
// Map film ID to titre
$filmMap = [];
foreach ($films as $film) {
    $filmMap[$film->getId()] = $film->getTitre();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gestion des Commentaires - MovieVibe</title>
  <link rel="stylesheet" href="../style.css"/>
  <script src="../commentairegestion.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <style>
    .table-container {
      display: flex;
      justify-content: center;
      margin-top: 10px; 
      margin-left: 1px;
      margin-right: 150px;
    }

    table.animated-table {
      width: 80%;
      border-collapse: collapse;
      margin: 0 auto;
    }

    table.animated-table th, table.animated-table td {
      text-align: center;
      padding: 10px;
      border: 1px solid var(--divider);
    }
  </style>
</head>
<body>
<div class="wrapper">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="logo">
    <a id="logo" href="../frontoffice/page.php" ><img src="../logo.png" alt="MovieVibe Logo" id="logo1"></a>
      <h3 style="color: var(--primary);">MovieVibe</h3>
    </div>
    <nav class="menu">
      <a><i class="fas fa-user"></i> Client</a>
      <a data-attr="films" href="index.php"><i class="fas fa-film"></i> Films</a>
      <a data-attr="commentaires" class="active"><i class="fas fa-comment"></i> Commentaires</a>
      <a><i class="fas fa-calendar"></i> Événement</a>
      <a data-attr="produit" href="http://localhost/arslenemna/View/index.php"><i class="fas fa-shopping-cart"></i> Produit</a>
        <a href="http://localhost/arslenemna/View/backoffice/index.php">
            <i class="fa-solid fa-bell"></i> Réclamation
        </a>
 
  
    </nav>
    <div class="sidebar-footer">
      <button id="quitBtn" class="quit-button"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">
    <section class="Commentaires-section">
      
      <!-- Add this before the table-container div -->
<div class="statistics-dashboard">
    <h2>Statistiques des Commentaires</h2>
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-comments"></i></div>
            <div class="stat-details">
                <h3>Total Commentaires</h3>
                <p><?= $totalComments ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div class="stat-details">
                <h3>Note Moyenne</h3>
                <p><?= $averageRating ?>/10</p>
            </div>
        </div>
    </div>

    <div class="charts-container">
        <div class="chart">
            <h3>Distribution des Notes</h3>
            <div class="rating-bars">
                <?php for($i = 0; $i <= 10; $i++): ?>
                    <div class="bar-container">
                        <div class="bar-label"><?= $i ?></div>
                        <div class="bar">
                            <div class="bar-fill" style="height: <?= ($totalComments > 0 ? ($ratingDistribution[$i] / $totalComments * 100) : 0) ?>%">
                                <span class="bar-value"><?= $ratingDistribution[$i] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="chart">
            <h3>Commentaires par Film</h3>
            <div class="film-stats">
                <?php $count = 0; ?>
                <?php foreach ($commentsPerFilm as $film => $commentCount): ?>
                    <?php if ($count++ < 5): ?>
                    <div class="film-stat-row">
                        <div class="film-name"><?= htmlspecialchars($film) ?></div>
                        <div class="film-bar-container">
                            <div class="film-bar" style="width: <?= ($commentCount / max($commentsPerFilm) * 100) ?>%">
                                <span class="film-count"><?= $commentCount ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

      <!-- Table des commentaires -->
      <div class="table-container">
        <table id="CommentairesTable" class="animated-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Titre du Film</th>
              <th>Auteur</th>
              <th>Contenu</th>
              <th>Note</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($Commentaires as $commentaire): ?>
            <tr>
              <td><?= htmlspecialchars($commentaire->getIdCommentaire()) ?></td>
              <td><?= htmlspecialchars($filmMap[$commentaire->getIdFilm()] ?? 'Film inconnu') ?></td>
              <td><?= htmlspecialchars($commentaire->getAuteur()) ?></td>
              <td><?= htmlspecialchars($commentaire->getContenu()) ?></td>
              <td><?= htmlspecialchars($commentaire->getNote()) ?></td>
              <td><?= htmlspecialchars($commentaire->getDateCommentaire()) ?></td>
              <td>
                <a href="updateCommentaire.php?id=<?= $commentaire->getIdCommentaire() ?>" class="btn-edit">Modifier</a>
                <a href="deleteCommentaire.php?id=<?= $commentaire->getIdCommentaire() ?>" class="btn-delete" onclick="return confirm('Supprimer ce Commentaire ?')">Supprimer</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>
</body>
</html>
