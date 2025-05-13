<?php
include 'C:\xampp\htdocs\arslenemna\crudweb\config.php';
include 'C:\xampp\htdocs\arslenemna\crudweb\Controller\CommentaireController.php';

$commentController = new CommentaireController($pdo);
$errors = [];
$submissionSuccess = false;

$id_film = $_GET['id_film'] ?? null;
if (!$id_film) {
    die("Film ID is required.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
            header("Location: page.php");
            exit;
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
  <title>Ajouter un commentaire</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      background-color: var(--background);
      color: var(--light);
      font-family: 'Netflix Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }

    .wrapper {
      width: 100%;
      max-width: 600px;
      background: var(--surface);
      padding: 2rem;
      border-radius: var(--border-radius);
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    header {
      text-align: center;
      margin-bottom: 2rem;
    }

    h1 {
      color: white;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold;
      color: var(--light);
    }
    main {
      margin-top: 100px;
      text-align: center;
    }

    input[type="text"],
    input[type="number"],
    textarea {
      width: 100%;
      padding: 0.8rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: var(--border-radius);
      background: rgba(255, 255, 255, 0.05);
      color: var(--light);
      font-size: 1rem;
    }

    textarea {
      min-height: 100px;
      resize: vertical;
    }

    input:focus,
    textarea:focus {
      outline: none;
      border-color: var(--primary);
      background: rgba(255, 255, 255, 0.1);
    }

    .btn-primary {
      background-color: var(--primary);
      color: var(--light);
      border: none;
      padding: 0.8rem 1rem;
      border-radius: var(--border-radius);
      cursor: pointer;
      font-size: 1rem;
      width: 100%;
      transition: var(--transition);
    }

    .btn-primary:hover {
      background-color: #f40612;
    }

    .error-message {
      color: var(--error);
      margin-top: 0.5rem;
    }

    .success-message {
      color: var(--success);
      text-align: center;
      margin-bottom: 1rem;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 1rem;
      color: var(--primary);
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <header>
      <div class="movieVibeLogo">
        <a id="logo" href="page.php"><img src="../logo.png" alt="MovieVibe Logo" id="logo1"></a>
      </div>
      <h1>Ajouter un commentaire</h1>
    </header>
    <main>
      <?php if (!empty($errors['general'])): ?>
        <p class="error-message"><?= htmlspecialchars($errors['general']) ?></p>
      <?php endif; ?>
      
      <form method="post" action="addComment.php?id_film=<?= htmlspecialchars($id_film) ?>">
        <div class="form-group">
          <label for="auteur">Auteur </label>
          <input type="text" name="auteur" id="auteur" value="<?= htmlspecialchars($_POST['auteur'] ?? '') ?>" >
          <?php if (isset($errors['auteur'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['auteur']) ?></p>
          <?php endif; ?>
        </div>
        
        <div class="form-group">
          <label for="contenu">Contenu </label>
          <textarea name="contenu" id="contenu" ><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
          <?php if (isset($errors['contenu'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['contenu']) ?></p>
          <?php endif; ?>
        </div>
        
        <div class="form-group">
          <label for="note">Note (0-10)</label>
          <input type="number" name="note" id="note" min="0" max="10" value="<?= htmlspecialchars($_POST['note'] ?? '') ?>">
          <?php if (isset($errors['note'])): ?>
            <p class="error-message"><?= htmlspecialchars($errors['note']) ?></p>
          <?php endif; ?>
        </div>
        
        <button type="submit" class="btn-primary">Envoyer</button>
      </form>
      
      <a href="page.php" class="back-link">Retour à la page principale</a>
    </main>
  </div>
</body>
</html>