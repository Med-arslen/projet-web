<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil - MovieVibe</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #000000;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .edit-form-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            margin: 40px auto;
            border: 1px solid #ff0000;
        }

        .form-title {
            color: #ff0000;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #ff0000;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ff0000;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            box-sizing: border-box;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .submit-btn, .cancel-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.3s ease;
        }

        .submit-btn {
            background-color: #ff0000;
            color: white;
        }

        .cancel-btn {
            background-color: #333;
            color: white;
        }

        .submit-btn:hover, .cancel-btn:hover {
            transform: scale(1.05);
        }

        .message {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: rgba(0, 255, 0, 0.1);
            color: #00ff00;
        }

        .error {
            background-color: rgba(255, 0, 0, 0.1);
            color: #ff0000;
        }
    </style>
</head>
<body>
    <div class="edit-form-container">
        <h2 class="form-title">Modifier mon profil</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success">
                <?php 
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
          <form action="../../controllers/update-profile-handler.php" method="POST">
            <input type="hidden" name="action" value="updateProfile">
            
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="current_password">Mot de passe actuel (requis)</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
                <label for="new_password">Nouveau mot de passe (laisser vide si inchangé)</label>
                <input type="password" id="new_password" name="new_password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>

            <div class="btn-container">
                <a href="../dashboard.php" class="cancel-btn">Annuler</a>
                <button type="submit" class="submit-btn">Enregistrer</button>
            </div>
        </form>
    </div>
</body>
</html>