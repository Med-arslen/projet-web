<?php
include 'C:\xampp\htdocs\backoffice\config.php';
include 'C:\xampp\htdocs\backoffice\Controller\FilmController.php';


    $controller = new FilmController($pdo);
    $films= $controller->getAllFilms();
    ?>
    <?php
$controller = new FilmController($pdo);
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre = $_POST['titre'];
    $genre = $_POST['genre'];
    $annee_sortie = $_POST['annee_sortie'];
    $duree = $_POST['duree'];
    $age_recommande = $_POST['age_recommande'];

    // Gestion de l'upload de l'affiche ou image
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

    if ($controller->createFilm($titre, $genre, $annee_sortie, $duree, $age_recommande, $photoName)) {
      header("Location: index.php");
      exit;
    } else {
        $error = "Échec de la création du film.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="MovieVibe - Système de gestion de films"/>
  <meta name="theme-color" content="#141414"/>
  <title>Gestion des Films - MovieVibe</title>
  <link rel="stylesheet" href="style.css"/>
  <script defer src="script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
</head>
<body>
  <div class="wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="logo">
        <img src="logo.png" alt="Logo MovieVibe" />
        <h2>MovieVibe</h2>
      </div>
      <nav class="menu">
        <a data-attr="films" class="active"><i class="fas fa-film"></i> Films</a>
        <a data-attr="event"><i class="fas fa-calendar"></i> Événement</a>
        <a data-attr="reclamation"><i class="fas fa-bell"></i> Réclamation</a>
        <a data-attr="produit"><i class="fas fa-shopping-cart"></i> Produit</a>
        <a data-attr="client"><i class="fas fa-user"></i> Client</a>
      </nav>
      <div class="sidebar-footer">
        <button id="quitBtn" class="quit-button"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <!-- TOP BAR -->
      <header class="top-bar">
      
      </header>

      <!-- FILMS MANAGEMENT SECTION -->
      <section class="films-section">
        <div class="content-header">
          <div class="title-section">
            <h1>Gestion des Films</h1>
            <p class="subtitle">Vue d'ensemble de votre catalogue</p>
          </div>
          <div class="btn-group">
           
          </div>
        </div>
        <div class="search-zone">
          <i class="fas fa-search search-icon"></i>
          <input type="text" id="searchInput" placeholder="Rechercher un film..." />
        </div>
        <form name="userForm" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
  <div class="form-header">

  </div>
  <div class="form-grid">
    <div class="form-group">
      <label for="titre">Titre du film</label>
      <input type="text" name="titre" id="titre" required>
    </div>
    <div class="form-group">
      <label for="genre">Genre</label>
      <input type="text" name="genre" id="genre" required>
    </div>
    <div class="form-group">
      <label for="annee_sortie">Année de sortie</label>
      <input type="number" name="annee_sortie" id="annee_sortie" min="1900" max="2025">
    </div>
    <div class="form-group">
      <label for="duree">Durée (HH:MM:SS)</label>
      <input type="time" name="duree" id="duree" step="1" required>
    </div>
    <div class="form-group">
      <label for="age_recommande">Âge recommandé</label>
      <input type="number" name="age_recommande" id="age_recommande" min="0" max="18">
    </div>
  
  </div>
  <div class="form-actions">
    <button type="submit" id="addFilmBtn" class="btn btn-primary" >
      <i class="fas fa-save"></i> Enregistrer
    </button>
 
  </div>
</form>

<br></br>
        <!-- Films Table -->
        <div class="table-container">
          <table id="filmsTable" class="animated-table">
            <thead>
              <tr>
                <th>id_film</th>
                <th>titre</th>
                <th>genre</th>
                <th>anne_sortie</th>
                <th>duree</th>
                <th>age_recommande</th>
                <th>action</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($films as $film): ?>
<tr>
  <td><?= htmlspecialchars($film->getId()) ?></td>
  <td><?= htmlspecialchars($film->getTitre()) ?></td>
  <td><?= htmlspecialchars($film->getGenre()) ?></td>
  <td><?= htmlspecialchars($film->getAnneeSortie()) ?></td>
  <td><?= htmlspecialchars($film->getDuree()) ?></td>
  <td><?= htmlspecialchars($film->getAgeRecommande()) ?></td>
  <td>
    <a href="updatefilm.php?id=<?= $film->getId() ?>" class="btn-edit">Modifier</a>
    <a href="deletefilm.php?id=<?= $film->getId() ?>" class="btn-delete" onclick="return confirm('Supprimer ce film ?')">Supprimer</a>
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
