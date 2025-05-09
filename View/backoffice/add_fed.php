<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/feedbackController.php';
require_once __DIR__ . '/../../Model/feedback.php';

$id_rec = isset($_GET['id_rec']) ? (int)$_GET['id_rec'] : null; // Récupère l'ID de réclamation de l'URL

if (isset($_POST["submit"])) {
    $analyse = $_POST['analyse'] ?? '';
    $conseil = $_POST['conseil'] ?? '';
    $simplicite = $_POST['simplicite'] ?? ''; // Récupération du champ Simplicité
    $temps = $_POST['temps'] ?? ''; // Récupération du champ Temps
    $id_fed = uniqid('fed_', true); // identifiant string unique
    $id_rec = $_POST['id_rec'] ?? null; // Récupère l'id de la réclamation passé dans le formulaire

    if ($id_rec) {
        // Utilisation de 'feedback' en minuscule
        $feedbackk = new feedback($id_fed, (int)$id_rec, $analyse, $conseil, $simplicite, $temps); // Ajout des nouveaux paramètres
        $feedbackController = new feedbackController();
        $feedbackController->addFeedback($feedbackk);

        header("Location: listefed.php?msg=Feedback ajouté avec succès");
        exit;
    } else {
        echo "L'ID de réclamation est manquant.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MovieVibe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="../backoffice/assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../backoffice/css/style.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    form.addEventListener('submit', function (e) {
        const id_rec = form.id_rec.value.trim();
        const analyse = form.analyse.value.trim();
        const conseil = form.conseil.value.trim();
        const simplicite = form.simplicite.value;
        const temps = form.temps.value;

        let isValid = true;

        // Réinitialisation des messages d’erreurs et des styles
        document.getElementById('error-id_rec').textContent = '';
        document.getElementById('error-analyse').textContent = '';
        document.getElementById('error-conseil').textContent = '';
        document.getElementById('error-simplicite').textContent = '';
        document.getElementById('error-temps').textContent = '';

        // Réinitialiser les bordures des champs
        document.querySelector('input[name="id_rec"]').style.borderColor = '';
        document.querySelector('textarea[name="analyse"]').style.borderColor = '';
        document.querySelector('textarea[name="conseil"]').style.borderColor = '';
        document.querySelector('select[name="simplicite"]').style.borderColor = '';
        document.querySelector('select[name="temps"]').style.borderColor = '';

        // Validation
        if (!/^\d+$/.test(id_rec)) {
            isValid = false;
            document.getElementById('error-id_rec').textContent = "❌ L'ID doit être un nombre entier.";
            document.querySelector('input[name="id_rec"]').style.borderColor = 'red';
        }

        if (analyse.length < 5) {
            isValid = false;
            document.getElementById('error-analyse').textContent = "❌ L'analyse doit contenir au moins 5 caractères.";
            document.querySelector('textarea[name="analyse"]').style.borderColor = 'red';
        }

        if (conseil.length < 5) {
            isValid = false;
            document.getElementById('error-conseil').textContent = "❌ Le conseil doit contenir au moins 5 caractères.";
            document.querySelector('textarea[name="conseil"]').style.borderColor = 'red';
        }

        if (!["oui", "non"].includes(simplicite)) {
            isValid = false;
            document.getElementById('error-simplicite').textContent = "❌ Sélection invalide pour la simplicité.";
            document.querySelector('select[name="simplicite"]').style.borderColor = 'red';
        }

        if (!["rapide", "moyen", "lent"].includes(temps)) {
            isValid = false;
            document.getElementById('error-temps').textContent = "❌ Sélection invalide pour le temps de réponse.";
            document.querySelector('select[name="temps"]').style.borderColor = 'red';
        }

        if (!isValid) {
            e.preventDefault(); // empêche l’envoi du formulaire
        }
    });
});
</script>

<body>
<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">
            <img src="../backoffice/assets/img/logo.png" alt="Logo MovieVibe" />
            
        </div>
        <nav class="menu">
            <a data-attr="client"><i class="fa-solid fa-user"></i> Client</a>
            <a data-attr="event" class="has-submenu" onclick="toggleSubMenu(this)">
                <i class="fa-solid fa-calendar"></i> Événements
                <i class="fa-solid fa-chevron-down submenu-icon"></i>
            </a>
            <div class="submenu" style="display: none;">
                <a data-attr="listeReservation"><i class="fa-solid fa-list"></i> Liste des Réservations</a>
                <a id="newEventBtn" href="#"><i class="fa-solid fa-plus-circle"></i> Nouveaux Événements</a>
            </div>
            <a data-attr="reclamation"  href="index.php"><i class="fa-solid fa-bell"></i> Réclamation</a>
            <a data-attr="reclamation" class="active" ><i class="fa-solid fa-bell"></i>Feedback</a>
            <a data-attr="produit"><i class="fa-solid fa-cart-shopping"></i> Produits</a>
            <a data-attr="film"><i class="fa-solid fa-film"></i> Films</a>
            <button id="quitBtn"><i class="fa-solid fa-right-from-bracket"></i> Quitter</button>
        </nav>
    </aside>

    <nav class="navbar navbar-expand-lg mb-5 shadow-lg rounded-3" style="background: #8B0000; border-bottom: 4px solid #d3a5a5;">
        <div class="container justify-content-center">
            <span class="navbar-brand mb-0 h1 text-white fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 3rem;">
                🎬 Ajouter un Feedback
            </span>
        </div>
    </nav>

    <div class="container">
        <div class="text-center mb-4">
            <p class="fs-2 fw-light" style="color: #ffd700; background: linear-gradient(to right, #f4a261, #e76f51); -webkit-background-clip: text; color: transparent; font-family: 'Roboto', sans-serif; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">
                Remplissez les informations ci-dessous
            </p>
            <div style="width: 60px; height: 3px; background-color: #f4a261; margin: 10px auto;"></div>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                
                <!-- ID de la Réclamation -->
                <div class="mb-3">
                    <label class="form-label">ID de la Réclamation</label>
                    <input type="text" class="form-control" name="id_rec" value="<?php echo htmlspecialchars($id_rec ?? '', ENT_QUOTES); ?>">
                    <div class="text-danger" id="error-id_rec"></div>
                </div>

                <!-- Analyse -->
                <div class="mb-3">
                    <label class="form-label">Analyse du Feedback</label>
                    <textarea class="form-control" name="analyse" rows="3"></textarea>
                    <div class="text-danger" id="error-analyse"></div>
                </div>

                <!-- Conseil -->
                <div class="mb-3">
                    <label class="form-label">Conseil du Feedback</label>
                    <textarea class="form-control" name="conseil" rows="3"></textarea>
                    <div class="text-danger" id="error-conseil"></div>
                </div>

                <!-- Simplicité -->
                <div class="mb-3">
                    <label class="form-label">Le processus de réclamation était-il simple ?</label>
                    <select class="form-control" name="simplicite">
                        <option value="">-- Choisissez --</option>
                        <option value="oui">Oui</option>
                        <option value="non">Non</option>
                    </select>
                    <div class="text-danger" id="error-simplicite"></div>
                </div>

                <!-- Temps -->
                <div class="mb-3">
                    <label class="form-label">Le temps de réponse vous semble-t-il raisonnable ?</label>
                    <select class="form-control" name="temps">
                        <option value="">-- Choisissez --</option>
                        <option value="rapide">Rapide</option>
                        <option value="moyen">Moyen</option>
                        <option value="lent">Lent</option>
                    </select>
                    <div class="text-danger" id="error-temps"></div>
                </div>

                <button type="submit" class="btn btn-success" name="submit">Enregistrer</button>
                <a href="listefed.php" class="btn btn-danger">Annuler</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
