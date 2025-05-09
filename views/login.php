<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - MovieVibe</title>
  <link rel="stylesheet" href="style.css" />
  <script defer src="login.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(rgba(0,0,0,0.75), rgba(0,0,0,0.75)), url('cinema.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
    }

    .signin-header {
      padding: 20px 40px;
    }

    .signin-header img {
      height: 50px;
    }

    .login-container {
      max-width: 400px;
      margin: 80px auto;
      background-color: rgba(0, 0, 0, 0.75);
      padding: 40px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }

    .login-container h2 {
      margin-bottom: 30px;
      font-size: 28px;
      text-align: center;
    }

    .login-container input {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: none;
      border-radius: 4px;
      background: #333;
      color: white;
      font-size: 14px;
    }

    .login-container input::placeholder {
      color: #bbb;
    }

    .login-container button {
      width: 100%;
      padding: 12px;
      background-color: #e50914;
      border: none;
      border-radius: 4px;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .login-container button:hover {
      background-color: #f40612;
    }

    .error-message {
      background-color: #000000;
      border: 1px solid #ff0000;
      padding: 10px;
      border-radius: 4px;
      margin-bottom: 15px;
      color: #ff0000;
      font-size: 14px;
      text-align: center;
    }

    .success-message {
      background-color: #000000;
      border: 1px solid #ff0000;
      color: #ff0000;
      padding: 10px;
      border-radius: 4px;
      margin-bottom: 15px;
      text-align: center;
      font-size: 14px;
    }

    .login-footer, .forgot-password {
      margin-top: 20px;
      text-align: center;
      font-size: 14px;
      color: #bbb;
    }

    .login-footer a, .forgot-password a {
      color: #fff;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <header class="signin-header">
    <a href="page.html"><img src="../assets/images/logo.png" alt="MovieVibe Logo" /></a>
  </header>

  <div class="login-container">
    <h2>Connexion</h2>
    <?php if (isset($_SESSION['login_error'])): ?>
      <div class="error-message">
        <?php 
          echo htmlspecialchars($_SESSION['login_error']);
          unset($_SESSION['login_error']);
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="success-message">
        <?php 
          echo htmlspecialchars($_SESSION['success']);
          unset($_SESSION['success']);
        ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="../controllers/LoginController.php">
      <input type="email" name="email" placeholder="Email"  aria-label="Email" />
      <input type="password" name="password" placeholder="Mot de passe" aria-label="Password" />
      <button type="submit" name="login">Se connecter</button>
    </form>

    <div class="login-footer">
      <p>Nouveau sur MovieVibe ? <a href="signup.php">Inscrivez-vous</a></p>
    </div>
    <div class="forgot-password">
      <p><a href="forgot-password.php">Mot de passe oublié ?</a></p>
    </div>
  </div>
</body>
</html>
