<?php
include_once '../../controller/ReclamationController.php'; 
include_once '../../config/database.php'; 
include_once '../../Model/Reclamation.php';

$lang = $_GET['lang'] ?? 'fr';
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8">
  <title>Movie Vibe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    /* Styles CSS */
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #111; /* Fond noir */
      color: #fff; /* Texte en blanc pour contraster avec le fond noir */
    }

    .main-header {
      background-color: #111;
      padding: 10px 0;
    }

    .menu-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
    }

    .netflixLogo img {
      width: 150px;
    }

    ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
    }

    ul li {
      margin-left: 20px;
    }

    ul li a {
      text-decoration: none;
      color: #fff;
      font-size: 16px;
      font-weight: 600;
    }

    ul li a:hover {
      color: #f5f5f5;
    }

    .lang-selector form select {
      background-color: #333;
      color: #fff;
      border: none;
      padding: 5px 10px;
      font-size: 14px;
    }

    .reclamation {
      position: relative;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      top: 30px;
      left: 0;
      background-color: #fff;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
      border-radius: 5px;
      padding: 10px;
      z-index: 100;
    }

    .dropdown-menu a {
      display: block;
      text-decoration: none;
      color: #333;
      margin: 5px 0;
    }

    .dropdown-menu a:hover {
      color: #f5f5f5;
      background-color: #111;
    }

    .form-main-container {
      max-width: 900px;
      margin: 40px auto;
      background-color: #222; /* Fond gris foncé pour le formulaire */
      padding: 30px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    .form-card {
      padding: 20px;
    }

    .form-title {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      font-size: 16px;
      font-weight: 500;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-top: 5px;
    }

    .form-group textarea {
      resize: vertical;
    }

    .form-actions {
      text-align: center;
    }

    .submit-btn {
      background-color: #111;
      color: #fff;
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .submit-btn:hover {
      background-color: #333;
    }

    footer {
      text-align: center;
      padding: 20px;
      background-color: #111;
      color: #fff;
      font-size: 14px;
    }

    footer a {
      color: #f5f5f5;
      text-decoration: none;
    }

    footer a:hover {
      color: #fff;
    }

    .error-message {
      color: #ff4444;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      display: none;
    }

    .form-group input.error,
    .form-group select.error,
    .form-group textarea.error {
      border-color: #ff4444;
    }
  </style>

  <script>
    function toggleDropdown() {
      const menu = document.getElementById('dropdown-menu');
      menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }

    window.addEventListener('click', function(e) {
      const target = e.target;
      if (!target.closest('.reclamation')) {
        const menu = document.getElementById('dropdown-menu');
        if (menu) menu.style.display = 'none';
      }
    });

    document.addEventListener("DOMContentLoaded", function () {
      const form = document.getElementById("reclamationForm");
      const submitBtn = document.getElementById("submitBtn");

      // Fonction pour afficher une erreur
      function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(`${fieldId}-error`);
        if (field && errorDiv) {
          field.classList.add('error');
          errorDiv.textContent = message;
          errorDiv.style.display = 'block';
        }
      }

      // Fonction pour cacher une erreur
      function hideError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(`${fieldId}-error`);
        if (field && errorDiv) {
          field.classList.remove('error');
          errorDiv.style.display = 'none';
        }
      }

      // Fonction pour valider un email
      function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      }

      // Validation en temps réel
      ['nomprenom', 'email', 'nomfilm', 'type_rec', 'detail'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
          field.addEventListener('input', function() {
            hideError(fieldId);
          });
        }
      });

      if (form) {
        form.addEventListener("submit", function (e) {
          e.preventDefault();
          let hasErrors = false;

          // Réinitialiser tous les messages d'erreur
          document.querySelectorAll('.error-message').forEach(div => {
            div.style.display = 'none';
          });
          document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(field => {
            field.classList.remove('error');
          });

          // Validation des champs
          const formData = new FormData(form);

          if (!formData.get('nomprenom')) {
            showError('nomprenom', '<?= ReclamationController::traduction_form("Le nom et prénom sont requis", $lang) ?>');
            hasErrors = true;
          }

          const email = formData.get('email');
          if (!email) {
            showError('email', '<?= ReclamationController::traduction_form("L\'email est requis", $lang) ?>');
            hasErrors = true;
          } else if (!isValidEmail(email)) {
            showError('email', '<?= ReclamationController::traduction_form("Format d\'email invalide", $lang) ?>');
            hasErrors = true;
          }

          if (!formData.get('nomfilm')) {
            showError('nomfilm', '<?= ReclamationController::traduction_form("Le nom du film est requis", $lang) ?>');
            hasErrors = true;
          }

          if (!formData.get('type_rec')) {
            showError('type_rec', '<?= ReclamationController::traduction_form("Le type de réclamation est requis", $lang) ?>');
            hasErrors = true;
          }

          if (!formData.get('detail')) {
            showError('detail', '<?= ReclamationController::traduction_form("Les détails sont requis", $lang) ?>');
            hasErrors = true;
          }

          if (hasErrors) {
            return;
          }

          // Si pas d'erreurs, soumettre le formulaire
          submitBtn.disabled = true;
          submitBtn.innerHTML = "<i class='fas fa-spinner fa-spin'></i> <?= ReclamationController::traduction_form('Envoi en cours...', $lang) ?>";

          fetch('ajouter_rec_front.php', {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if (!response.ok) {
              throw new Error('Erreur réseau');
            }
            return response.json();
          })
          .then(data => {
            if (data.success && data.data && data.data.id_rec) {
              window.location.href = `feedback.php?id_rec=${data.data.id_rec}`;
            } else {
              throw new Error(data.message || "Une erreur est survenue lors de l'enregistrement.");
            }
          })
          .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('form-error').textContent = error.message || "Une erreur inattendue s'est produite";
            document.getElementById('form-error').style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.innerHTML = "<?= ReclamationController::traduction_form('Envoyer', $lang) ?>";
          });
        });
      }
    });
  </script>
</head>

<body>
  <header class="main-header">
    <nav class="menu-bar">
      <div class="netflixLogo">
        <a id="logo" href="#home"><img src="logo.png" alt="Logo Movie Vibe" id="logo1"></a>
      </div>      <ul>
  <li><a href="page.html"><?= ReclamationController::traduction_form('Accueil', $lang) ?></a></li>
  <li><a href="../page.php"><?= ReclamationController::traduction_form('Boutique', $lang) ?></a></li>
  <li><a href="event.html"><?= ReclamationController::traduction_form('Evénement', $lang) ?></a></li>
  <li class="reclamation">
    <a href="#" onclick="event.preventDefault(); toggleDropdown();">
      <?= ReclamationController::traduction_form('Réclamation', $lang) ?>
      <span class="arrow">▼</span>
    </a>
    <div class="dropdown-menu" id="dropdown-menu">
      <a href="historique.php"><?= ReclamationController::traduction_form('Historique', $lang) ?></a>
    </div>
  </li>
</ul>

      <div class="lang-selector">
        <form method="get" action="">
          <select name="lang" onchange="this.form.submit()">
            <option value="fr" <?= $lang == 'fr' ? 'selected' : '' ?>>Français</option>
            <option value="en" <?= $lang == 'en' ? 'selected' : '' ?>>English</option>
            <option value="es" <?= $lang == 'es' ? 'selected' : '' ?>>Español</option>
            <option value="ar" <?= $lang == 'ar' ? 'selected' : '' ?>>العربية</option>
          </select>
        </form>
      </div>
    </nav>
  </header>

  <main class="form-main-container">
    <div class="form-card">
      <form id="reclamationForm" method="post" action="ajouter_rec_front.php">
        <h1 class="form-title"><?= ReclamationController::traduction_form('Formulaire de Réclamation', $lang) ?></h1>
        <p><?= ReclamationController::traduction_form('Veuillez remplir ce formulaire pour signaler un problème.', $lang) ?></p>

        <div id="form-error" class="error-message" style="margin-bottom: 1rem;"></div>

        <div class="form-group">
          <label for="nomprenom"><?= ReclamationController::traduction_form('Nom Prénom :', $lang) ?></label>
          <input type="text" id="nomprenom" name="nomprenom">
          <div id="nomprenom-error" class="error-message"></div>
        </div>

        <div class="form-group">
          <label for="email"><?= ReclamationController::traduction_form('Email :', $lang) ?></label>
          <input type="email" id="email" name="email">
          <div id="email-error" class="error-message"></div>
        </div>

        <div class="form-group">
          <label for="nomfilm"><?= ReclamationController::traduction_form('Titre du film concerné :', $lang) ?></label>
          <input type="text" id="nomfilm" name="nomfilm">
          <div id="nomfilm-error" class="error-message"></div>
        </div>

        <div class="form-group">
          <label for="type_rec"><?= ReclamationController::traduction_form('Type de problème :', $lang) ?></label>
          <select id="type_rec" name="type_rec">
            <option value="" disabled selected><?= ReclamationController::traduction_form('Sélectionnez un problème', $lang) ?></option>
            <option value="lien"><?= ReclamationController::traduction_form('Lien cassé', $lang) ?></option>
            <option value="qualite"><?= ReclamationController::traduction_form('Qualité mauvaise', $lang) ?></option>
            <option value="langue"><?= ReclamationController::traduction_form('Langue audio/sous-titre incorrecte', $lang) ?></option>
            <option value="autre"><?= ReclamationController::traduction_form('Autre', $lang) ?></option>
          </select>
          <div id="type_rec-error" class="error-message"></div>
        </div>

        <div class="form-group">
          <label for="detail"><?= ReclamationController::traduction_form('Détails du problème :', $lang) ?></label>
          <textarea id="detail" name="detail" rows="4" placeholder="<?= ReclamationController::traduction_form('Décrivez le problème...', $lang) ?>"></textarea>
          <div id="detail-error" class="error-message"></div>
        </div>

        <input type="hidden" name="submitted" value="true">

        <div class="form-actions">
          <button type="submit" id="submitBtn" class="submit-btn"><?= ReclamationController::traduction_form('Envoyer', $lang) ?></button>
        </div>
      </form>
    </div>
  </main>

  <footer>
    &copy; 2025 Movie Vibe. <a href="#">Mentions légales</a>.
  </footer>
</body>
</html>
