<?php
// Connexion à la base de données
require_once '../../config/database.php';  // Correction du chemin relatif vers database.php

// Traitement du formulaire si soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $note = $_POST['note']; // Cette variable correspond à l'analyse (note du service)
    $simplicite = $_POST['simplicite']; // Cette variable correspond aux conseils
    $temps = $_POST['temps']; // Cette variable correspond aux conseils
    $commentaire = $_POST['commentaire']; // Cette variable correspond au conseil détaillé
    $id_rec = $_POST['id_rec']; // ID de la réclamation

    // Vérification que l'id_rec existe dans la table 'reclamationn'
    $stmt = $pdo->prepare("SELECT id_rec FROM reclamationn WHERE id_rec = ?");
    $stmt->execute([$id_rec]);
    $reclamation = $stmt->fetch();

    if ($reclamation) {
        // Insertion dans la table feedbackk
        $query = "INSERT INTO feedbackk (analyse, conseil, id_rec) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$note, $commentaire, $id_rec]);

        // Redirection ou confirmation après soumission
        header('Location: merci.html');
        exit();
    } else {
        echo "Erreur : cette réclamation n'existe pas.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>MovieVibe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Formulaire de réclamation pour signaler un problème technique avec un film">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="feedback.js" defer></script>
</head>
<body style="color: #000000;">  <!-- Ajout du style en noir ici -->
  <div class="wrapper">
    <header class="main-header">
      <div class="menu-bar">
        <div class="netflixLogo">
          <a id="logo" href="index.html" aria-label="Retour à l'accueil">
            <img src="logo.png" alt="Movie-Vibe Logo" id="logo1">
          </a>
        </div>
        <div class="nav-top-center">
  <nav class="main-nav">
    <ul>
      <li><a href="index.html">Accueil</a></li>
      <li><a href="catalogue.html">Catalogue</a></li>
      <li><a href="events.html">Événements</a></li>
      <li><a href="achat.html">Acheter</a></li>
      <li><a href="reclamation.php">Réclamation</a>
        <div class="dropdown-menu">
          <ul>
            <li><a href="./historique.php">Historique</a></li>
          </ul>
        </div>
      </li>
    </ul>
  </nav>
</div>

    </header>

    <main class="form-main-container">
      <div class="form-card">
        <h1 class="form-title" style="color: #000000;">Merci pour votre réclamation !</h1>  <!-- Texte en noir -->
        <p class="form-description" style="color: #000000;">Votre demande a bien été reçue. Aidez-nous à améliorer notre service en répondant à ce court sondage :</p>  <!-- Texte en noir -->

        <form id="feedbackForm" method="POST" novalidate>
          <!-- Champ caché pour l'ID de la réclamation -->
          <input type="hidden" name="id_rec" value="<?= isset($_GET['id_rec']) ? $_GET['id_rec'] : '' ?>"> <!-- ID de la réclamation récupéré via URL ou autre méthode -->

          <!-- Note en étoiles -->
          <div class="form-group">
            <label for="note" style="color: #000000;">Notez notre service :</label>  <!-- Texte en noir -->
            <div id="note" class="star-rating">
              <i class="fa-regular fa-star" data-value="1"></i>
              <i class="fa-regular fa-star" data-value="2"></i>
              <i class="fa-regular fa-star" data-value="3"></i>
              <i class="fa-regular fa-star" data-value="4"></i>
              <i class="fa-regular fa-star" data-value="5"></i>
            </div>
            <div class="error-message" id="noteError">Veuillez sélectionner une note.</div>
          </div>

          <!-- Questions -->
          <div class="form-group">
            <label style="color: #000000;">Le processus de réclamation était-il simple ?</label><br>  <!-- Texte en noir -->
            <label><input type="radio" name="simplicite" value="oui"> Oui</label>
            <label><input type="radio" name="simplicite" value="non"> Non</label>
            <div class="error-message" id="simpliciteError">Veuillez répondre à cette question.</div>
          </div>

          <div class="form-group">
            <label style="color: #000000;">Le temps de réponse vous semble-t-il raisonnable ?</label><br>  <!-- Texte en noir -->
            <label><input type="radio" name="temps" value="rapide"> Rapide</label>
            <label><input type="radio" name="temps" value="moyen"> Moyen</label>
            <label><input type="radio" name="temps" value="lent"> Lent</label>
            <div class="error-message" id="tempsError">Veuillez répondre à cette question.</div>
          </div>

          <!-- Commentaire -->
          <div class="form-group">
            <label for="commentaire" style="color: #000000;">Un commentaire à ajouter ?</label>  <!-- Texte en noir -->
            <textarea id="commentaire" name="commentaire" rows="4" placeholder="Dites-nous ce que vous pensez du service..."></textarea>
            <div class="error-message" id="commentaireError">Veuillez entrer un commentaire.</div>
          </div>

          <div class="form-actions">
            <button type="submit" class="submit-btn">
              <i class="fas fa-paper-plane"></i> Envoyer le feedback
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
