<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=films', 'root', ''); // Ajoutez vos informations de connexion

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Vérifier si l'email existe dans la base de données
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Afficher le formulaire pour modifier le mot de passe
        echo '
        <div class="container">
            <h2>Réinitialiser votre mot de passe</h2>
            <form method="POST" action="">
                <label for="password">Nouveau mot de passe :</label>
                <input type="password" name="password" placeholder="Entrez votre nouveau mot de passe" required />
                
                <label for="confirm_password">Confirmez le mot de passe :</label>
                <input type="password" name="confirm_password" placeholder="Confirmez votre mot de passe" required />

                <button type="submit">Réinitialiser le mot de passe</button>
            </form>
        </div>';
    } else {
        echo "<div class='error'>Aucun compte trouvé avec cet email.</div>";
    }
}

// Si un mot de passe est soumis via le formulaire
if (isset($_POST['password'])) {
    $email = $_POST['email'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Vérification de la correspondance des mots de passe
    if ($newPassword === $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Mettre à jour le mot de passe dans la base de données
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        echo "<div class='success'>Votre mot de passe a été réinitialisé avec succès.</div>";
    } else {
        echo "<div class='error'>Les mots de passe ne correspondent pas. Veuillez réessayer.</div>";
    }
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 400px;
        margin: 50px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #5C6BC0;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="password"] {
        padding: 10px;
        margin: 10px 0 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    button {
        padding: 12px;
        background-color: #5C6BC0;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #3949AB;
    }

    .error {
        color: red;
        text-align: center;
        margin-top: 20px;
    }

    .success {
        color: green;
        text-align: center;
        margin-top: 20px;
    }
</style>
