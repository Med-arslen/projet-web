<?php
include 'C:\xampp\htdocs\crudweb\config.php';
include 'C:\xampp\htdocs\crudweb\Controller\FilmController.php';
include 'C:\xampp\htdocs\crudweb\Controller\HistoriqueController.php';

$controller = new FilmController($pdo);
$historiqueController = new HistoriqueController($pdo);
$error = "";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$film = $controller->getFilmById($id);

if (!$film) {
    header('Location: index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $genre = $_POST['genre'];
    $annee_sortie = $_POST['annee_sortie'];
    $duree = $_POST['duree'];
    $age_recommande = $_POST['age_recommande'];

    // Handle photo upload if needed
    $photoName = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../uploads/";
        $photoName = uniqid() . "-" . basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $photoName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath);
    }

    if ($controller->updateFilm($id, $titre, $genre, $annee_sortie, $duree, $age_recommande, $photoName)) {
        // Add to historique after successful update
        $historiqueController->addHistorique(
            'modification',
            $id,
            $titre,
            "Film modifié: $titre (Genre: $genre, Année: $annee_sortie)"
        );
        header("Location: index.php");
        exit;
    } else {
        $error = "Échec de la modification du film.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Film - MovieVibe</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <main class="content">
            <section class="form-section">
                <div class="section-header">
                    <h1>Modifier un Film</h1>
                    <p class="subtitle">Formulaire de modification</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="film-form">
                    <input type="hidden" name="id" value="<?= $film->getId() ?>">
                    
                    <div class="form-group">
                        <label for="titre">Titre du film *</label>
                        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($film->getTitre()) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="genre">Genre *</label>
                        <input type="text" id="genre" name="genre" value="<?= htmlspecialchars($film->getGenre()) ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="annee_sortie">Année de sortie *</label>
                            <input type="number" id="annee_sortie" name="annee_sortie" value="<?= $film->getAnneeSortie() ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="duree">Durée (minutes) *</label>
                            <input type="number" id="duree" name="duree" value="<?= $film->getDuree() ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="age_recommande">Âge recommandé *</label>
                            <input type="number" id="age_recommande" name="age_recommande" value="<?= $film->getAgeRecommande() ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="photo">Photo</label>
                        <input type="file" id="photo" name="photo" accept="image/*" class="file-input">
                        <?php if ($film->getPhoto()): ?>
                            <div class="current-photo">
                                <span>Photo actuelle: <?= htmlspecialchars($film->getPhoto()) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
