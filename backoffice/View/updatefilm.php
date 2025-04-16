<?php
include 'C:\xampp\htdocs\backoffice\config.php';
include 'C:\xampp\htdocs\backoffice\Controller\FilmController.php';

$controller = new FilmController($pdo);
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

$film = $controller->getFilmById($id);
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST['titre'];
    $genre = $_POST['genre'];
    $annee_sortie = $_POST['annee_sortie'];
    $duree = $_POST['duree'];
    $age_recommande = $_POST['age_recommande'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../uploads/";
        $photoName = uniqid() . "-" . basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $photoName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath);
    }

    if ($controller->updateFilm($id, $titre, $genre, $annee_sortie, $duree, $age_recommande)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Échec de la mise à jour du film.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Modifier un Film</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
</head>
<body>
  <div class="wrapper">
    <main class="main-content">
      <section class="films-section">
        <div class="content-header">
          <div class="title-section">
            <h1>Modifier un Film</h1>
            <p class="subtitle">Formulaire de modification</p>
          </div>
        </div>

        <?php if ($error): ?>
          <p style="color:red"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
          <div class="form-grid">
            <div class="form-group">
              <label for="titre">Titre du film</label>
              <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($film->getTitre()) ?>" required>
            </div>
            <div class="form-group">
              <label for="genre">Genre</label>
              <input type="text" name="genre" id="genre" value="<?= htmlspecialchars($film->getGenre()) ?>" required>
            </div>
            <div class="form-group">
              <label for="annee_sortie">Année de sortie</label>
              <input type="number" name="annee_sortie" id="annee_sortie" value="<?= htmlspecialchars($film->getAnneeSortie()) ?>" min="1900" max="2025">
            </div>
            <div class="form-group">
              <label for="duree">Durée</label>
              <input type="time" name="duree" id="duree" value="<?= htmlspecialchars($film->getDuree()) ?>" step="1" required>
            </div>
            <div class="form-group">
              <label for="age_recommande">Âge recommandé</label>
              <input type="number" name="age_recommande" id="age_recommande" value="<?= htmlspecialchars($film->getAgeRecommande()) ?>" min="0" max="18">
            </div>
          <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
            <a href="index.php" class="btn-secondary"><i class="fas fa-times"></i> Annuler</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
