<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Tableau de bord - MovieVibe</title>
  <link rel="stylesheet" href="style.css" />
  <script defer src="dashboard.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-image: url('https://images.unsplash.com/photo-1606214174580-2f65c8d9e132');
      background-size: cover;
      background-position: center;
      color: white;
    }

    .dashboard-header {
      background-color: rgba(0, 0, 0, 0.7);
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .dashboard-header h1 {
      margin: 0;
      font-size: 28px;
    }

    .dashboard-header button {
      padding: 10px 20px;
      background-color: #ac7b7b;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
      font-weight: bold;
    }

    .welcome-msg {
      text-align: center;
      margin-top: 80px;
      background-color: rgba(0, 0, 0, 0.6);
      padding: 40px;
      border-radius: 12px;
      width: 60%;
      margin-left: auto;
      margin-right: auto;
    }

    .welcome-msg h2 {
      font-size: 36px;
      margin-bottom: 10px;
    }

    .welcome-msg p {
      font-size: 18px;
      color: #ccc;
    }
  </style>
</head>
<body>
  <div class="dashboard-header">
    <h1>🎥 MovieVibe</h1>
    
  </div>

  <div class="welcome-msg">
    <h2 id="userWelcome">Bienvenue !</h2>
    <p>Vous êtes connecté à votre espace personnel. Explorez nos films et séries ! 🍿</p>
    
  </div>
  <div class="dashboard-header">
    <button id="logoutBtn">Se déconnecter</button>
     <a href="dashboard-admin.php" <button id="backOfficeBtn">🎛️ Back-Office</button>
  </div>
  
</body>
</html>
