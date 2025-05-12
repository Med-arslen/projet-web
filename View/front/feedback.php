<?php
// Connexion à la base de données
require_once '../../config/database.php';
require_once '../../controller/feedbackController.php';
require_once '../../Model/feedback.php';

// Déterminer la langue
$lang = $_GET['lang'] ?? 'fr';

// Vérification de l'ID de réclamation
$id_rec = $_GET['id_rec'] ?? null;

if ($id_rec === null) {
    die("Erreur : ID de réclamation manquant");
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        $note = $_POST['note'] ?? null;
        $simplicite = $_POST['simplicite'] ?? null;
        $temps = $_POST['temps'] ?? null;
        $commentaire = $_POST['commentaire'] ?? '';
        $id_rec = $_POST['id_rec'] ?? null;

        if (!$note || !$simplicite || !$temps || !$id_rec) {
            throw new Exception('Tous les champs sont requis');
        }

        $id_fed = uniqid('fed_', true);
        // Correction de l'ordre des paramètres : l'analyse est le commentaire, le conseil est vide
        $feedbackk = new feedback($id_fed, (int)$id_rec, '', $commentaire, $simplicite, $temps);
        $feedbackController = new feedbackController();
        $feedbackController->addFeedback($feedbackk);

        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
  <meta charset="UTF-8">
  <title>Movie Vibe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="logo.png" type="image/png">
  <meta name="description" content="Formulaire de réclamation pour signaler un problème technique avec un film">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    /* Reset et styles de base */
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #111;
      color: #fff;
      padding-top: 80px;
    }

    /* Header et Navigation */
    .main-header {
      position: fixed;
      top: 0;
      width: 100%;
      background-color: #111;
      z-index: 1000;
      padding: 10px 0;
      border-bottom: 1px solid #333;
    }

    .menu-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .netflixLogo {
      display: flex;
      align-items: center;
    }

    .netflixLogo img {
      width: 40px;
      margin-right: 10px;
    }

    ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
    }

    ul li {
      margin-left: 20px;
    }

    ul li a {
      text-decoration: none;
      color: #fff;
      font-size: 16px;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    ul li a:hover {
      color: #f5f5f5;
    }

    .reclamation {
      position: relative;
    }

    .dropdown-menu {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background-color: #222;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
      border-radius: 5px;
      padding: 10px;
      z-index: 100;
      min-width: 150px;
    }

    .dropdown-menu a {
      display: block;
      padding: 8px 12px;
      color: #fff;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .dropdown-menu a:hover {
      background-color: #333;
    }

    .lang-selector {
      margin-left: 20px;
    }

    .lang-selector select {
      background-color: #333;
      color: #fff;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      font-size: 14px;
      cursor: pointer;
    }

    .form-main-container {
      max-width: 900px;
      margin: 40px auto;
      background-color: #222;
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

    .form-description {
      font-size: 16px;
      color: #f5f5f5;
      margin-bottom: 20px;
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

    .star-rating {
      display: flex;
    }

    .star-rating i {
      font-size: 24px;
      cursor: pointer;
      color: #ccc;
    }

    .star-rating i.selected {
      color: gold;
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

    .form-group.error input[type="radio"],
    .form-group.error .star-rating i {
      border-color: #ff4444;
    }

    .form-group.error label {
      color: #ff4444;
    }

    /* Style pour mettre en évidence les champs en erreur */
    .star-rating.error i {
      color: #ff4444;
    }

    .radio-group.error {
      border: 1px solid #ff4444;
      padding: 10px;
      border-radius: 5px;
    }
    .error-message {
      color: #ff4444;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      margin-bottom: 0.5rem;
      display: none;
    }

    .form-group.error input,
    .form-group.error textarea,
    .form-group.error select {
      border-color: #ff4444;
      background-color: #fff1f1;
    }

    .form-group.error .radio-group {
      border: 1px solid #ff4444;
      padding: 10px;
      border-radius: 5px;
      background-color: #fff1f1;
    }

    .form-group.error label {
      color: #ff4444;
    }

    .star-rating.error {
      padding: 10px;
      border: 1px solid #ff4444;
      border-radius: 5px;
      background-color: #fff1f1;
    }

    .star-rating.error i {
      color: #ff9999;
    }
  </style>
</head>
<body>
  <header class="main-header">
    <nav class="menu-bar">
      <div class="netflixLogo">
        <a id="logo" href="#home">
          <img src="logo.png" alt="Logo Movie Vibe" id="logo1">
        </a>
      </div>    
      <ul>
        <li><a href="page.html"><?= feedbackController::traduction_form('Accueil', $lang) ?></a></li>
        <li><a href="../page.php"><?= feedbackController::traduction_form('Boutique', $lang) ?></a></li>
        <li><a href="event.html"><?= feedbackController::traduction_form('Evénement', $lang) ?></a></li>
        <li class="reclamation">
          <a href="#" onclick="event.preventDefault(); toggleDropdown();">
            <?= feedbackController::traduction_form('Réclamation', $lang) ?>
            <span class="arrow">▼</span>
          </a>
          <div class="dropdown-menu" id="dropdown-menu">
            <a href="historique.php"><?= feedbackController::traduction_form('Historique', $lang) ?></a>
          </div>
        </li>
      </ul>
      <div class="lang-selector">
        <form method="GET">
          <input type="hidden" name="id_rec" value="<?= isset($_GET['id_rec']) ? htmlspecialchars($_GET['id_rec']) : '' ?>">
          <select name="lang" onchange="this.form.submit()">
            <option value="fr" <?= $lang === 'fr' ? 'selected' : '' ?>>Français</option>
            <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
            <option value="es" <?= $lang === 'es' ? 'selected' : '' ?>>Español</option>
            <option value="ar" <?= $lang === 'ar' ? 'selected' : '' ?>>العربية</option>
          </select>
        </form>
      </div>
    </nav>
  </header>

<main class="form-main-container">
  <div class="form-card">
    <h1 class="form-title"><?= feedbackController::traduction_form('Merci Pour Votre Réclamation !', $lang) ?></h1>
    <br>
    <p class="form-description"><?= feedbackController::traduction_form('Votre demande a bien été reçue. Aidez-nous à améliorer notre service en répondant à ce court sondage :', $lang) ?></p>

    <form id="feedbackForm" method="POST" action="" novalidate>
      <input type="hidden" name="id_rec" value="<?= isset($_GET['id_rec']) ? htmlspecialchars($_GET['id_rec']) : '' ?>">

      <div class="form-group">
        <label for="note"><?= feedbackController::traduction_form('Notez notre service :', $lang) ?></label>
        <br><br>
        <div id="note" class="star-rating">
          <i class="fa-regular fa-star" data-value="1"></i>
          <i class="fa-regular fa-star" data-value="2"></i>
          <i class="fa-regular fa-star" data-value="3"></i>
          <i class="fa-regular fa-star" data-value="4"></i>
          <i class="fa-regular fa-star" data-value="5"></i>
        </div>
        <input type="hidden" name="note" value=""/>
        <div id="noteError" class="error-message"></div>
      </div>

      <div class="form-group">
        <label><?= feedbackController::traduction_form('Le processus de réclamation était-il simple ?', $lang) ?></label>
        <div class="radio-group">
          <label><input type="radio" name="simplicite" value="oui"> <?= feedbackController::traduction_form('Oui', $lang) ?></label>
          <label><input type="radio" name="simplicite" value="non"> <?= feedbackController::traduction_form('Non', $lang) ?></label>
        </div>
        <div id="simpliciteError" class="error-message"></div>
      </div>

      <div class="form-group">
        <label><?= feedbackController::traduction_form('Le temps de réponse vous semble-t-il raisonnable ?', $lang) ?></label>
        <div class="radio-group">
          <label><input type="radio" name="temps" value="rapide"> <?= feedbackController::traduction_form('Rapide', $lang) ?></label>
          <label><input type="radio" name="temps" value="moyen"> <?= feedbackController::traduction_form('Moyen', $lang) ?></label>
          <label><input type="radio" name="temps" value="lent"> <?= feedbackController::traduction_form('Lent', $lang) ?></label>
        </div>
        <div id="tempsError" class="error-message"></div>
      </div>

      <div class="form-group">
        <label for="commentaire"><?= feedbackController::traduction_form('Un commentaire à ajouter ?', $lang) ?></label>
        <textarea id="commentaire" name="commentaire" rows="4" placeholder="<?= feedbackController::traduction_form('Dites-nous ce que vous pensez du service...', $lang) ?>"></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="submit-btn">
          <i class="fas fa-paper-plane"></i> <?= feedbackController::traduction_form('Envoyer le feedback', $lang) ?>
        </button>
      </div>
    </form>
  </div>
</main>
<footer>
    &copy; 2025 Movie Vibe. <a href="#"><?= feedbackController::traduction_form('Mentions légales', $lang) ?></a>
  </footer>
</body>
</html>
<script>
  document.addEventListener("DOMContentLoaded", function() {
      const form = document.getElementById("feedbackForm");
      const stars = document.querySelectorAll('.star-rating i');
      const noteInput = document.querySelector('input[name="note"]');
      
      // Gestion des étoiles
      stars.forEach(star => {
        star.addEventListener('click', function() {
          const value = this.dataset.value;
          noteInput.value = value;
          stars.forEach(s => {
            if (s.dataset.value <= value) {
              s.classList.remove('fa-regular');
              s.classList.add('fa-solid');
              s.classList.add('selected');
            } else {
              s.classList.add('fa-regular');
              s.classList.remove('fa-solid');
              s.classList.remove('selected');
            }
          });
          hideError('note');
        });
      });

      // Gestionnaires d'événements pour cacher les erreurs lors de la saisie
      document.querySelectorAll('input[name="simplicite"]').forEach(radio => {
        radio.addEventListener('change', () => hideError('simplicite'));
      });

      document.querySelectorAll('input[name="temps"]').forEach(radio => {
        radio.addEventListener('change', () => hideError('temps'));
      });

      // Fonction pour afficher une erreur
      function showError(fieldId, message) {
        const errorDiv = document.getElementById(`${fieldId}Error`);
        const group = document.querySelector(`.form-group:has([name="${fieldId}"])`);
        if (errorDiv) {
          errorDiv.textContent = message;
          errorDiv.style.display = 'block';
        }
        if (group) {
          group.classList.add('error');
        }
        if (fieldId === 'note') {
          document.querySelector('.star-rating').classList.add('error');
        }
      }

      // Fonction pour cacher une erreur
      function hideError(fieldId) {
        const errorDiv = document.getElementById(`${fieldId}Error`);
        const group = document.querySelector(`.form-group:has([name="${fieldId}"])`);
        if (errorDiv) {
          errorDiv.style.display = 'none';
        }
        if (group) {
          group.classList.remove('error');
        }
        if (fieldId === 'note') {
          document.querySelector('.star-rating').classList.remove('error');
        }
      }

      // Validation du formulaire
      form.addEventListener("submit", function(e) {
        e.preventDefault();
        let hasErrors = false;

        // Réinitialiser les erreurs
        document.querySelectorAll('.error-message').forEach(div => {
          div.style.display = 'none';
        });
        document.querySelectorAll('.form-group').forEach(group => {
          group.classList.remove('error');
        });

        // Validation de la note
        if (!noteInput.value) {
          showError('note', '<?= feedbackController::traduction_form("Veuillez sélectionner une note", $lang) ?>');
          hasErrors = true;
        }

        // Validation de la simplicité
        if (!form.querySelector('input[name="simplicite"]:checked')) {
          showError('simplicite', '<?= feedbackController::traduction_form("Veuillez répondre à cette question", $lang) ?>');
          hasErrors = true;
        }

        // Validation du temps
        if (!form.querySelector('input[name="temps"]:checked')) {
          showError('temps', '<?= feedbackController::traduction_form("Veuillez répondre à cette question", $lang) ?>');
          hasErrors = true;
        }

        // Validation du commentaire
        const commentaire = form.querySelector('#commentaire').value.trim();
        if (!commentaire) {
          showError('commentaire', '<?= feedbackController::traduction_form("Veuillez ajouter un commentaire", $lang) ?>');
          hasErrors = true;
        }

        if (!hasErrors) {
          // Soumettre le formulaire
          const formData = new FormData(form);
          const submitBtn = form.querySelector('.submit-btn');
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= feedbackController::traduction_form("Envoi en cours...", $lang) ?>';

          fetch(window.location.href, {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if (!response.ok) {
              return response.json().then(data => {
                throw new Error(data.error || 'Une erreur est survenue');
              });
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              // Utiliser l'URL absolue pour la redirection
              window.location.href = window.location.origin + window.location.pathname.replace('feedback.php', 'merci.php');
            } else {
              throw new Error(data.error || '<?= feedbackController::traduction_form("Une erreur est survenue lors de l\'enregistrement", $lang) ?>');
            }
          })
          .catch(error => {
            console.error('Erreur:', error);
            alert('<?= feedbackController::traduction_form("Une erreur est survenue", $lang) ?>');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <?= feedbackController::traduction_form("Envoyer le feedback", $lang) ?>';
          });
        } else {
          const formError = document.getElementById('form-error');
          if (formError) {
            formError.textContent = '<?= feedbackController::traduction_form("Veuillez remplir tous les champs", $lang) ?>';
            formError.style.display = 'block';
          }
        }
      });
    });

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
  </script>

