<?php
require_once __DIR__ . '/../../config/database.php';


$conn = config::getConnexion();

$error = "";

if (isset($_POST["submit"])) {

    $nomprenom = $_POST['nomprenom'];
    $email= $_POST['email'];
    $nomfilm = $_POST['nomfilm'];
    $type_rec = $_POST['type_rec'];
    $detail= $_POST['detail'];
    $reponse_rec= $_POST['reponse_rec'];
    $id_rec= $_POST['id_rec'];

    try {
        $sql = "UPDATE reclamationn
                SET nomprenom = :nomprenom, email = :email, nomfilm = :nomfilm, type_rec = :type_rec, 
                    detail= :detail , reponse_rec = :reponse_rec
                WHERE id_rec = :id_rec";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nomprenom' => $nomprenom,
            ':email' => $email,
            ':nomfilm' => $nomfilm,
            ':type_rec' => $type_rec,
            ':detail' => $detail,
            ':reponse_rec' => $reponse_rec,
        
            ':id_rec' => $id_rec  
        ]);

        header("Location: index.php?msg=Reclamation updated successfully");
        exit;
    } catch (PDOException $e) {
        $error = "Error updating reclamationn: " . $e->getMessage();
    }
}

if (!isset($_GET["id"])) {
    echo "reclamationn ID is missing in the URL!";
    exit;
}

$id = $_GET["id"];

try {
    $stmt = $conn->prepare("SELECT * FROM reclamationn WHERE id_rec = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "Reclamation not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error fetching event: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Réclamation</title>
   
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../../assets/img/logo.png" type="image/png">
</head>
<body style="background: url('../../assets/img/cinema.jpg') no-repeat center center fixed; background-size: cover;">

<nav class="navbar navbar-expand-lg mb-5 shadow-lg rounded-3" style="background: #8B0000; border-bottom: 4px solid #d3a5a5;">
  <div class="container justify-content-center">
    <span class="navbar-brand mb-0 h1 text-white fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 3rem;">
      📝 Modifier Réclamation
    </span>
  </div>
</nav>

<div class="container">
    <div class="text-center mb-4">
       <p class="fs-2 fw-light" style="color: #ffd700; background: linear-gradient(to right, #f4a261, #e76f51); -webkit-background-clip: text; color: transparent;">
          Mettez à jour les informations puis cliquez sur "Sauvegarder"
       </p>
       <div style="width: 60px; height: 3px; background-color: #f4a261; margin: 10px auto;"></div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="container d-flex justify-content-center">
        <form action="" method="post" style="width:50vw; min-width:300px;">
        </div>
            <input type="hidden" name="id_rec" value="<?= $id ?>">

            <div>
            <div class="mb-3">
                <label class="form-label">Nom et prénom :</label>
                <input type="text" class="form-control" name="nomprenom" value="<?= htmlspecialchars($row['nomprenom']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email :</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($row['email']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nom du film :</label>
                <input type="text" class="form-control" name="nomfilm" value="<?= htmlspecialchars($row['nomfilm']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Type de réclamation :</label>
                <select class="form-select" name="type_rec" >
                    <option value="Lien" <?= $row['type_rec'] == 'Lien' ? 'selected' : '' ?>>Lien cassé</option>
                    <option value="Qualité" <?= $row['type_rec'] == 'Qualité' ? 'selected' : '' ?>>Qualité mauvaise</option>
                    <option value="Langue" <?= $row['type_rec'] == 'Langue' ? 'selected' : '' ?>>Langue audio/sous-titre incorrecte</option>
                    <option value="Autre" <?= $row['type_rec'] == 'Autre' ? 'selected' : '' ?>>Autre</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Détails :</label>
                <textarea class="form-control" name="detail" rows="3"><?= htmlspecialchars($row['detail']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Reponse:</label>
                <input type="text" class="form-control" name="reponse_rec" value="<?= htmlspecialchars($row['reponse_rec']) ?>">
            </div>

            <div>
                <button type="submit" class="btn btn-success" name="submit">Save</button>
                <a href="index.php" class="btn btn-danger">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>