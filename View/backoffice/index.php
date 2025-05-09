<?php
include 'C:\xampp\htdocs\crudweb\config.php';
include 'C:\xampp\htdocs\crudweb\Controller\FilmController.php';
include 'C:\xampp\htdocs\crudweb\Controller\CommentaireController.php'; // Add this line
include 'C:\xampp\htdocs\crudweb\Controller\HistoriqueController.php';

$controller = new FilmController($pdo);
$historiqueController = new HistoriqueController($pdo);
$commentController = new CommentaireController($pdo); // Ensure this is initialized
$films = $controller->getAllFilms();
$historique = $historiqueController->getRecentHistorique();
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

// Gestion de l'upload de la photo
$photoName = null;
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
	$uploadDir = "../uploads/";
	$photoName = uniqid() . "-" . basename($_FILES['photo']['name']);
	$targetPath = $uploadDir . $photoName;

	// Vérifiez si le dossier d'upload existe
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
  <link rel="stylesheet" href="../style.css"/>
  <script src="../script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
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
        <a data-attr="films" class="active"><i class="fas fa-film"></i> Films</a>
        <a data-attr="commentaires" href="commentairegestion.php"><i class="fas fa-comment"></i> Commentaires</a>
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
            <a href="chatbot.php" class="btn btn-primary"><i class="fas fa-robot"></i> Chatbot</a>
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
      <input type="text" name="titre" id="titre" >
    </div>
    <div class="form-group">
      <label for="genre">Genre</label>
      <input type="text" name="genre" id="genre" >
    </div>
    <div class="form-group">
      <label for="annee_sortie">Année de sortie</label>
      <input type="number" name="annee_sortie" id="annee_sortie" min="1900" max="2025">
    </div>
    <div class="form-group">
      <label for="duree">Durée (HH:MM:SS)</label>
      <input type="time" name="duree" id="duree" step="1">
    </div>
    <div class="form-group">
      <label for="age_recommande">Âge recommandé</label>
      <input type="number" name="age_recommande" id="age_recommande" min="0" max="18">
    </div>
    <div class="form-group mb-lg">
        <label for="photo">Photo:</label>
        <input type="file" name="photo"  class="form-control input-lg"><br>
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
                <th>photo</th>
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
    <?php if ($film->getPhoto()): ?>
        <img src="../uploads/<?= htmlspecialchars($film->getPhoto()) ?>" alt="Photo de profil" width="50">
    <?php else: ?>
        Pas de photo
    <?php endif; ?>
</td>		
  <td>
    <a href="updatefilm.php?id=<?= $film->getId() ?>" class="btn-edit">Modifier</a>
    <a href="deletefilm.php?id=<?= $film->getId() ?>" class="btn-delete" onclick="return confirm('Supprimer ce film ?')">Supprimer</a>
  </td>
</tr>
<tr>
  <td colspan="8">
    <div class="comments-section">
      <h4>Commentaires pour <?= htmlspecialchars($film->getTitre()) ?>:</h4>
      <?php 
        $comments = $commentController->getCommentairesByFilm($film->getId());
        if (count($comments) > 0): 
      ?>
        <ul>
          <?php foreach ($comments as $comment): ?>
            <li>
              <strong><?= htmlspecialchars($comment->getAuteur()) ?>:</strong> 
              <?= htmlspecialchars($comment->getContenu()) ?> 
              <em>(Note: <?= htmlspecialchars($comment->getNote()) ?>/10)</em>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Aucun commentaire pour ce film.</p>
      <?php endif; ?>
    </div>
  </td>
</tr>
<?php endforeach; ?>

          </tbody>
          </table>
        </div>
        <!-- Add this after the table-container div -->
        <section class="historique-section">
            <div class="historique-header">
                <h2><i class="fas fa-history"></i> Historique des modifications</h2>
                <p class="subtitle">Dernières actions effectuées</p>
            </div>
            <div class="historique-container">
                <?php if (empty($historique)): ?>
                    <div class="no-history">
                        <i class="fas fa-info-circle"></i>
                        <p>Aucune modification récente</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($historique as $entry): ?>
                        <div class="historique-item">
                            <div class="historique-icon <?= $entry['action_type'] ?>">
                                <?php 
                                $icon = match($entry['action_type']) {
                                    'création' => 'fa-plus-circle',
                                    'modification' => 'fa-edit',
                                    'suppression' => 'fa-trash',
                                    default => 'fa-info-circle'
                                };
                                ?>
                                <i class="fas <?= $icon ?>"></i>
                            </div>
                            <div class="historique-content">
                                <div class="historique-info">
                                    <span class="action-type">
                                        <?= ucfirst(htmlspecialchars($entry['action_type'])) ?>
                                    </span>
                                    <span class="action-date">
                                        <i class="far fa-clock"></i>
                                        <?= date('d/m/Y H:i', strtotime($entry['date_action'])) ?>
                                    </span>
                                </div>
                                <div class="historique-details">
                                    <?= htmlspecialchars($entry['details']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
      </section>
    </main>
  </div>
  <script>
  document.getElementById('searchInput').addEventListener('input', function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#filmsTable tbody tr');

    rows.forEach((row, index) => {
      const titleCell = row.querySelector('td:nth-child(2)');
      
      // Check if the row is a film row (has a title cell)
      if (titleCell) {
        const title = titleCell.textContent.toLowerCase();
        const shouldDisplay = title.includes(filter);
        row.style.display = shouldDisplay ? '' : 'none';

        // Hide the next row (comments row) if the film row is hidden
        const nextRow = rows[index + 1];
        if (nextRow && nextRow.querySelector('td[colspan="8"]')) {
          nextRow.style.display = shouldDisplay ? '' : 'none';
        }
      }
    });
  });
</script>
</body>
</html>
