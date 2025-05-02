<?php
// --- Connexion à la base de données ---
$pdo = new PDO('mysql:host=localhost;dbname=films', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
            // Ajouter un utilisateur avec mot de passe et date de création
            $stmt = $pdo->prepare("INSERT INTO users (admin_id, name, email, password, role, phone, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $_POST['admin_id'],
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

if (isset($_POST['search_term']) && !empty($_POST['search_term'])) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ?)";
    $params[] = '%' . $_POST['search_term'] . '%';
    $params[] = '%' . $_POST['search_term'] . '%';
}

if (isset($_POST['status']) && !empty($_POST['status'])) {
    if ($_POST['status'] === 'blocked') {
        $where_conditions[] = "is_blocked = TRUE";
    } else if ($_POST['status'] === 'active') {
        $where_conditions[] = "is_blocked = FALSE";
    }
}

if (isset($_POST['role']) && !empty($_POST['role'])) {
    $where_conditions[] = "role = ?";
    $params[] = $_POST['role'];
}

if (isset($_POST['date_from']) && !empty($_POST['date_from'])) {
    $where_conditions[] = "created_at >= ?";
    $params[] = $_POST['date_from'] . ' 00:00:00';
}

if (isset($_POST['date_to']) && !empty($_POST['date_to'])) {
    $where_conditions[] = "created_at <= ?";
    $params[] = $_POST['date_to'] . ' 23:59:59';
}

$query = "SELECT *, DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') as formatted_created_at, 
                 DATE_FORMAT(updated_at, '%d/%m/%Y %H:%i') as formatted_updated_at 
          FROM users";

if (!empty($where_conditions)) {
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}

$query .= " ORDER BY updated_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - MovieVibe</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="wrapper">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="logo">
      <img src="logo.png" alt="Logo MovieVibe">
      <h2>MovieVibe</h2>
    </div>
    <nav class="menu">
      <a class="active"><i class="fas fa-user"></i> Utilisateurs</a>
      <a><i class="fas fa-calendar"></i> Événements</a>
      <a><i class="fas fa-film"></i> Films</a>
      <a><i class="fas fa-shopping-cart"></i> Produits</a>
      <a><i class="fas fa-bell"></i> Réclamations</a>
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

    <section class="content">
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

      <div class="content-header">
        <h1>👥 Liste des utilisateurs</h1>
      </div>

      <!-- Formulaire Ajouter/Modifier -->
      <div class="form-section">
  <h2>Ajouter / Modifier un Utilisateur</h2>
  <form method="POST">
    <input type="hidden" name="id" id="userId">
    <input type="hidden" name="action" id="formAction" value="add">

    <input type="number" name="admin_id" id="adminId" placeholder="Admin ID" required>
    <input type="text" name="name" id="userName" placeholder="Nom" required>
    <input type="email" name="email" id="userEmail" placeholder="Email" required>
    <input type="password" name="password" id="userPassword" placeholder="Mot de passe (laisser vide pour ne pas changer)">
    
    <select name="role" id="userRole" required>
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
  background: #ac7b7b;
  color: white;
}

.advanced-search-form button[type="reset"] {
  background: #333;
  color: white;
}

.advanced-search-form button:hover {
  opacity: 0.9;
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
          background-color: #4CAF50;
          color: white;
          border: none;
          padding: 5px 10px;
          border-radius: 4px;
          cursor: pointer;
        }

        .qr-btn:hover {
          background-color: #45a049;
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
      </script>

    </section>
  </main>
</div>
<script>
// Script pour remplir le formulaire en mode "edit"
function editUser(user) {
  document.getElementById('userId').value = user.id;
  document.getElementById('adminId').value = user.admin_id;
  document.getElementById('userName').value = user.name;
  document.getElementById('userEmail').value = user.email;
  document.getElementById('userPassword').value = ''; // Le mot de passe reste vide pour modification sans changement de mot de passe
  document.getElementById('userPhone').value = user.phone; // Remplir le champ du téléphone
  document.getElementById('userRole').value = user.role; // Remplir le champ du rôle (user ou admin)
  document.getElementById('formAction').value = 'edit';
}

// Fonction pour réinitialiser les filtres
document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector('input[name="search_term"]').value = '';
    document.querySelector('select[name="status"]').value = '';
    document.querySelector('select[name="role"]').value = '';
    document.querySelector('input[name="date_from"]').value = '';
    document.querySelector('input[name="date_to"]').value = '';
    this.closest('form').submit();
});

// Validation des dates
document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
    const dateFrom = document.querySelector('input[name="date_from"]').value;
    const dateTo = document.querySelector('input[name="date_to"]').value;
    
    if (dateFrom && dateTo && dateFrom > dateTo) {
        e.preventDefault();
        alert('La date de début doit être antérieure à la date de fin');
    }
});
</script>


</body>
</html>
<style>
.user-form {
  background: #fff;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  max-width: 500px;
  margin: 20px auto;
}

.user-form .form-group {
  margin-bottom: 1.5rem;
}

.user-form label {
  display: block;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #333;
}
input[type="number"],
 input[type="text"],
 input[type="email"],
 select,
input[type="password"] {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

 ser-form input:focus {
  border-color: #5c6bc0;
  outline: none;
}

  .form-actions {
  text-align: center;
}

 .btn-save {
  background-color: #5c6bc0;
  color: white;
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s;
}
.btn-save:hover {
  background-color: #3949ab;
}

/* Styles pour la section statistiques */
.stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background-color: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-card i {
  font-size: 2em;
  margin-bottom: 10px;
  color: #ac7b7b;
}

.stat-card h3 {
  color: #ffffff;
  font-size: 1.1em;
  margin: 10px 0;
}

.stat-number {
  font-size: 2em;
  font-weight: bold;
  color: #ac7b7b;
  margin: 0;
}

.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.block-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
}

.unblock-btn {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
}

.block-btn:hover, .unblock-btn:hover {
    opacity: 0.8;
}
</style>
