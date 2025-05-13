<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil - MovieVibe Admin</title>
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
            text-decoration: none;
            display: inline-block;
            text-align: center;
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
        <h2 class="form-title">Modifier mon profil administrateur</h2>
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
        
        <form action="../../controllers/AdminController.php" method="POST">
            <input type="hidden" name="action" value="updateProfile">
            
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['user']['name'] ?? ''); ?>" >
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?>" >
            </div>

            <div class="form-group">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password">
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
                <a href="../dashboard-admin.php" class="cancel-btn">Annuler</a>
                <button type="submit" class="submit-btn">Enregistrer</button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');

            form.addEventListener('submit', function(e) {
                let hasError = false;
                let errorMessage = '';

                // Validation du nom
                if (name.value.trim().length < 2) {
                    errorMessage += "Le nom doit contenir au moins 2 caractères.\n";
                    hasError = true;
                }

                // Validation de l'email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value.trim())) {
                    errorMessage += "L'email n'est pas valide.\n";
                    hasError = true;
                }

                // Validation des mots de passe
                if (newPassword.value) {
                    if (!currentPassword.value) {
                        errorMessage += "Veuillez entrer votre mot de passe actuel.\n";
                        hasError = true;
                    }
                    if (newPassword.value.length < 8) {
                        errorMessage += "Le nouveau mot de passe doit contenir au moins 8 caractères.\n";
                        hasError = true;
                    }
                    if (newPassword.value !== confirmPassword.value) {
                        errorMessage += "Les mots de passe ne correspondent pas.\n";
                        hasError = true;
                    }
                }

                if (hasError) {
                    e.preventDefault();
                    alert(errorMessage);
                }
            });

            // Validation en temps réel
            newPassword.addEventListener('input', function() {
                if (this.value.length > 0 && this.value.length < 8) {
                    this.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères');
                } else {
                    this.setCustomValidity('');
                }
            });

            confirmPassword.addEventListener('input', function() {
                if (this.value !== newPassword.value) {
                    this.setCustomValidity('Les mots de passe ne correspondent pas');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
</body>
</html>