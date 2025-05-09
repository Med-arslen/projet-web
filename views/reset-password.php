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
        <div class="reset-password-container">
            <h2>Réinitialiser votre mot de passe</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="password">Nouveau mot de passe :</label>
                    <input type="password" name="password" placeholder="Entrez votre nouveau mot de passe" />
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmez le mot de passe :</label>
                    <input type="password" name="confirm_password" placeholder="Confirmez votre mot de passe" />
                </div>
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
            font-family: 'Segoe UI', sans-serif;
            background-color: #000000;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .reset-password-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            border: 1px solid #ff0000;
        }

        h2 {
            text-align: center;
            color: #ff0000;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #ff0000;
        }

        input {
            width: 100%;
            padding: 10px;
            background-color: #000000;
            border: 1px solid #ff0000;
            border-radius: 4px;
            color: #ffffff;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.25);
        }

        button {
            padding: 12px;
            background-color: #ff0000;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        button:hover {
            background-color: #cc0000;
        }

        .error, .success {
            color: #ff0000;
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ff0000;
            border-radius: 4px;
            background-color: #000000;
        }
    </style>
