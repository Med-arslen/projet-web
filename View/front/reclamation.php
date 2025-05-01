<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Movie Vibe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Formulaire de réclamation pour signaler un problème technique avec un film">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="script.js" defer></script>
</head>
<body>
  <div class="wrapper">
    <!-- HEADER -->
    <header class="main-header">
      <div class="menu-bar">
        <div class="netflixLogo">
          <a id="logo" href="index.html" aria-label="Retour à l'accueil">
            <img src="logo.png" alt="Movie-Vibe Logo" id="logo1">
          </a>
        </div>    

        <nav class="main-nav">
          <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="catalogue.html">Catalogue</a></li>
            <li>
              <a href="events.html">Événements <i class="fas fa-caret-down"></i></a>
            </li>
            <li><a href="achat.html">Acheter</a></li>
            <li><a href="reclamation.php" aria-current="page">Réclamation</a>
              <div class="dropdown-menu">
                <ul>
                  <li><a href="./historique.php">historique</a></li>
                </ul>
              </div>
            </li>
          </ul>
        </nav>
        
        <nav class="sub-nav">
          <a href="#" aria-label="Recherche"><i class="fas fa-search"></i></a>
          <a href="#" aria-label="Notifications"><i class="fas fa-bell"></i></a>
          <a href="compte.html">Mon Compte</a>        
        </nav>
      </div>
    </header>

    <!-- FORM SECTION -->
    <main class="form-main-container">
      <div class="form-card">
        <form id="reclamationForm" method="post" action="../backoffice/ajouter_rec.php">
          <h1 class="form-title">Formulaire de Réclamation</h1>
          <p class="form-description">Veuillez remplir ce formulaire pour nous signaler un problème technique.</p>

          <!-- Champ Nom et Prénom -->
          <div class="form-group">
            <label for="nomprenom">Nom Prenom :</label>
            <input type="text" id="nomprenom" name="nomprenom" >
          </div>

          <!-- Champ Email -->
          <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email">
          </div>
          
          <!-- Champ Titre du film -->
          <div class="form-group">
            <label for="nomfilm">Titre du film concerné :</label>
            <input type="text" id="nomfilm" name="nomfilm" >
          </div>

          <!-- Champ Type de problème -->
          <div class="form-group">
            <label for="type_rec">Type de problème :</label>
            <select id="type_rec" name="type_rec">
              <option value="" disabled selected>Sélectionnez un problème</option>
              <option value="lien">Lien cassé</option>
              <option value="qualite">Qualité mauvaise</option>
              <option value="langue">Langue audio/sous-titre incorrecte</option>
              <option value="autre">Autre</option>
            </select>
          </div>
          
          <!-- Champ Détails du problème -->
          <div class="form-group">
            <label for="detail">Détails du problème :</label>
            <textarea id="detail" name="detail" rows="5" placeholder="Décrivez le problème en détail..."></textarea>
          </div>
          
          <!-- Bouton de soumission -->
        <!-- Champ caché pour détecter soumission -->
<input type="hidden" name="submitted" value="false">

<!-- Bouton de soumission -->
<div class="form-actions">
  <button type="submit" class="submit-btn" id="submitBtn">
    <i class="fas fa-paper-plane"></i> Envoyer la réclamation
  </button>
</div>

        </form>
      </div>
    </main>
  </div>

    <!-- Redirection après enregistrement -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
      const form = document.getElementById("reclamationForm");
      const submitBtn = document.getElementById("submitBtn");

      form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Désactiver le bouton pour éviter les doubles clics
        submitBtn.disabled = true;
        submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> Envoi en cours...";

        const formData = new FormData(form);

        fetch('../backoffice/ajouter_rec.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // ✅ Redirection en cas de succès
            window.location.href = "feedback.php";
          } else {
            alert(data.message || "Erreur lors de l'envoi.");
            submitBtn.disabled = false;
            submitBtn.innerHTML = "<i class='fas fa-paper-plane'></i> Envoyer la réclamation";
          }
        })
        .catch(error => {
          console.error("Erreur:", error);
          alert("Une erreur est survenue.");
          submitBtn.disabled = false;
          submitBtn.innerHTML = "<i class='fas fa-paper-plane'></i> Envoyer la réclamation";
        });
      });
    });
  </script>
</body>
</html>

</body>
</html>
