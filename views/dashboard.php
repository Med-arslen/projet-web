<?php
session_start();

// Vérification du rôle et redirection si nécessaire
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
} else if ($_SESSION['role'] === 'admin') {
    header('Location: dashboard-admin.php');
    exit();
} else if ($_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Tableau de bord - MovieVibe</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #000000;
      color: white;
    }

    .dashboard-header {
      background-color: rgba(0, 0, 0, 0.9);
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #ff0000;
    }

    .dashboard-header h1 {
      margin: 0;
      font-size: 28px;
      color: #ff0000;
    }

    .logout-btn {
      padding: 12px 25px;
      background-color: #ff0000;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
      font-weight: bold;
      font-size: 16px;
      transition: transform 0.3s ease;
    }

    .logout-btn:hover {
      transform: scale(1.05);
    }

    .welcome-msg {
      text-align: center;
      margin-top: 80px;
      background-color: rgba(0, 0, 0, 0.8);
      padding: 40px;
      border-radius: 12px;
      width: 60%;
      margin-left: auto;
      margin-right: auto;
      border: 1px solid #ff0000;
    }

    .welcome-msg h2 {
      font-size: 36px;
      margin-bottom: 10px;
      color: #ff0000;
    }

    .welcome-msg p {
      font-size: 18px;
      color: white;
    }

    .profile-section {
      background-color: rgba(0, 0, 0, 0.8);
      padding: 30px;
      border-radius: 12px;
      width: 60%;
      margin: 40px auto;
      border: 1px solid #ff0000;
    }

    .profile-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .profile-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background-color: #ff0000;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 48px;
      color: white;
    }

    .profile-info {
      display: grid;
      grid-template-columns: auto 1fr;
      gap: 15px;
      max-width: 400px;
      margin: 0 auto;
    }

    .profile-label {
      font-weight: bold;
      color: #ff0000;
    }

    .profile-value {
      color: white;
    }

    .edit-profile-btn {
      display: block;
      width: 200px;
      margin: 20px auto 0;
      padding: 10px;
      background-color: #ff0000;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
      font-weight: bold;
      transition: transform 0.3s ease;
      text-align: center;
      text-decoration: none;
    }

    .edit-profile-btn:hover {
      transform: scale(1.05);
    }

    /* Styles pour la section statistiques */
    .stats-section {
      background-color: rgba(0, 0, 0, 0.8);
      padding: 30px;
      border-radius: 12px;
      width: 60%;
      margin: 40px auto;
      border: 1px solid #ff0000;
    }

    .stats-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .stats-header h3 {
      color: #ff0000;
      font-size: 24px;
      margin-bottom: 10px;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }

    .stat-card {
      background-color: rgba(255, 0, 0, 0.1);
      padding: 20px;
      border-radius: 8px;
      border: 1px solid #ff0000;
    }

    .stat-title {
      color: #ff0000;
      font-size: 18px;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .stat-content {
      color: white;
    }

    .stat-chart {
      height: 200px;
      margin-top: 15px;
    }

    .watching-hours {
      font-size: 24px;
      color: #ff0000;
      text-align: center;
      margin: 10px 0;
    }

    .genre-list {
      list-style: none;
      padding: 0;
    }

    .genre-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      padding: 5px;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 4px;
    }

    .genre-name {
      color: white;
    }

    .genre-count {
      color: #ff0000;
      font-weight: bold;
    }

    .time-distribution {
      display: flex;
      justify-content: space-between;
      margin-top: 10px;
    }

    .time-block {
      text-align: center;
      flex: 1;
    }

    .time-label {
      color: #ff0000;
      font-size: 12px;
    }

    .time-value {
      color: white;
      font-size: 16px;
      margin-top: 5px;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="dashboard-header">
    <h1>🎥 MovieVibe</h1>
    <form action="logout.php" method="POST">
      <input type="hidden" name="logout" value="1">
      <button type="submit" class="logout-btn">Se déconnecter</button>
    </form>
  </div>
  <div class="welcome-msg">
    <h2>Bienvenue <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Utilisateur'; ?> !</h2>
    <p>Vous êtes connecté à votre espace personnel. Explorez nos films et séries ! 🍿</p>
  </div>

  <div class="profile-section">
    <div class="profile-header">
      <div class="profile-avatar">
        <?php echo strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)); ?>
      </div>
      <h3>Mon Profil</h3>
    </div>
    <div class="profile-info">
      <span class="profile-label">Nom :</span>
      <span class="profile-value"><?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></span>
      
      <span class="profile-label">Email :</span>
      <span class="profile-value"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
      
      <span class="profile-label">Membre depuis :</span>
      <span class="profile-value"><?php echo isset($_SESSION['created_at']) ? date('d/m/Y', strtotime($_SESSION['created_at'])) : ''; ?></span>
    </div>
    <a href="user/edit-profile.php" class="edit-profile-btn">Modifier mon profil</a>
  </div>

  <div class="stats-section">
    <div class="stats-header">
      <h3>📊 Vos Statistiques Personnelles</h3>
      <p>Découvrez vos habitudes de visionnage</p>
    </div>
    
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-title">
          <i class="fas fa-film"></i> Genres Préférés
        </div>
        <div class="stat-content">
          <ul class="genre-list">
            <li class="genre-item">
              <span class="genre-name">Action</span>
              <span class="genre-count">42%</span>
            </li>
            <li class="genre-item">
              <span class="genre-name">Science Fiction</span>
              <span class="genre-count">28%</span>
            </li>
            <li class="genre-item">
              <span class="genre-name">Drame</span>
              <span class="genre-count">15%</span>
            </li>
            <li class="genre-item">
              <span class="genre-name">Comédie</span>
              <span class="genre-count">15%</span>
            </li>
          </ul>
          <div class="stat-chart">
            <canvas id="genresChart"></canvas>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-title">
          <i class="fas fa-clock"></i> Temps de Visionnage
        </div>
        <div class="stat-content">
          <div class="watching-hours">127h</div>
          <p>Temps total ce mois-ci</p>
          <div class="stat-chart">
            <canvas id="watchTimeChart"></canvas>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-title">
          <i class="fas fa-sun"></i> Moments Favoris
        </div>
        <div class="stat-content">
          <div class="time-distribution">
            <div class="time-block">
              <div class="time-label">Matin</div>
              <div class="time-value">15%</div>
            </div>
            <div class="time-block">
              <div class="time-label">Après-midi</div>
              <div class="time-value">25%</div>
            </div>
            <div class="time-block">
              <div class="time-label">Soir</div>
              <div class="time-value">45%</div>
            </div>
            <div class="time-block">
              <div class="time-label">Nuit</div>
              <div class="time-value">15%</div>
            </div>
          </div>
          <div class="stat-chart">
            <canvas id="timeDistributionChart"></canvas>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-title">
          <i class="fas fa-chart-line"></i> Évolution Mensuelle
        </div>
        <div class="stat-content">
          <div class="stat-chart">
            <canvas id="monthlyProgressChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Configuration des graphiques
    const genresCtx = document.getElementById('genresChart').getContext('2d');
    new Chart(genresCtx, {
      type: 'doughnut',
      data: {
        labels: ['Action', 'Science Fiction', 'Drame', 'Comédie'],
        datasets: [{
          data: [42, 28, 15, 15],
          backgroundColor: [
            '#ff0000',
            '#ff4444',
            '#ff8888',
            '#ffcccc'
          ],
          borderWidth: 1
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

    const watchTimeCtx = document.getElementById('watchTimeChart').getContext('2d');
    new Chart(watchTimeCtx, {
      type: 'line',
      data: {
        labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
        datasets: [{
          label: 'Heures de visionnage',
          data: [2.5, 3, 4.5, 2, 5, 6, 4],
          borderColor: '#ff0000',
          backgroundColor: 'rgba(255, 0, 0, 0.1)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: '#ffffff'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          },
          x: {
            ticks: {
              color: '#ffffff'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          }
        }
      }
    });

    const timeDistributionCtx = document.getElementById('timeDistributionChart').getContext('2d');
    new Chart(timeDistributionCtx, {
      type: 'bar',
      data: {
        labels: ['Matin', 'Après-midi', 'Soir', 'Nuit'],
        datasets: [{
          data: [15, 25, 45, 15],
          backgroundColor: '#ff0000',
          borderColor: '#ff0000',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: '#ffffff'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          },
          x: {
            ticks: {
              color: '#ffffff'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          }
        }
      }
    });

    const monthlyProgressCtx = document.getElementById('monthlyProgressChart').getContext('2d');
    new Chart(monthlyProgressCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
        datasets: [{
          label: 'Films regardés',
          data: [8, 12, 15, 10, 18, 14],
          borderColor: '#ff0000',
          backgroundColor: 'rgba(255, 0, 0, 0.1)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: '#ffffff'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          },
          x: {
            ticks: {
              color: '#ffffff'
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)'
            }
          }
        }
      }
    });
  </script>
</body>
</html>
