<?php
session_start();

// Vérification stricte du rôle administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['isAdmin'])) {
    header('Location: login.php');
    exit();
}

// --- Connexion à la base de données ---
$pdo = new PDO('mysql:host=localhost;dbname=films', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer l'ID de l'admin connecté
$adminId = $_SESSION['user']['id'];

// --- Gérer les actions (CRUD) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'block') {
        $stmt = $pdo->prepare("UPDATE users SET is_blocked = TRUE WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    } elseif ($action === 'unblock') {
        $stmt = $pdo->prepare("UPDATE users SET is_blocked = FALSE WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    } elseif ($action === 'add') {
        if (!empty($_POST['password'])) {
            // Ajouter un utilisateur avec l'ID de l'admin connecté
            $stmt = $pdo->prepare("INSERT INTO users (admin_id, name, email, password, role, phone, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $adminId, // Utiliser l'ID de l'admin connecté
                $_POST['name'],
                $_POST['email'],
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                $_POST['role'],
                $_POST['phone']
            ]);
        }
    } elseif ($action === 'edit') {
        if (!empty($_POST['password'])) {
            // Mise à jour avec mot de passe
            $stmt = $pdo->prepare("UPDATE users SET admin_id = ?, name = ?, email = ?, password = ?, role = ?, phone = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                $_POST['admin_id'],
                $_POST['name'],
                $_POST['email'],
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                $_POST['role'],
                $_POST['phone'],
                $_POST['id']
            ]);
        } else {
            // Mise à jour sans mot de passe
            $stmt = $pdo->prepare("UPDATE users SET admin_id = ?, name = ?, email = ?, role = ?, phone = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([
                $_POST['admin_id'],
                $_POST['name'],
                $_POST['email'],
                $_POST['role'],
                $_POST['phone'],
                $_POST['id']
            ]);
        }
    } elseif ($action === 'delete') {
        // Suppression d'un utilisateur
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_POST['id']]);
    }
}

// --- Rechercher des utilisateurs ---
$where_conditions = [];
$params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    if (!empty($_POST['search_term'])) {
        $where_conditions[] = "(name LIKE ? OR email LIKE ?)";
        $search_term = '%' . $_POST['search_term'] . '%';
        $params[] = $search_term;
        $params[] = $search_term;
    }

    if (!empty($_POST['status'])) {
        if ($_POST['status'] === 'blocked') {
            $where_conditions[] = "is_blocked = 1";
        } else if ($_POST['status'] === 'active') {
            $where_conditions[] = "is_blocked = 0";
        }
    }

    if (!empty($_POST['role'])) {
        $where_conditions[] = "role = ?";
        $params[] = $_POST['role'];
    }

    if (!empty($_POST['date_from'])) {
        $where_conditions[] = "created_at >= ?";
        $params[] = $_POST['date_from'] . ' 00:00:00';
    }

    if (!empty($_POST['date_to'])) {
        $where_conditions[] = "created_at <= ?";
        $params[] = $_POST['date_to'] . ' 23:59:59';
    }
}

// Construire la requête SQL
$sql = "SELECT *, 
        DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as formatted_created_at,
        DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as formatted_updated_at 
        FROM users";

if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " ORDER BY created_at DESC";

// Exécuter la requête
$stmt = $pdo->prepare($sql);
if (!empty($params)) {
    $stmt->execute($params);
} else {
    $stmt->execute();
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Exportation CSV ---
if (isset($_POST['export_csv'])) {
    $filename = "users_" . date('Ymd') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    // En-têtes du CSV
    fputcsv($output, ['ID', 'Admin ID', 'Nom', 'Email']);
    foreach ($users as $user) {
        fputcsv($output, $user);
    }
    fclose($output);
    exit;
}

// Ajout des requêtes pour les statistiques
$totalUsers = $pdo->query("SELECT COUNT(*) as total FROM users")->fetch()['total'];
$totalAdmins = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch()['total'];
$totalRegularUsers = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch()['total'];
$recentUsers = $pdo->query("SELECT COUNT(*) as total FROM users WHERE id > (SELECT MAX(id) - 5 FROM users)")->fetch()['total'];

// Requête pour obtenir le nombre d'utilisateurs actifs et inactifs
$userStatusStats = $pdo->query("
    SELECT 
        is_blocked as status,
        COUNT(*) as count
    FROM users
    GROUP BY is_blocked
")->fetchAll(PDO::FETCH_ASSOC);

$activeUsers = 0;
$inactiveUsers = 0;
foreach ($userStatusStats as $stat) {
    if ($stat['status'] == 0) {
        $activeUsers = $stat['count'];
    } else {
        $inactiveUsers = $stat['count'];
    }
}

// Modification de la requête pour utiliser created_at au lieu de last_login
$connectionStats = $pdo->query("
    SELECT 
        DATE(created_at) as stat_date,
        COUNT(*) as count
    FROM users
    GROUP BY DATE(created_at)
    ORDER BY stat_date DESC
    LIMIT 7
")->fetchAll(PDO::FETCH_ASSOC);

$statDates = [];
$statCounts = [];
foreach (array_reverse($connectionStats) as $stat) {
    $statDates[] = date('d/m', strtotime($stat['stat_date']));
    $statCounts[] = $stat['count'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - MovieVibe</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
  /* Stats Container */
  .stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }

  .stat-card {
    background-color: #000000;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #ff0000;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-5px);
  }

  .stat-card i {
    font-size: 2em;
    margin-bottom: 10px;
    color: #ff0000;
  }

  .stat-card h3 {
    color: #ffffff;
    font-size: 1.1em;
    margin: 10px 0;
  }

  .stat-number {
    font-size: 2em;
    font-weight: bold;
    color: #ff0000;
  }

  /* Form Section */
  .form-section {
    background: #000000;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #ff0000;
    margin-bottom: 20px;
  }

  /* Search Section */
  .search-section {
    background: #000000;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #ff0000;
    margin-bottom: 20px;
  }

  /* Modal Styles */
  .modal {
    background-color: rgba(0, 0, 0, 0.9);
  }

  .modal-content {
    background-color: #000000;
    border: 1px solid #ff0000;
  }

  .close {
    color: #ff0000;
  }

  .close:hover {
    color: #cc0000;
  }

  /* Admin Profile Mini */
  .admin-profile-mini {
    background: #000000;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #ff0000;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .admin-avatar {
    width: 40px;
    height: 40px;
    background: #ff0000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-weight: bold;
  }

  .admin-info {
    flex: 1;
  }

  .admin-name {
    color: #ffffff;
    margin: 0;
    font-weight: bold;
  }

  .admin-role {
    color: #ff0000;
    font-size: 0.9em;
  }

  .search-group input[type="text"],
  .search-group input[type="date"],
  .search-group select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ff0000;
      border-radius: 4px;
      background: #000000;
      color: #ffffff;
      margin-bottom: 10px;
  }

  .search-group select {
      cursor: pointer;
      appearance: none;
      -webkit-appearance: none;
      padding-right: 30px;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23ff0000' viewBox='0 0 16 16'%3E%3Cpath d='M8 11.5l-5-5h10l-5 5z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 10px center;
  }

  .search-group input:focus,
  .search-group select:focus {
      outline: none;
      border-color: #ff0000;
      box-shadow: 0 0 0 2px rgba(255, 0, 0, 0.25);
  }

  .advanced-search-form button[type="submit"],
  .advanced-search-form button[type="reset"] {
      padding: 12px 24px;
      font-weight: bold;
      transition: all 0.3s ease;
  }

  .advanced-search-form button[type="submit"] {
      background-color: #ff0000;
      color: white;
  }

  .advanced-search-form button[type="submit"]:hover {
      background-color: #cc0000;
      transform: translateY(-2px);
  }

  .advanced-search-form button[type="reset"] {
      background-color: #000000;
      border: 1px solid #ff0000;
      color: #ffffff;
  }

  .advanced-search-form button[type="reset"]:hover {
      background-color: #ff0000;
  }

  /* Style du tableau */
  .animated-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background: #000000;
      margin: 20px 0;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(255, 0, 0, 0.1);
  }

  .animated-table thead th {
      background: linear-gradient(45deg, #1a1a1a, #2a2a2a);
      color: #ffffff;
      font-weight: bold;
      padding: 15px;
      text-transform: uppercase;
      font-size: 0.9em;
      letter-spacing: 1px;
      border-bottom: 3px solid #ff0000;
  }

  .animated-table tbody tr {
      background: #1a1a1a;
      transition: all 0.3s ease;
  }

  .animated-table tbody tr:hover {
      background: #2a2a2a;
      transform: scale(1.01);
      box-shadow: 0 0 15px rgba(255, 0, 0, 0.2);
  }

  .animated-table td {
      padding: 12px 15px;
      color: #ffffff;
      border-bottom: 1px solid rgba(255, 0, 0, 0.1);
  }

  /* Style des badges de statut */
  .badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.8em;
      font-weight: bold;
      text-transform: uppercase;
  }

  .badge-success {
      background: rgba(40, 167, 69, 0.2);
      color: #28a745;
      border: 1px solid #28a745;
  }

  .badge-danger {
      background: rgba(229, 9, 20, 0.2);
      color: #ff0000;
      border: 1px solid #ff0000;
  }

  /* Style des boutons d'action */
  .actions {
      display: flex;
      gap: 8px;
      justify-content: center;
  }

  .actions button {
      background: transparent;
      border: 1px solid #ff0000;
      color: #ffffff;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s ease;
  }

  .actions button:hover {
      background: #ff0000;
      color: #ffffff;
      transform: translateY(-2px);
  }

  .actions .edit-btn:hover {
      background: #4a90e2;
      border-color: #4a90e2;
  }

  .actions .delete-btn:hover {
      background: #dc3545;
      border-color: #dc3545;
  }

  .actions .block-btn:hover {
      background: #ffc107;
      border-color: #ffc107;
  }

  .actions .profile-btn:hover {
      background: #28a745;
      border-color: #28a745;
  }

  /* Pagination style si nécessaire */
  .pagination {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin: 20px 0;
  }

  .pagination button {
      background: #1a1a1a;
      border: 1px solid #ff0000;
      color: #ffffff;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.3s ease;
  }

  .pagination button:hover,
  .pagination button.active {
      background: #ff0000;
      color: #ffffff;
  }

  /* Animation de chargement du tableau */
  @keyframes fadeIn {
      from {
          opacity: 0;
          transform: translateY(20px);
      }
      to {
          opacity: 1;
          transform: translateY(0);
      }
  }

  .animated-table tbody tr {
      animation: fadeIn 0.5s ease forwards;
      opacity: 0;
  }

  .animated-table tbody tr:nth-child(1) { animation-delay: 0.1s; }
  .animated-table tbody tr:nth-child(2) { animation-delay: 0.2s; }
  .animated-table tbody tr:nth-child(3) { animation-delay: 0.3s; }
  .animated-table tbody tr:nth-child(4) { animation-delay: 0.4s; }
  .animated-table tbody tr:nth-child(5) { animation-delay: 0.5s; }
  </style>
</head>
<body>

<div class="wrapper">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="logo">
      <img src="../assets/images/logo.png" alt="Logo MovieVibe">
      <h2>MovieVibe</h2>
    </div>
    <div class="admin-profile-mini">
      <div class="admin-avatar">
        <?php echo strtoupper(substr($_SESSION['user']['name'] ?? 'A', 0, 1)); ?>
      </div>
      <div class="admin-info">
        <p class="admin-name"><?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Admin'); ?></p>
        <span class="admin-role">Administrateur</span>
      </div>
    </div>
    <nav class="menu">
      <a href="#profile" class="menu-item" data-section="profile"><i class="fas fa-id-card"></i> Mon Profil</a>
      <a href="#users" class="menu-item" data-section="users"><i class="fas fa-users"></i> Utilisateurs</a>
      <a href="#events" class="menu-item" data-section="events"><i class="fas fa-calendar"></i> Événements</a>
      <a href="#movies" class="menu-item" data-section="movies"><i class="fas fa-film"></i> Films</a>
      <a href="#products" class="menu-item" data-section="products"><i class="fas fa-shopping-cart"></i> Produits</a>
      <a href="#claims" class="menu-item" data-section="claims"><i class="fas fa-bell"></i> Réclamations</a>
      <form action="logout.php" method="post">
        <button type="submit" id="quitBtn"><i class="fas fa-sign-out-alt"></i> Quitter</button>
      </form>
    </nav>
  </aside>

  <!-- MAIN -->
  <main class="main-content">
    <header class="top-bar">
      <p id="currentDate">📅 Aujourd'hui : <?php echo date('d/m/Y'); ?></p>
    </header>

    <!-- Section Profil Admin -->
    <div id="profile" class="section-content" style="display: none;">
      <div class="content-header">
        <h1><i class="fas fa-id-card"></i> Mon Profil Administrateur</h1>
      </div>
      
      <div class="profile-detail-container">
        <div class="profile-card">
          <div class="profile-avatar-large">
            <?php echo strtoupper(substr($_SESSION['user']['name'] ?? 'A', 0, 1)); ?>
          </div>
          
          <div class="profile-info-detailed">
            <div class="info-section">
              <h3>Informations Personnelles</h3>
              <div class="info-group">
                <label>Nom :</label>
                <span><?php echo htmlspecialchars($_SESSION['user']['name'] ?? ''); ?></span>
              </div>
              <div class="info-group">
                <label>Email :</label>
                <span><?php echo htmlspecialchars($_SESSION['user']['email'] ?? ''); ?></span>
              </div>
            </div>

            <div class="info-section">
              <h3>Détails du Compte</h3>
              <div class="info-group">
                <label>ID Admin :</label>
                <span><?php echo htmlspecialchars($_SESSION['user']['id'] ?? ''); ?></span>
              </div>
              <div class="info-group">
                <label>Rôle :</label>
                <span>Administrateur</span>
              </div>
              <div class="info-group">
                <label>Statut :</label>
                <span class="status-active">Actif</span>
              </div>
            </div>
          </div>

          <div class="profile-actions">
            <a href="admin/edit-profile.php" class="btn-edit">
              <i class="fas fa-edit"></i> Modifier mon profil
            </a>
          </div>
        </div>
      </div>
    </div>

    <section class="content section-content" id="users">
      <!-- Ajout de la section statistiques -->
      <div class="stats-container">
        <div class="stat-card">
          <i class="fas fa-users"></i>
          <h3>Total Utilisateurs</h3>
          <p class="stat-number"><?= $totalUsers ?></p>
        </div>
        <div class="stat-card">
          <i class="fas fa-user-shield"></i>
          <h3>Administrateurs</h3>
          <p class="stat-number"><?= $totalAdmins ?></p>
        </div>
        <div class="stat-card">
          <i class="fas fa-user"></i>
          <h3>Utilisateurs Standard</h3>
          <p class="stat-number"><?= $totalRegularUsers ?></p>
        </div>
        <div class="stat-card">
          <i class="fas fa-user-plus"></i>
          <h3>Nouveaux Utilisateurs</h3>
          <p class="stat-number"><?= $recentUsers ?></p>
        </div>
      </div>

      <!-- Les deux graphiques côte à côte -->
      <div style="display: flex; gap: 20px; margin: 20px auto;">
          <!-- Graphique circulaire -->
          <div class="chart-wrapper" style="flex: 1;">
              <h3 class="chart-title">Statut des Utilisateurs</h3>
              <canvas id="userStatusChart"></canvas>
          </div>
          
          <!-- Graphique en courbe -->
          <div class="chart-wrapper" style="flex: 1;">
              <h3 class="chart-title">Nouveaux Utilisateurs par Jour</h3>
              <canvas id="connectionsChart"></canvas>
          </div>
      </div>

      <div class="content-header">
        <h1>👥 Liste des utilisateurs</h1>
      </div>

      <!-- Formulaire Ajouter/Modifier -->
      <div class="form-section">
  <h2>Ajouter / Modifier un Utilisateur</h2>
  <form method="POST">
    <input type="hidden" name="id" id="userId">
    <input type="hidden" name="action" id="formAction" value="add">

    <input type="number" name="admin_id" id="adminId" placeholder="Admin ID">
    <input type="text" name="name" id="userName" placeholder="Nom">
    <input type="email" name="email" id="userEmail" placeholder="Email">
    <input type="password" name="password" id="userPassword" placeholder="Mot de passe (laisser vide pour ne pas changer)">
    
    <select name="role" id="userRole">
      <option value="user">Utilisateur</option>
      <option value="admin">Administrateur</option>
    </select>

    <input type="text" name="phone" id="userPhone" placeholder="Téléphone">

    <button type="submit" name="create">Enregistrer</button>
  </form>
</div>

<!-- Recherche d'utilisateurs -->
<div class="search-section">
  <h2>Recherche avancée</h2>
  <form method="POST" class="advanced-search-form">
    <div class="search-group">
      <input type="text" name="search_term" placeholder="Rechercher par nom ou email" value="<?= htmlspecialchars($_POST['search_term'] ?? '') ?>">
    </div>
    
    <div class="search-group">
      <label>Statut :</label>
      <select name="status">
        <option value="">Tous</option>
        <option value="active" <?= isset($_POST['status']) && $_POST['status'] === 'active' ? 'selected' : '' ?>>Actif</option>
        <option value="blocked" <?= isset($_POST['status']) && $_POST['status'] === 'blocked' ? 'selected' : '' ?>>Bloqué</option>
      </select>
    </div>
    
    <div class="search-group">
      <label>Rôle :</label>
      <select name="role">
        <option value="">Tous</option>
        <option value="user" <?= isset($_POST['role']) && $_POST['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
        <option value="admin" <?= isset($_POST['role']) && $_POST['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
      </select>
    </div>
    
    <div class="search-group">
      <label>Période :</label>
      <input type="date" name="date_from" value="<?= htmlspecialchars($_POST['date_from'] ?? '') ?>" placeholder="Date début">
      <input type="date" name="date_to" value="<?= htmlspecialchars($_POST['date_to'] ?? '') ?>" placeholder="Date fin">
    </div>

    <button type="submit" name="search">Rechercher</button>
    <button type="reset">Réinitialiser</button>
  </form>
</div>

<style>
.advanced-search-form {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.search-group {
  margin-bottom: 15px;
}

.search-group label {
  display: block;
  margin-bottom: 5px;
  color: #fff;
}

.search-group input[type="text"],
.search-group input[type="date"],
.search-group select {
  width: 100%;
  padding: 8px;
  border: 1px solid #333;
  border-radius: 4px;
  background: #2a2a2a;
  color: #fff;
  margin-bottom: 10px;
}

.search-group select {
  cursor: pointer;
}

.advanced-search-form button {
  padding: 8px 15px;
  margin-right: 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.advanced-search-form button[type="submit"] {
  background-color: #e50914;
  color: white;
}

.advanced-search-form button[type="submit"]:hover {
  background-color: #f40612;
  transform: translateY(-2px);
}

.advanced-search-form button[type="reset"] {
  background-color: #333;
}

.advanced-search-form button[type="reset"]:hover {
  background-color: #444;
}

/* Styles pour les boutons d'action */
.edit-btn, .qr-btn, .block-btn, .unblock-btn, .delete-btn,
button[type="submit"], button[type="reset"] {
    background-color: #e50914;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.edit-btn:hover, .qr-btn:hover, .block-btn:hover, 
.unblock-btn:hover, .delete-btn:hover,
button[type="submit"]:hover {
    background-color: #f40612;
    transform: translateY(-2px);
}

/* Style spécifique pour le bouton reset */
button[type="reset"] {
    background-color: #333;
}

button[type="reset"]:hover {
    background-color: #444;
}

/* Style pour le bouton Quitter */
#quitBtn {
    background-color: #e50914;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

#quitBtn:hover {
    background-color: #f40612;
    transform: translateY(-2px);
}

/* Styles pour les boutons modaux */
.modal-content button {
    background-color: #e50914;
    color: white;
}

.modal-content button:hover {
    background-color: #f40612;
}

/* Badge styles */
.badge-danger {
    background-color: #e50914;
    color: white;
}

.badge-success {
    background-color: #28a745;
    color: white;
}
</style>

      <!--********************************************** Recherche d'utilisateurs -->

      <!-- Recherche d'utilisateurs -->
      <div class="search-section">
        <h2>Rechercher un utilisateur</h2>
        <form method="POST">
          <input type="text" name="search" placeholder="Rechercher par nom ou email" value="<?= htmlspecialchars($searchQuery) ?>">
          <button type="submit">Rechercher</button>
        </form>
      </div>

      <!-- Bouton pour exporter en CSV -->
      <form method="POST" style="margin-top: 20px;">
        <button type="submit" name="export_csv">Exporter CSV</button>
      </form>

      <!-- Tableau Utilisateurs -->
      <table id="clientTable" class="animated-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Admin ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Téléphone</th>
            <th>Créé le</th>
            <th>Modifié le</th>
            <th>Statut</th>
            <th>QR Code</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['admin_id']) ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td><?= htmlspecialchars($user['phone']) ?></td>
            <td><?= htmlspecialchars($user['formatted_created_at']) ?></td>
            <td><?= htmlspecialchars($user['formatted_updated_at']) ?></td>
            <td>
              <?php if ($user['is_blocked']): ?>
                <span class="badge badge-danger">Bloqué</span>
              <?php else: ?>
                <span class="badge badge-success">Actif</span>
              <?php endif; ?>
            </td>
            <td>
              <button onclick="openQRCode(<?= $user['id'] ?>)" class="qr-btn" title="Voir QR Code">
                <i class="fas fa-qrcode"></i>
              </button>
            </td>
            <td class="actions">
              <button onclick='showUserProfile(<?= json_encode($user) ?>)' class="profile-btn" title="Voir le profil">
                <i class="fas fa-user-circle"></i>
              </button>
              <button onclick='editUser(<?= json_encode($user) ?>)' class="edit-btn">
                <i class="fas fa-edit"></i>
              </button>
              <?php if ($user['is_blocked']): ?>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="unblock">
                  <input type="hidden" name="id" value="<?= $user['id'] ?>">
                  <button type="submit" class="unblock-btn" title="Débloquer l'utilisateur">
                    <i class="fas fa-unlock"></i>
                  </button>
                </form>
              <?php else: ?>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="block">
                  <input type="hidden" name="id" value="<?= $user['id'] ?>">
                  <button type="submit" class="block-btn" title="Bloquer l'utilisateur" onclick="return confirm('Êtes-vous sûr de vouloir bloquer cet utilisateur ?');">
                    <i class="fas fa-lock"></i>
                  </button>
                </form>
              <?php endif; ?>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');" class="delete-btn">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <!-- Modal pour afficher le QR code -->
      <div id="qrModal" class="modal">
        <div class="modal-content">
          <span class="close">&times;</span>
          <h2>QR Code</h2>
          <div id="qrModalContent"></div>
        </div>
      </div>

      <style>
        .qr-btn {
          background-color: #e50914;
          color: white;
          border: none;
          padding: 5px 10px;
          border-radius: 4px;
          cursor: pointer;
        }

        .qr-btn:hover {
          background-color: #f40612;
          transform: translateY(-2px);
        }

        .modal {
          display: none;
          position: fixed;
          z-index: 1000;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0,0,0,0.7);
        }

        .modal-content {
          background-color: #1f1f1f;
          margin: 15% auto;
          padding: 20px;
          border: 1px solid #333;
          border-radius: 8px;
          width: 300px;
          position: relative;
        }

        .close {
          color: #aaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
          cursor: pointer;
        }

        .close:hover {
          color: #fff;
        }

        .qr-code-image {
          max-width: 200px;
          margin: 20px auto;
          display: block;
        }
      </style>

      <script>
      function openQRCode(userId) {
          window.open('qr-viewer.php?user_id=' + userId, '_blank', 'width=600,height=800');
      }

      // Graphique circulaire pour le statut des utilisateurs
      const userStatusCtx = document.getElementById('userStatusChart').getContext('2d');
      const userStatusChart = new Chart(userStatusCtx, {
          type: 'doughnut',
          data: {
              labels: ['Utilisateurs Actifs', 'Utilisateurs Inactifs'],
              datasets: [{
                  data: [<?= $activeUsers ?>, <?= $inactiveUsers ?>],
                  backgroundColor: [
                      '#00ff00',  // Vert pour actif
                      '#ff4444'   // Rouge pour inactif
                  ],
                  borderColor: '#000000',
                  borderWidth: 2
              }]
          },
          options: {
              responsive: true,
              plugins: {
                  legend: {
                      position: 'bottom',
                      labels: {
                          color: '#ffffff'
                      }
                  }
              }
          }
      });

      // Graphique en courbe modifié
      const connectionsCtx = document.getElementById('connectionsChart').getContext('2d');
      const connectionsChart = new Chart(connectionsCtx, {
          type: 'line',
          data: {
              labels: <?= json_encode($statDates) ?>,
              datasets: [{
                  label: 'Nouveaux utilisateurs',
                  data: <?= json_encode($statCounts) ?>,
                  borderColor: '#ff0000',
                  backgroundColor: 'rgba(255, 0, 0, 0.1)',
                  borderWidth: 2,
                  tension: 0.4,
                  fill: true
              }]
          },
          options: {
              responsive: true,
              plugins: {
                  legend: {
                      position: 'bottom',
                      labels: {
                          color: '#ffffff'
                      }
                  }
              },
              scales: {
                  y: {
                      beginAtZero: true,
                      grid: {
                          color: 'rgba(255, 255, 255, 0.1)'
                      },
                      ticks: {
                          color: '#ffffff',
                          stepSize: 1
                      }
                  },
                  x: {
                      grid: {
                          color: 'rgba(255, 255, 255, 0.1)'
                      },
                      ticks: {
                          color: '#ffffff'
                      }
                  }
              }
          }
      });

      // Fonction pour éditer un utilisateur
      function editUser(user) {
          // Remplir le formulaire avec les données de l'utilisateur
          document.getElementById('userId').value = user.id;
          document.getElementById('adminId').value = user.admin_id;
          document.getElementById('userName').value = user.name;
          document.getElementById('userEmail').value = user.email;
          document.getElementById('userPhone').value = user.phone || '';
          document.getElementById('userRole').value = user.role;
          document.getElementById('formAction').value = 'edit';
          
          // Faire défiler jusqu'au formulaire
          const formSection = document.querySelector('.form-section');
          formSection.scrollIntoView({ behavior: 'smooth' });
          
          // Mettre à jour le titre du formulaire
          const formTitle = formSection.querySelector('h2');
          if (formTitle) {
              formTitle.textContent = 'Modifier un Utilisateur';
          }
      }

      // Fonction pour valider le formulaire
      function validateForm(formData) {
          const errors = [];
          
          // Validation du nom (au moins 3 caractères, lettres uniquement)
          const name = formData.get('name').trim();
          if (!name) {
              errors.push('Le nom est obligatoire');
          } else if (name.length < 3) {
              errors.push('Le nom doit contenir au moins 3 caractères');
          } else if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(name)) {
              errors.push('Le nom ne doit contenir que des lettres');
          }

          // Validation de l'email
          const email = formData.get('email').trim();
          if (!email) {
              errors.push('L\'email est obligatoire');
          } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
              errors.push('Format d\'email invalide');
          }

          // Validation du mot de passe (uniquement si c'est un nouvel utilisateur ou si un nouveau mot de passe est fourni)
          const password = formData.get('password');
          const isNewUser = !formData.get('id');
          if (isNewUser || password) {
              if (isNewUser && !password) {
                  errors.push('Le mot de passe est obligatoire pour un nouvel utilisateur');
              } else if (password && password.length < 8) {
                  errors.push('Le mot de passe doit contenir au moins 8 caractères');
              }
          }

          // Validation du téléphone (optionnel, mais doit être valide si fourni)
          const phone = formData.get('phone').trim();
          if (phone && !/^[0-9+\s-]{8,}$/.test(phone)) {
              errors.push('Le numéro de téléphone n\'est pas valide');
          }

          // Validation de l'admin ID
          const adminId = formData.get('admin_id');
          if (!adminId) {
              errors.push('L\'ID administrateur est obligatoire');
          } else if (!/^\d+$/.test(adminId)) {
              errors.push('L\'ID administrateur doit être un nombre');
          }

          return errors;
      }

      // Modifier le gestionnaire de soumission du formulaire
      document.querySelector('.form-section form').addEventListener('submit', function(e) {
          e.preventDefault();
          
          // Supprimer les messages d'erreur existants
          const existingErrors = document.querySelector('.form-errors');
          if (existingErrors) {
              existingErrors.remove();
          }

          const formData = new FormData(this);
          const errors = validateForm(formData);

          if (errors.length > 0) {
              // Afficher les erreurs
              const errorDiv = document.createElement('div');
              errorDiv.className = 'form-errors';
              errorDiv.innerHTML = `
                  <div style="color: #ff0000; background-color: rgba(255, 0, 0, 0.1); 
                              padding: 15px; border-radius: 5px; margin-bottom: 15px; 
                              border: 1px solid #ff0000;">
                      <strong>Erreurs de validation :</strong>
                      <ul style="margin: 10px 0 0; padding-left: 20px;">
                          ${errors.map(error => `<li>${error}</li>`).join('')}
                      </ul>
                  </div>
              `;
              this.insertBefore(errorDiv, this.firstChild);
          } else {
              // Si pas d'erreurs, soumettre le formulaire
              this.submit();
          }
      });

      // Ajouter des gestionnaires d'événements pour la validation en temps réel
      document.getElementById('userName').addEventListener('input', function() {
          const nameError = document.getElementById('nameError');
          if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(this.value.trim()) && this.value.trim()) {
              this.style.borderColor = '#ff0000';
              if (!nameError) {
                  const error = document.createElement('div');
                  error.id = 'nameError';
                  error.style.color = '#ff0000';
                  error.style.fontSize = '12px';
                  error.textContent = 'Le nom ne doit contenir que des lettres';
                  this.parentNode.insertBefore(error, this.nextSibling);
              }
          } else {
              this.style.borderColor = '#ff0000';
              if (nameError) nameError.remove();
          }
      });

      document.getElementById('userEmail').addEventListener('input', function() {
          const emailError = document.getElementById('emailError');
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value.trim()) && this.value.trim()) {
              this.style.borderColor = '#ff0000';
              if (!emailError) {
                  const error = document.createElement('div');
                  error.id = 'emailError';
                  error.style.color = '#ff0000';
                  error.style.fontSize = '12px';
                  error.textContent = 'Format d\'email invalide';
                  this.parentNode.insertBefore(error, this.nextSibling);
              }
          } else {
              this.style.borderColor = '#ff0000';
              if (emailError) emailError.remove();
          }
      });

      document.getElementById('userPhone').addEventListener('input', function() {
          const phoneError = document.getElementById('phoneError');
          if (!/^[0-9+\s-]{8,}$/.test(this.value.trim()) && this.value.trim()) {
              this.style.borderColor = '#ff0000';
              if (!phoneError) {
                  const error = document.createElement('div');
                  error.id = 'phoneError';
                  error.style.color = '#ff0000';
                  error.style.fontSize = '12px';
                  error.textContent = 'Numéro de téléphone invalide';
                  this.parentNode.insertBefore(error, this.nextSibling);
              }
          } else {
              this.style.borderColor = '#ff0000';
              if (phoneError) phoneError.remove();
          }
      });
      </script>

      <!-- Section Profil Utilisateur -->
      <div id="userProfile" class="section-content" style="display: none;">
        <div class="content-header">
          <h1><i class="fas fa-user-circle"></i> Profil Utilisateur</h1>
        </div>
        
        <div class="profile-detail-container">
          <div class="profile-header">
            <div class="back-button">
              <button onclick="showSection('users')" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour à la liste
              </button>
            </div>
          </div>

          <div class="profile-card">
            <div class="profile-avatar-large">
              <i class="fas fa-user"></i>
            </div>
            
            <div class="profile-info-detailed">
              <div class="info-section">
                <h3>Informations Personnelles</h3>
                <div class="info-group">
                  <label>Nom :</label>
                  <span id="profileName"></span>
                </div>
                <div class="info-group">
                  <label>Email :</label>
                  <span id="profileEmail"></span>
                </div>
                <div class="info-group">
                  <label>Téléphone :</label>
                  <span id="profilePhone"></span>
                </div>
              </div>

              <div class="info-section">
                <h3>Détails du Compte</h3>
                <div class="info-group">
                  <label>ID Utilisateur :</label>
                  <span id="profileId"></span>
                </div>
                <div class="info-group">
                  <label>Rôle :</label>
                  <span id="profileRole"></span>
                </div>
                <div class="info-group">
                  <label>Statut du compte :</label>
                  <span id="profileStatus"></span>
                </div>
              </div>

              <div class="info-section">
                <h3>Activité</h3>
                <div class="info-group">
                  <label>Membre depuis :</label>
                  <span id="profileCreatedAt"></span>
                </div>
                <div class="info-group">
                  <label>Dernière modification :</label>
                  <span id="profileUpdatedAt"></span>
                </div>
              </div>
            </div>

            <div class="profile-actions">
              <button onclick="editUserProfile()" class="btn-edit">
                <i class="fas fa-edit"></i> Modifier
              </button>
              <button onclick="showUserQRCode()" class="btn-qr">
                <i class="fas fa-qrcode"></i> QR Code
              </button>
              <button onclick="toggleUserStatus()" class="btn-status">
                <i class="fas fa-lock"></i> Changer le statut
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer tous les liens du menu
    const menuItems = document.querySelectorAll('.menu-item');
    const sections = document.querySelectorAll('.section-content');
    
    // Fonction pour afficher une section
    function showSection(sectionId) {
        // Cacher toutes les sections
        sections.forEach(section => {
            section.style.display = 'none';
        });
        
        // Afficher la section demandée
        const activeSection = document.getElementById(sectionId);
        if (activeSection) {
            activeSection.style.display = 'block';
        }
        
        // Mettre à jour les classes actives du menu
        menuItems.forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-section') === sectionId) {
                item.classList.add('active');
            }
        });
    }
    
    // Ajouter les écouteurs d'événements pour chaque lien du menu
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.getAttribute('data-section');
            showSection(sectionId);
            
            // Mettre à jour l'URL sans recharger la page
            history.pushState(null, null, `#${sectionId}`);
        });
    });
    
    // Gérer le chargement initial de la page
    const defaultSection = window.location.hash ? window.location.hash.substring(1) : 'profile';
    showSection(defaultSection);
    
    // Gérer les changements d'historique
    window.addEventListener('popstate', function() {
        const sectionId = window.location.hash.substring(1) || 'profile';
        showSection(sectionId);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const advancedSearchForm = document.querySelector('.advanced-search-form');
    
    // Handle form reset
    advancedSearchForm.querySelector('button[type="reset"]').addEventListener('click', function(e) {
        e.preventDefault();
        advancedSearchForm.reset();
        advancedSearchForm.submit();
    });
    
    // Prevent automatic form submission on enter key
    advancedSearchForm.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});
</script>
</body>
</html>
