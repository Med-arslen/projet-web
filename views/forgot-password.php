<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .forgot-password-container {
            background-color: white;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="email"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="email"]:focus {
            outline: none;
            border-color: #0056b3;
        }

        button {
            background-color: #0056b3;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #003d80;
        }

        .message {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="forgot-password-container">
        <h2>Mot de passe oublié</h2>

        <form method="POST" action="reset-password.php">
            <input type="email" name="email" placeholder="Entrez votre email" required />
            <button type="submit">Réinitialiser le mot de passe</button>
        </form>

        <div class="message">
            <p>Entrez l'email associé à votre compte pour recevoir un lien de réinitialisation.</p>
        </div>
    </div>

</body>
</html>
