<?php
include 'C:\xampp\htdocs\arslenemna\crudweb\config.php';
include 'C:\xampp\htdocs\arslenemna\crudweb\Controller\CommentaireController.php';
include 'C:\xampp\htdocs\arslenemna\crudweb\Controller\FilmController.php';

$filmController = new FilmController($pdo);
$commentController = new CommentaireController($pdo);
$films = $filmController->getAllFilms();

// Handle comment submission
$errors = [];
$submissionSuccess = false;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_film'])) {
    // Validate id_film
    $id_film = isset($_POST['id_film']) ? trim($_POST['id_film']) : '';
    if (empty($id_film)) {
        $errors['id_film'] = "Film ID is required";
    }

    // Validate auteur
    $auteur = isset($_POST['auteur']) ? trim($_POST['auteur']) : '';
    if (empty($auteur)) {
        $errors['auteur'] = "Author name is required";
    } elseif (strlen($auteur) < 2 || strlen($auteur) > 50) {
        $errors['auteur'] = "Author name must be between 2 and 50 characters";
    }

    // Validate contenu
    $contenu = isset($_POST['contenu']) ? trim($_POST['contenu']) : '';
    if (empty($contenu)) {
        $errors['contenu'] = "Content is required";
    } elseif (strlen($contenu) < 10) {
        $errors['contenu'] = "Content must be at least 10 characters";
    }

    // Validate note
    $note = isset($_POST['note']) ? trim($_POST['note']) : '';
    if ($note === '') {
        $errors['note'] = "Rating is required";
    } elseif (!is_numeric($note) || $note < 0 || $note > 10) {
        $errors['note'] = "Rating must be between 0 and 10";
    }

    // If no errors, save the comment
    if (empty($errors)) {
        $date_commentaire = date('Y-m-d');
        if ($commentController->createCommentaire($id_film, $auteur, $contenu, $note, $date_commentaire)) {
            $submissionSuccess = true;
            // Clear POST data to show empty form on success
            $_POST = [];
        } else {
            $errors['general'] = "Failed to save the comment. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>MovieVibe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <script src="../scriptpage.js"></script>
</head>
<body>
  <div class="wrapper">
    <!-- HEADER -->
    <header>
      <div class="movieVibeLogo">
        <a id="logo" href="#home"><img src="../logo.png" alt="MovieVibe Logo" id="logo1"></a>
      </div>      
      <nav class="main-nav">                  <a href="#home" class="active" style="font-size: 15px;">Accueil</a>
      <a href="http://localhost/arslenemna/View/page.php" style="font-size: 15px;">Boutique</a>
      <a href="#evenement" style="font-size: 15px;">Evénement</a>
      <a href="http://localhost/arslenemna/View/front/reclamation.php" style="font-size: 15px;">Réclamation</a>


      </nav>
      <nav class="sub-nav">

        <a href="http://localhost/arslenemna/CrudWeb/View/backoffice/" class="account-menu">
          <i class="fas fa-user-circle"></i>
          <span>Compte</span>
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
                  <div class="movie-info">
                    <h3><?= htmlspecialchars($film->getTitre()) ?></h3>
                    <p>Genre: <?= htmlspecialchars($film->getGenre()) ?></p>
                    <p>Année: <?= htmlspecialchars($film->getAnneeSortie()) ?></p>
                    <p>Durée: <?= htmlspecialchars($film->getDuree()) ?></p>
                    <p>Âge recommandé: <?= htmlspecialchars($film->getAgeRecommande()) ?>+</p>
                  </div>
                  <div class="movie-actions">
                    <a href="addComment.php?id_film=<?= $film->getId() ?>" class="btn btn-primary">Ajouter un commentaire</a>
                    <a href="filmDetails.php?id_film=<?= $film->getId() ?>" class="btn btn-primary">Voir les détails</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>





  <style>
    .comment-popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1000;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      width: 90%;
      max-width: 400px;
      padding: 20px;
      display: none;
    }

    .comment-popup-content {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .comment-popup-content h4 {
      text-align: center;
      margin-bottom: 10px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .form-group label {
      font-weight: bold;
    }

    .form-actions {
      display: flex;
      justify-content: center;
    }

    .btn-primary {
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn-primary:hover {
      background-color: #0056b3;
    }

    .close-popup {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 20px;
      cursor: pointer;
      color: #666;
    }

    .comment-popup::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: -1;
    }

    .error-message {
      color: #dc3545;
      font-size: 0.8em;
      margin-top: 5px;
    }

    .error-field {
      border: 1px solid #dc3545 !important;
    }

    .success-message {
      color: #28a745;
      background-color: #d4edda;
      border: 1px solid #c3e6cb;
      padding: 10px;
      border-radius: 4px;
      margin-bottom: 15px;
      text-align: center;
    }

    .movie-actions {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-top: 10px;
    }

    .btn-primary {
      background-color: var(--primary);
      color: var(--light);
      border: none;
      padding: 10px 15px;
      border-radius: var(--border-radius);
      cursor: pointer;
      font-size: 0.9rem;
      text-decoration: none;
      text-align: center;
      transition: var(--transition);
    }

    .btn-primary:hover {
      background-color: #f40612;
    }
  </style>

  <script>
    function showCommentForm(filmId) {
      document.getElementById('comment-film-id').value = filmId;
      document.getElementById('comment-form').style.display = 'block';
      
      // Clear previous errors and success messages
      const errorMessages = document.querySelectorAll('.error-message');
      errorMessages.forEach(msg => msg.remove());
      
      const successMessages = document.querySelectorAll('.success-message');
      successMessages.forEach(msg => msg.remove());
      
      const errorFields = document.querySelectorAll('.error-field');
      errorFields.forEach(field => field.classList.remove('error-field'));
    }

    function closeCommentForm() {
      document.getElementById('comment-form').style.display = 'none';
    }

    // Close popup when clicking outside of it
    window.addEventListener('click', function(event) {
      const popup = document.getElementById('comment-form');
      if (event.target === popup) {
        closeCommentForm();
      }
    });
  </script>
</body>
</html>