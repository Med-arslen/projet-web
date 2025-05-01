        
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"/>
    <link rel="stylesheet" href="his.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="menu-bar">
            <div class="netflixLogo">
                <a id="logo" href="#home"><img src="logo.png" alt="Logo Image" id="logo1"></a>
            </div>    
            <ul>
                <li><a href="page.html">Home</a></li>
                <li><a href="#">catalogue</a></li>
                <li><a href="event.html">events <i class="fas fa-caret-down"></i></a>
                    <div class="dropdown-menu">
                        <ul>
                            <li><a href="./historique.php">historique</a></li>
                        </ul>
                    </div>
                </li>
                <li><a href="#">achat </a></li>
                <li><a href="./reclamation.php">reclamation</a>
                    <div class="dropdown-menu">
                        <ul>
                            <li><a href="./historique.php">historique</a></li>
                        </ul>
                    </div>
                </li>
            </ul>
            <nav class="sub-nav">
                <a href="#"><i class="fas fa-search sub-nav-logo"></i></a>
                <a href="#"><i class="fas fa-bell sub-nav-logo"></i></a>
                <a href="#">Account</a>        
            </nav>  
        </div>
    </header>

    <!-- Historique Table to display reservations -->
    <h2>Votre Reclamation Historique</h2>
    <table>
        <thead>
            <tr>
                <th>Nom Prenom</th>
                <th>Email</th>
                <th>Titre du film concerné :</th>
                <th>Type de problème :</th>
                <th>Détails du problème :</th>
                <th>Reponse :</th>
            </tr>
        </thead>
        <tbody>
        <?php
            require_once '../../config/database.php';
            require_once '../../controller/ReclamationController.php';

            $sql = "SELECT * FROM reclamationn";
            $conn = config::getConnexion();
            $stmt = $conn->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <tr>
                <td><?= htmlspecialchars($row["nomprenom"]) ?></td>
                <td><?= htmlspecialchars($row["email"]) ?></td>
                <td><?= htmlspecialchars($row["nomfilm"]) ?></td>
                <td><?= htmlspecialchars($row["type_rec"]) ?></td>
                <td><?= htmlspecialchars($row["detail"]) ?></td>
                <td><?= htmlspecialchars($row["reponse_rec"]) ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

  
</body>


<script src="script.js"></script>

</body>
</html>
