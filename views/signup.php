<?php
session_start();
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']); // Clear the form data after retrieving it
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - MovieVibe</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <div class="header-container">
            <a href="page.html"><img src="../assets/images/logo.png" alt="MovieVibe Logo" class="logo"></a>
        </div>
    </header>

    <div class="signup-container">
        <h2>Create Account</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="../controllers/signupController.php" class="signup-form">
            <input type="text" 
                   name="name" 
                   placeholder="Full name" 
                   
                   class="input-field"
                   value="<?= htmlspecialchars($formData['name'] ?? '') ?>" />

            <input type="email" 
                   name="email" 
                   placeholder="Email" 
                    
                   class="input-field"
                   value="<?= htmlspecialchars($formData['email'] ?? '') ?>" />

            <input type="password" 
                   name="password" 
                   placeholder="Password (minimum 8 characters)" 
                   
                   minlength="8"
                   class="input-field" />

            <input type="text" 
                   name="phone" 
                   placeholder="Phone Number" 
                   class="input-field"
                   value="<?= htmlspecialchars($formData['phone'] ?? '') ?>" />

            <select name="role"  class="input-field">
                <option value="" disabled selected>Choose a role</option>
                <option value="user" <?= (($formData['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= (($formData['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>

            <button type="submit" class="submit-btn">Sign Up</button>
        </form>

        <div class="footer">
            <p>Already have an account? <a href="login.php" class="link">Sign in</a></p>
        </div>
    </div>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('cinema.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            padding: 20px;
            text-align: center;
        }

        .logo {
            max-width: 200px;
        }

        .signup-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            margin: 20px auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .signup-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .input-field {
            padding: 12px;
            border: 2px solid #444;
            border-radius: 5px;
            background-color: #222;
            color: white;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .input-field:focus {
            border-color: #e50914;
            outline: none;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.2);
            border: 1px solid #ff0000;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }

        .submit-btn {
            background-color: #e50914;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #f40612;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .link {
            color: #e50914;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        select.input-field {
            appearance: none;
            padding-right: 30px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23ffffff' viewBox='0 0 16 16'%3E%3Cpath d='M8 11.5l-5-5h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }

        @media (max-width: 480px) {
            .signup-container {
                width: 95%;
                padding: 20px;
            }

            .input-field, .submit-btn {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</body>
</html>
