<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #000000;
            color: #ffffff;
        }

        .forgot-password-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            border: 1px solid #ff0000;
        }

        h2 {
            text-align: center;
            color: #ff0000;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="email"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ff0000;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
            background-color: #000000;
            color: #ffffff;
        }

        input[type="email"]:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.25);
        }

        button {
            background-color: #ff0000;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #cc0000;
        }

        .message {
            background-color: #000000;
            border: 1px solid #ff0000;
            color: #ff0000;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            border-color: #ff0000;
        }

        .error {
            border-color: #ff0000;
        }
    </style>
</head>
<body>

    <div class="forgot-password-container">
        <h2>Mot de passe oublié</h2>

        <form method="POST" action="reset-password.php">
            <input type="email" name="email" placeholder="Entrez votre email" />
            <button type="submit">Réinitialiser le mot de passe</button>
        </form>

        <div class="message">
            <p>Entrez l'email associé à votre compte pour recevoir un lien de réinitialisation.</p>
        </div>
    </div>

</body>
</html>
