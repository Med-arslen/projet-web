<?php
require_once __DIR__ . '/../../config/database.php';

$conn = config::getConnexion();
$error = "";

if (isset($_POST["submit"])) {
    $nomprenom = $_POST['nomprenom'];
    $email = $_POST['email'];
    $nomfilm = $_POST['nomfilm'];
    $type_rec = $_POST['type_rec'];
    $detail = $_POST['detail'];
    $reponse_rec = $_POST['reponse_rec'];
    $analyse = $_POST['analyse'];
    $conseil = $_POST['conseil'];
    $simplicite = $_POST['simplicite'];
    $temps = $_POST['temps'];
    $id_rec = $_POST['id_rec'];

    try {
        // Mise à jour de la réclamation
        $sql1 = "UPDATE reclamationn
                 SET nomprenom = :nomprenom, email = :email, nomfilm = :nomfilm, 
                     type_rec = :type_rec, detail = :detail, reponse_rec = :reponse_rec
                 WHERE id_rec = :id_rec";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([
            ':nomprenom' => $nomprenom,
            ':email' => $email,
            ':nomfilm' => $nomfilm,
            ':type_rec' => $type_rec,
            ':detail' => $detail,
            ':reponse_rec' => $reponse_rec,
            ':id_rec' => $id_rec
        ]);

        // Mise à jour ou insertion du feedback
        $sql2 = "INSERT INTO feedbackk (id_rec, analyse, conseil, simplicite, temps)
                 VALUES (:id_rec, :analyse, :conseil, :simplicite, :temps)
                 ON DUPLICATE KEY UPDATE analyse = :analyse, conseil = :conseil, simplicite = :simplicite, temps = :temps";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([
            ':id_rec' => $id_rec,
            ':analyse' => $analyse,
            ':conseil' => $conseil,
            ':simplicite' => $simplicite,
            ':temps' => $temps
        ]);

        header("Location: listefed.php?msg=Réclamation et feedback mis à jour avec succès");
        exit;
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}

// Chargement des données existantes
if (!isset($_GET["id"])) {
    echo "ID de la réclamation manquant.";
    exit;
}

$id = $_GET["id"];

try {
    $stmt = $conn->prepare("
        SELECT r.*, f.id_fed, f.analyse, f.conseil, f.simplicite, f.temps
        FROM reclamationn r
        LEFT JOIN feedbackk f ON r.id_rec = f.id_rec
        WHERE r.id_rec = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "Réclamation introuvable.";
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../backoffice/css/feedback.css"/>
    <link href="../../public/css/add.css" rel="stylesheet">
</head>
<body style="background: url('../../assets/img/cinema.jpg') no-repeat center center fixed; background-size: cover;">
    <!-- Sidebar -->
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
                <a data-attr="index.php" class="active"><i class="fa-solid fa-bell"></i> Réclamation</a>
                <a data-attr="listefed.php" class="active"><i class="fa-solid fa-bell"></i> Feedback</a>
                <a data-attr="produit"><i class="fa-solid fa-cart-shopping"></i> Produits</a>
                <a data-attr="film"><i class="fa-solid fa-film"></i> Films</a>
                <button id="quitBtn"><i class="fa-solid fa-right-from-bracket"></i> Quitter</button>
            </nav>
        </aside>
    </div>

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
                <input type="hidden" name="id_rec" value="<?= htmlspecialchars($id) ?>">

                <div class="mb-3">
                    <label class="form-label">Nom & Prénom :</label>
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
                    <input type="text" class="form-control" name="type_rec" value="<?= htmlspecialchars($row['type_rec']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Détails :</label>
                    <textarea class="form-control" name="detail"><?= htmlspecialchars($row['detail']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Réponse :</label>
                    <textarea class="form-control" name="reponse_rec"><?= htmlspecialchars($row['reponse_rec']) ?></textarea>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <label class="form-label">ID Feedback :</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($row['id_fed'] ?? '') ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Analyse Feedback :</label>
                    <input type="text" class="form-control" name="analyse" value="<?= htmlspecialchars($row['analyse']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Conseil Feedback :</label>
                    <input type="text" class="form-control" name="conseil" value="<?= htmlspecialchars($row['conseil']) ?>">
                </div>

                <!-- Ajout des champs 'simplicite' et 'temps' -->
                <div class="mb-3">
                    <label class="form-label">Simplicité :</label>
                    <select class="form-control" name="simplicite">
                        <option value="oui" <?= $row['simplicite'] === 'oui' ? 'selected' : '' ?>>Oui</option>
                        <option value="non" <?= $row['simplicite'] === 'non' ? 'selected' : '' ?>>Non</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Temps :</label>
                    <select class="form-control" name="temps">
                        <option value="rapide" <?= $row['temps'] === 'rapide' ? 'selected' : '' ?>>Rapide</option>
                        <option value="moyen" <?= $row['temps'] === 'moyen' ? 'selected' : '' ?>>Moyen</option>
                        <option value="lent" <?= $row['temps'] === 'lent' ? 'selected' : '' ?>>Lent</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success" name="submit">Sauvegarder</button>
                    <a href="listefed.php" class="btn btn-danger">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
