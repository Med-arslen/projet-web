<?php
include 'C:\xampp\htdocs\crudweb\config.php';
include 'C:\xampp\htdocs\crudweb\Controller\CommentaireController.php';
include 'C:\xampp\htdocs\crudweb\Controller\FilmController.php';

$controller = new CommentaireController($pdo);
$filmController = new FilmController($pdo);
$films = $filmController->getAllFilms();
$error = "";
$success = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID invalide.");
}

$id = (int)$_GET['id'];
$commentaire = $controller->getCommentaireById($id);

if (!$commentaire) {
    die("Commentaire non trouvé.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_film = $_POST['id_film'];
    $auteur = $_POST['auteur'];
    $contenu = $_POST['contenu'];
    $note = $_POST['note'];
    $date_commentaire = date('Y-m-d');

    if ($controller->updateCommentaire($id, $id_film, $auteur, $contenu, $note, $date_commentaire)) {
        $success = "Commentaire mis à jour avec succès.";
        header("Location: commentairegestion.php"); // Optionally redirect: header("Location: commentairegestion.php"); exit;
    } else {
        $error = "Erreur lors de la mise à jour du commentaire.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Modifier Commentaire - MovieVibe</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="wrapper">
  <main class="main-content">
    <section class="Commentaires-section">
      <div class="content-header">
        <h1>Modifier le Commentaire</h1>
        <a href="commentairegestion.php" class="btn btn-secondary">← Retour</a>
      </div>

      <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
      <?php elseif ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
      <?php endif; ?>

      <form method="POST">
        <div class="form-grid">
          <div class="form-group">
            <label for="id_film">Film</label>
            <select name="id_film" id="id_film" required>
              <?php foreach ($films as $film): ?>
                <option value="<?= $film->getId() ?>"
                  <?= $film->getId() == $commentaire->getIdFilm() ? 'selected' : '' ?>>
                  <?= htmlspecialchars($film->getTitre()) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="auteur">Auteur</label>
            <input type="text" name="auteur" id="auteur" value="<?= htmlspecialchars($commentaire->getAuteur()) ?>" required>
          </div>
          <div class="form-group">
            <label for="contenu">Contenu</label>
            <textarea name="contenu" id="contenu" rows="3" required><?= htmlspecialchars($commentaire->getContenu()) ?></textarea>
          </div>
          <div class="form-group">
            <label for="note">Note</label>
            <input type="number" name="note" id="note" min="0" max="10" value="<?= htmlspecialchars($commentaire->getNote()) ?>" required>
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
        </div>
      </form>
    </section>
  </main>
</div>
</body>
</html>
