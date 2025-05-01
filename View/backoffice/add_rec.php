<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/ReclamationController.php';
require_once __DIR__ . '/../../Model/Reclamation.php';

if (isset($_POST["submit"])) {
   
    $nomprenom   = $_POST['nomprenom'];
    $email   = $_POST['email'];
    $nomfilm = $_POST['nomfilm'];
    $type_rec  = $_POST['type_rec'];
    $detail  = $_POST['detail'];
    $reponse_rec  = $_POST['reponse_rec'];
    

   
    $id_rec = null;

 
    $reclamationn = new Reclamation(
        $nomprenom,
    $email,
    $nomfilm,
    $type_rec,
    $detail,
    $reponse_rec,
    null // id_rec
      
    );

    $ReclamationController = new ReclamationController();
    $ReclamationController->addReclamation($reclamationn);



    header("Location: index.php?msg=Reclamation created successfully");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Reclamation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../backoffice/css/style.css"/>
    
    
   
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
      <div class="logo">
        <img src="../backoffice/assets/img/logo.png" alt="Logo MovieVibe" />
        <h2>MovieVibe</h2>
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
        <a data-attr="reclamation" class="active"><i class="fa-solid fa-bell"></i> Réclamation</a>
        <a data-attr="reclamation"href="listefed.php"><i class="fa-solid fa-bell"></i>Feedback</a>
        <a data-attr="produit"><i class="fa-solid fa-cart-shopping"></i> Produits</a>
        <a data-attr="film"><i class="fa-solid fa-film"></i> Films</a>
        <button id="quitBtn"><i class="fa-solid fa-right-from-bracket"></i> Quitter</button>
      </nav>
    </aside>


<nav class="navbar navbar-expand-lg mb-5 shadow-lg rounded-3" style="background: #8B0000; border-bottom: 4px solid #d3a5a5;">
    <div class="container justify-content-center">
        <span class="navbar-brand mb-0 h1 text-white fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 3rem;">
            🎬 Add New Reclamation
        </span>
    </div>
</nav>


<div class="container">
    <div class="text-center mb-4">
        <p class="fs-2 fw-light" style="color: #ffd700; background: linear-gradient(to right, #f4a261, #e76f51); -webkit-background-clip: text; color: transparent; font-family: 'Roboto', sans-serif; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">
            Fill in the details below
        </p>
        <div style="width: 60px; height: 3px; background-color: #f4a261; margin: 10px auto;"></div>
    </div>

    <div class="container d-flex justify-content-center">
        <form action="" method="post" style="width:50vw; min-width:300px;">
            <div class="mb-3">
                <label class="form-label">NomPrenom:</label>
                <input type="text" class="form-control" name="nomprenom" >
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" name="email" >
            </div>
            <div class="mb-3">
                <label class="form-label">Nom Film</label>
                <input type="text" class="form-control" name="nomfilm" >
            </div>
            <div class="form-group">
            <label for="type_rec">Type de problème :</label>
            <select id="type_rec" name="type_rec" >
              <option value="" disabled selected>Sélectionnez un problème</option>
              <option value="lien">Lien cassé</option>
              <option value="qualite">Qualité mauvaise</option>
              <option value="langue">Langue audio/sous-titre incorrecte</option>
              <option value="autre">Autre</option>
            </select>
          </div>
          
            <div class="mb-3">
                <label class="form-label">Detail</label>
                <textarea class="form-control" name="detail" rows="3"></textarea>
            </div>
            <div>
                <label class="form-label">Reponse</label>
                <input type="text" class="form-control" name="reponse_rec" >
            </div>
                <button type="submit" class="btn btn-success" name="submit">Save</button>
                <a href="index.php" class="btn btn-danger">Cancel</a>
            </div>
          
        </form>
    </div>
</div>
<script src="add.js"></script>
</body>  
</html>