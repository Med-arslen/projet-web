<?php
include 'C:\xampp\htdocs\projetweb\config.php';
include 'C:\xampp\htdocs\projetweb\Controller\ProduitController.php';
include 'C:\xampp\htdocs\projetweb\Controller\CommandeController.php';


$controller = new ProduitController($pdo);
$produits = $controller->getAllProduits();

$commandeController = new CommandeController($pdo);
$commandes = $commandeController->getAllCommandes();

$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'produits';

// Pagination pour les produits
$produitsParPage = 4;
$nombreTotalProduits = count($produits);
$nombrePages = ceil($nombreTotalProduits / $produitsParPage);
$pageActuelle = isset($_GET['page']) ? max(1, min($nombrePages, intval($_GET['page']))) : 1;
$indexDebut = ($pageActuelle - 1) * $produitsParPage;
$produitsPage = array_slice($produits, $indexDebut, $produitsParPage);

// Pagination pour les commandes
$commandesParPage = 4;
$nombreTotalCommandes = count($commandes);
$nombrePagesCommandes = ceil($nombreTotalCommandes / $commandesParPage);
$pageActuelleCommandes = isset($_GET['pageCommandes']) ? max(1, min($nombrePagesCommandes, intval($_GET['pageCommandes']))) : 1;
$indexDebutCommandes = ($pageActuelleCommandes - 1) * $commandesParPage;
$commandesPage = array_slice($commandes, $indexDebutCommandes, $commandesParPage);

function buildPaginationUrl($page, $tab) {
    return '?page=' . $page . '&tab=' . $tab;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $quantite = $_POST['quantite'] ?? 0;
    $imageName = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    }

    $controller->createProduit($nom, $description, $prix, $quantite, $imageName);
    header('Location: index.php');
    exit;
}

// Créer un tableau JSON de toutes les commandes pour la carte
$allCommandesForMap = array_map(function($commande) {
    return [
        'client' => $commande['nom_client'],
        'adresse' => $commande['adresse'],
        'produit' => $commande['id_produit'],
        'quantite' => $commande['quantite'],
        'date' => $commande['date_commande']
    ];
}, $commandes);

// Convertir le tableau en JSON pour l'utiliser dans JavaScript
$commandesJson = json_encode($allCommandesForMap);

// Préparer les données pour les statistiques
$allStatsData = array_map(function($commande) {
    return [
        'id_produit' => $commande['id_produit'],
        'nom_client' => $commande['nom_client'],
        'quantite' => $commande['quantite']
    ];
}, $commandes);

// Convertir en JSON pour l'utiliser dans JavaScript
$statsJson = json_encode($allStatsData);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="MovieVibe - Système de gestion de produits"/>
  <meta name="theme-color" content="#141414"/>
  <title>Gestion des Produits - MovieVibe</title>
  <link rel="stylesheet" href="style.css"/>
  <script defer src="script.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <div class="logo">
        <img src="logo.png" alt="Logo MovieVibe" />
        <h2>MovieVibe</h2>
      </div>
      <nav class="menu">
        <a href="<?= buildPaginationUrl(1, 'produits', $pageActuelle, $pageActuelleCommandes) ?>" 
           data-tab="produits" 
           class="tab-button <?= $currentTab === 'produits' ? 'active' : '' ?>">
            <i class="fas fa-box"></i> Produits
        </a>
        <a href="<?= buildPaginationUrl(1, 'commandes', $pageActuelleCommandes, $pageActuelle) ?>" 
           data-tab="commandes" 
           class="tab-button <?= $currentTab === 'commandes' ? 'active' : '' ?>">
            <i class="fas fa-shopping-cart"></i> Commandes
        </a>
      </nav>
      <div class="sidebar-footer">
        <button id="quitBtn" class="quit-button" onclick="window.location.href='../View/page.php';">
          <i class="fas fa-sign-out-alt"></i> Déconnexion
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
      <!-- TOP BAR -->
      <header class="top-bar"></header>

      <!-- Section Produits -->
      <section class="tab-content <?= $currentTab === 'produits' ? 'active' : '' ?>" id="produits">
        <div class="content-header">
          <div class="title-section">
            <h1>Gestion des Produits</h1>
            <p class="subtitle">Vue d'ensemble de votre catalogue</p>
          </div>
          <div class="form-actions">
            <a href="ajouterproduit.php" class="btn btn-primary">
              <i class="fas fa-plus"></i> Ajouter un produit
            </a>
          </div>
        </div>

        <!-- Barre de recherche et tri pour les produits -->
        <div class="search-sort-container">
          <div class="search-box">
            <input type="text" id="searchProduits" placeholder="Rechercher par ID, nom ou produit...">
            <button type="button" onclick="searchProduits()">
              <i class="fas fa-search"></i>
            </button>
          </div>
          <div class="sort-box">
            <select id="sortProduits" onchange="sortProduits()">
              <option value="">Trier par...</option>
              <option value="nom">Nom</option>
              <option value="prix">Prix</option>
              <option value="quantite">Quantité</option>
            </select>
          </div>
        </div>

        <!-- Produits Table -->
        <div class="table-container">
          <table id="produitsTable" class="animated-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Image</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($produitsPage as $produit): ?>
              <tr>
                <td><?= htmlspecialchars($produit->getId()) ?></td>
                <td><?= htmlspecialchars($produit->getNom()) ?></td>
                <td><?= htmlspecialchars($produit->getDescription()) ?></td>
                <td><?= htmlspecialchars($produit->getPrix()) ?></td>
                <td><?= htmlspecialchars($produit->getQuantite()) ?></td>
                <td><img src="../uploads/<?= htmlspecialchars($produit->getImage()) ?>" alt="Image du produit" width="50"></td>
                <td>
                  <a href="updateproduit.php?id=<?= $produit->getId() ?>" class="btn-icon btn-edit" title="Modifier">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="deleteproduit.php?id=<?= $produit->getId() ?>" class="btn-icon btn-delete" onclick="return confirm('Supprimer ce produit ?')" title="Supprimer">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <!-- Pagination -->
          <div class="pagination">
            <?php if ($nombrePages > 1): ?>
              <?php if ($pageActuelle > 1): ?>
                <a href="<?= buildPaginationUrl($pageActuelle - 1, 'produits') ?>" class="pagination-btn">&laquo; Précédent</a>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $nombrePages; $i++): ?>
                <a href="<?= buildPaginationUrl($i, 'produits') ?>" class="pagination-btn <?= $i === $pageActuelle ? 'active' : '' ?>">
                  <?= $i ?>
                </a>
              <?php endfor; ?>

              <?php if ($pageActuelle < $nombrePages): ?>
                <a href="<?= buildPaginationUrl($pageActuelle + 1, 'produits') ?>" class="pagination-btn">Suivant &raquo;</a>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <!-- Section Commandes -->
      <section class="tab-content <?= $currentTab === 'commandes' ? 'active' : '' ?>" id="commandes">
        <div class="content-header">
          <div class="title-section">
            <h1>Gestion des Commandes</h1>
            <p class="subtitle">Vue d'ensemble des commandes</p>
          </div>
          <div class="form-actions">
            <a href="facture.php" class="btn btn-primary">
              <i class="fas fa-file-invoice"></i> Voir la dernière facture
            </a>
          </div>
        </div>

        <!-- Barre de recherche et tri pour les commandes -->
        <div class="search-sort-container">
          <div class="search-box">
            <input type="text" id="searchCommandes" placeholder="Rechercher par ID, produit ou client...">
            <button type="button" onclick="searchCommandes()">
              <i class="fas fa-search"></i>
            </button>
          </div>
          <div class="sort-box">
            <select id="sortCommandes" onchange="sortCommandes()">
              <option value="">Trier par...</option>
              <option value="id">ID</option>
              <option value="produit">Produit</option>
              <option value="client">Client</option>
            </select>
          </div>
        </div>

        <!-- Table des commandes -->
        <div class="table-container">
          <table id="commandesTable" class="animated-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Produit</th>
                <th>Client</th>
                <th>Adresse</th>
                <th>Date</th>
                <th>Quantité</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($commandesPage as $commande): ?>
              <tr>
                <td><?= htmlspecialchars($commande['id']) ?></td>
                <td><?= htmlspecialchars($commande['id_produit']) ?></td>
                <td><?= htmlspecialchars($commande['nom_client']) ?></td>
                <td><?= htmlspecialchars($commande['adresse']) ?></td>
                <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                <td><?= htmlspecialchars($commande['quantite']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <!-- Pagination pour les commandes -->
          <div class="pagination">
            <?php if ($nombrePagesCommandes > 1): ?>
              <?php if ($pageActuelleCommandes > 1): ?>
                <a href="?pageCommandes=<?= $pageActuelleCommandes - 1 ?>&tab=commandes" class="pagination-btn">&laquo; Précédent</a>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $nombrePagesCommandes; $i++): ?>
                <a href="?pageCommandes=<?= $i ?>&tab=commandes" class="pagination-btn <?= $i === $pageActuelleCommandes ? 'active' : '' ?>">
                  <?= $i ?>
                </a>
              <?php endfor; ?>

              <?php if ($pageActuelleCommandes < $nombrePagesCommandes): ?>
                <a href="?pageCommandes=<?= $pageActuelleCommandes + 1 ?>&tab=commandes" class="pagination-btn">Suivant &raquo;</a>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Carte des livraisons -->
        <div class="delivery-map-container">
          <h2>Carte des Livraisons</h2>
          <div class="map-actions">
            <button id="downloadPDF" class="btn btn-primary">
              <i class="fas fa-file-pdf"></i> Télécharger PDF
            </button>
          </div>
          <div id="delivery-map"></div>
        </div>

        <!-- Statistiques des produits -->
        <div class="stats-container">
            <h2>Statistiques des Produits les Plus Vendus</h2>
            <canvas id="produitStats"></canvas>
        </div>

        <script>
// Stocker toutes les données de commandes pour les statistiques
const allStatsData = <?= $statsJson ?>;

// Fonction mise à jour pour calculer les statistiques à partir de toutes les commandes
function calculateStats() {
    const stats = new Map();

    // Utiliser toutes les commandes au lieu des lignes du tableau
    allStatsData.forEach(commande => {
        const produit = commande.id_produit;
        const client = commande.nom_client;
        const quantite = parseInt(commande.quantite) || 0;

        if (!stats.has(produit)) {
            stats.set(produit, {
                nombre_ventes: 0,
                clients: new Set(),
                quantite_totale: 0
            });
        }

        const produitStats = stats.get(produit);
        produitStats.nombre_ventes++;
        produitStats.clients.add(client);
        produitStats.quantite_totale += quantite;
    });

    // Convertir la Map en tableau et trier par quantité totale
    const statsArray = Array.from(stats.entries())
        .map(([produit, data]) => ({
            nom_produit: produit,
            nombre_ventes: data.nombre_ventes,
            nombre_clients: data.clients.size,
            quantite_totale: data.quantite_totale
        }))
        .sort((a, b) => b.quantite_totale - a.quantite_totale)
        .slice(0, 10); // Garder les 10 premiers

    return statsArray;
}

// Fonction pour mettre à jour le graphique avec toutes les données
function updateChart() {
    const stats = calculateStats();
    const ctx = document.getElementById('produitStats');

    if (!stats || stats.length === 0) {
        console.warn('Aucune donnée disponible pour les statistiques');
        return;
    }

    // Détruire le graphique existant s'il y en a un
    const existingChart = Chart.getChart(ctx);
    if (existingChart) {
        existingChart.destroy();
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: stats.map(item => item.nom_produit),
            datasets: [
                {
                    label: 'Quantité totale vendue',
                    data: stats.map(item => item.quantite_totale),
                    backgroundColor: 'rgba(229, 9, 20, 0.7)',
                    borderColor: 'rgba(229, 9, 20, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Nombre de clients uniques',
                    data: stats.map(item => item.nombre_clients),
                    backgroundColor: 'rgba(255, 255, 255, 0.7)',
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#fff'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#fff',
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#fff',
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: false
                }
            }
        }
    });
}

// Supprimer les écouteurs d'événements liés à la recherche et au tri pour les statistiques
// car nous voulons que les statistiques restent indépendantes de la pagination et de la recherche
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('commandes').classList.contains('active')) {
        updateChart();
    }
});

// Mettre à jour le graphique uniquement lors du changement d'onglet
document.querySelector('[data-tab="commandes"]').addEventListener('click', function() {
    setTimeout(updateChart, 100);
});
</script>

        <style>
          .search-sort-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
          }

          .search-box {
            flex: 1;
            display: flex;
            gap: 10px;
          }

          .search-box input {
            flex: 1;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
          }

          .search-box button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background: #e50914;
            color: #fff;
            cursor: pointer;
          }

          .sort-box select {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            cursor: pointer;
          }

          .search-box input:focus,
          .sort-box select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
          }

          .stats-container {
              background: rgba(255, 255, 255, 0.05);
              border-radius: 8px;
              padding: 20px;
              margin-top: 20px;
              margin-bottom: 20px;
          }

          .stats-container h2 {
              color: #fff;
              font-size: 1.2em;
              margin-bottom: 15px;
              text-align: center;
          }

          #produitStats {
              width: 100% !important;
              height: 400px !important;
              margin: 0 auto;
          }

          .delivery-map-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
          }

          .delivery-map-container h2 {
            color: #fff;
            font-size: 1.2em;
            margin-bottom: 15px;
            text-align: center;
          }

          #delivery-map {
            width: 100%;
            height: 400px;
            border-radius: 8px;
            margin-top: 15px;
          }

          .map-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
          }

          .leaflet-popup-content-wrapper {
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
          }

          .leaflet-popup-tip {
            background: rgba(0, 0, 0, 0.8);
          }

          .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            gap: 10px;
          }

          .pagination-btn {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
          }

          .pagination-btn:hover {
            background: rgba(255, 255, 255, 0.2);
          }

          .pagination-btn.active {
            background: #e50914;
            pointer-events: none;
          }
        </style>

        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

        <script>
          // Fonction pour obtenir les coordonnées à partir d'une adresse
          async function getCoordinates(address) {
            try {
              const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`);
              const data = await response.json();
              if (data.length > 0) {
                return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
              }
              return null;
            } catch (error) {
              console.error('Erreur lors de la géocodification:', error);
              return null;
            }
          }

          // Fonction mise à jour pour initialiser la carte avec toutes les commandes
          async function initMap() {
            const map = L.map('delivery-map').setView([33.8869, 9.5375], 7); // Centre sur la Tunisie

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            const processedAddresses = new Map();

            // Utiliser toutes les commandes au lieu des lignes du tableau
            for (const commande of allCommandes) {
                const client = commande.client;
                const adresse = commande.adresse;
                const coordinates = await getCoordinates(adresse);

                if (coordinates) {
                    const key = `${client}-${adresse}`;
                    if (!processedAddresses.has(key)) {
                        const marker = L.marker(coordinates).addTo(map);
                        
                        // Regrouper toutes les commandes pour ce client à cette adresse
                        const clientOrders = allCommandes.filter(c => 
                            c.client === client && c.adresse === adresse
                        );

                        let popupContent = `<b>Client:</b> ${client}<br><b>Adresse:</b> ${adresse}<br><b>Commandes:</b><br>`;
                        clientOrders.forEach(order => {
                            popupContent += `- Produit: ${order.produit}, Quantité: ${order.quantite}<br>`;
                        });

                        marker.bindPopup(popupContent);
                        processedAddresses.set(key, true);
                    }
                }
            }
          }

          // Initialiser la carte lorsque l'onglet commandes est actif
          document.querySelector('[data-tab="commandes"]').addEventListener('click', function() {
            setTimeout(initMap, 100); // Petit délai pour s'assurer que le conteneur est visible
          });

          // Télécharger le PDF
          document.getElementById('downloadPDF').addEventListener('click', function() {
            window.location.href = 'generate_pdf.php';
          });

          // Stocker toutes les commandes dans une variable JavaScript
          const allCommandes = <?= $commandesJson ?>;

          // Supprimer les écouteurs d'événements qui ne sont plus nécessaires
          // La carte n'a plus besoin d'être mise à jour avec la pagination
          document.addEventListener('DOMContentLoaded', function() {
              if (document.getElementById('commandes').classList.contains('active')) {
                  initMap();
              }
          });

          document.querySelector('[data-tab="commandes"]').addEventListener('click', function() {
              setTimeout(initMap, 100);
          });
        </script>
      </section>

      <script>
        // Script pour basculer entre les onglets Produits et Commandes
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const tab = this.dataset.tab;
                
                // Construire l'URL en préservant les paramètres de pagination appropriés
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('tab', tab);
                
                // Préserver le bon paramètre de page selon l'onglet
                if (tab === 'commandes') {
                    const currentPageCommandes = urlParams.get('pageCommandes') || '1';
                    urlParams.set('pageCommandes', currentPageCommandes);
                    urlParams.delete('page'); // Supprimer le paramètre de page des produits
                } else {
                    const currentPage = urlParams.get('page') || '1';
                    urlParams.set('page', currentPage);
                    urlParams.delete('pageCommandes'); // Supprimer le paramètre de page des commandes
                }

                // Mettre à jour l'URL
                window.history.pushState({}, '', '?' + urlParams.toString());
                
                // Activer l'onglet
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                this.classList.add('active');
                document.getElementById(tab).classList.add('active');

                // Initialiser les fonctionnalités spécifiques à l'onglet commandes
                if (tab === 'commandes') {
                    if (typeof initMap === 'function') setTimeout(initMap, 100);
                    if (typeof updateChart === 'function') setTimeout(updateChart, 100);
                }
            });
        });

        // Fonction de validation du formulaire
        function validateForm() {
            const nom = document.getElementById('nom').value.trim();
            const description = document.getElementById('description').value.trim();
            const prix = parseFloat(document.getElementById('prix').value);
            const quantite = parseInt(document.getElementById('quantite').value);
            const image = document.getElementById('image').value;
            let isValid = true;

            // Réinitialiser tous les messages d'erreur
            document.querySelectorAll('.warning-message').forEach(div => {
                div.style.display = 'none';
                div.textContent = '';
            });

            // Validation du nom
            if (nom.length < 3) {
                document.getElementById('nom-warning').textContent = 'Le nom du produit doit contenir au moins 3 caractères.';
                document.getElementById('nom-warning').style.display = 'block';
                isValid = false;
            } else if (nom.length > 50) {
                document.getElementById('nom-warning').textContent = 'Le nom du produit ne doit pas dépasser 50 caractères.';
                document.getElementById('nom-warning').style.display = 'block';
                isValid = false;
            }

            // Validation de la description
            if (description.length < 10) {
                document.getElementById('description-warning').textContent = 'La description doit contenir au moins 10 caractères.';
                document.getElementById('description-warning').style.display = 'block';
                isValid = false;
            } else if (description.length > 255) {
                document.getElementById('description-warning').textContent = 'La description ne doit pas dépasser 255 caractères.';
                document.getElementById('description-warning').style.display = 'block';
                isValid = false;
            }

            // Validation du prix
            if (isNaN(prix) || prix <= 0) {
                document.getElementById('prix-warning').textContent = 'Le prix doit être un nombre positif.';
                document.getElementById('prix-warning').style.display = 'block';
                isValid = false;
            }

            // Validation de la quantité
            if (isNaN(quantite) || quantite < 0) {
                document.getElementById('quantite-warning').textContent = 'La quantité doit être un nombre positif ou nul.';
                document.getElementById('quantite-warning').style.display = 'block';
                isValid = false;
            }

            // Validation de l'image
            if (!image) {
                document.getElementById('image-warning').textContent = 'Veuillez sélectionner une image pour le produit.';
                document.getElementById('image-warning').style.display = 'block';
                isValid = false;
            } else {
                const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.webp)$/i;
                if (!allowedExtensions.exec(image)) {
                    document.getElementById('image-warning').textContent = 'Format d\'image invalide. Seuls les formats JPG, JPEG, PNG, GIF et WEBP sont acceptés.';
                    document.getElementById('image-warning').style.display = 'block';
                    isValid = false;
                }
            }

            return isValid;
        }

        // Écouteurs d'événements pour validation en temps réel
        document.getElementById('nom').addEventListener('input', () => {
            validateField('nom');
        });
        document.getElementById('description').addEventListener('input', () => {
            validateField('description');
        });
        document.getElementById('prix').addEventListener('input', () => {
            validateField('prix');
        });
        document.getElementById('quantite').addEventListener('input', () => {
            validateField('quantite');
        });
        document.getElementById('image').addEventListener('change', () => {
            validateField('image');
        });

        // Fonction pour valider un champ spécifique
        function validateField(fieldName) {
            const field = document.getElementById(fieldName);
            const warning = document.getElementById(`${fieldName}-warning`);
            let isValid = true;
            let message = '';

            switch(fieldName) {
                case 'nom':
                    const nom = field.value.trim();
                    if (nom.length < 3) {
                        message = 'Le nom du produit doit contenir au moins 3 caractères.';
                        isValid = false;
                    } else if (nom.length > 50) {
                        message = 'Le nom du produit ne doit pas dépasser 50 caractères.';
                        isValid = false;
                    }
                    break;

                case 'description':
                    const description = field.value.trim();
                    if (description.length < 10) {
                        message = 'La description doit contenir au moins 10 caractères.';
                        isValid = false;
                    } else if (description.length > 255) {
                        message = 'La description ne doit pas dépasser 255 caractères.';
                        isValid = false;
                    }
                    break;

                case 'prix':
                    const prix = parseFloat(field.value);
                    if (isNaN(prix) || prix <= 0) {
                        message = 'Le prix doit être un nombre positif.';
                        isValid = false;
                    }
                    break;

                case 'quantite':
                    const quantite = parseInt(field.value);
                    if (isNaN(quantite) || quantite < 0) {
                        message = 'La quantité doit être un nombre positif ou nul.';
                        isValid = false;
                    }
                    break;

                case 'image':
                    const image = field.value;
                    if (image) {
                        const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.webp)$/i;
                        if (!allowedExtensions.exec(image)) {
                            message = 'Format d\'image invalide. Seuls les formats JPG, JPEG, PNG, GIF et WEBP sont acceptés.';
                            isValid = false;
                        }
                    }
                    break;
            }

            warning.textContent = message;
            warning.style.display = isValid ? 'none' : 'block';
        }

        // Fonctions de recherche
        function searchProduits() {
          const searchValue = document.getElementById('searchProduits').value.toLowerCase();
          const rows = document.querySelectorAll('#produitsTable tbody tr');
          let visibleCount = 0;
          const startIndex = (<?= $pageActuelle ?> - 1) * <?= $produitsParPage ?>;
          const endIndex = startIndex + <?= $produitsParPage ?>;

          rows.forEach((row, index) => {
            const id = row.cells[0].textContent.toLowerCase();
            const nom = row.cells[1].textContent.toLowerCase();
            const description = row.cells[2].textContent.toLowerCase();
            
            if (id.includes(searchValue) || nom.includes(searchValue) || description.includes(searchValue)) {
              if (index >= startIndex && index < endIndex) {
                row.style.display = '';
              } else {
                row.style.display = 'none';
              }
            } else {
              row.style.display = 'none';
            }
          });
        }

        function searchCommandes() {
          const searchValue = document.getElementById('searchCommandes').value.toLowerCase();
          const rows = document.querySelectorAll('#commandesTable tbody tr');
          let visibleCount = 0;

          rows.forEach(row => {
            const id = row.cells[0].textContent.toLowerCase();
            const produit = row.cells[1].textContent.toLowerCase();
            const client = row.cells[2].textContent.toLowerCase();
            
            if (id.includes(searchValue) || produit.includes(searchValue) || client.includes(searchValue)) {
              if (visibleCount < <?= $commandesParPage ?>) {
                row.style.display = '';
                visibleCount++;
              } else {
                row.style.display = 'none';
              }
            } else {
              row.style.display = 'none';
            }
          });
        }

        // Fonctions de tri
        function sortProduits() {
          const sortBy = document.getElementById('sortProduits').value;
          const tbody = document.querySelector('#produitsTable tbody');
          const rows = Array.from(tbody.querySelectorAll('tr'));

          rows.sort((a, b) => {
            let aValue = '', bValue = '';
            
            switch(sortBy) {
              case 'nom':
                aValue = a.cells[1].textContent;
                bValue = b.cells[1].textContent;
                return aValue.localeCompare(bValue);
              case 'prix':
                aValue = parseFloat(a.cells[3].textContent);
                bValue = parseFloat(b.cells[3].textContent);
                return aValue - bValue;
              case 'quantite':
                aValue = parseInt(a.cells[4].textContent);
                bValue = parseInt(b.cells[4].textContent);
                return aValue - bValue;
              default:
                return 0;
            }
          });

          rows.forEach(row => tbody.appendChild(row));
        }

        function sortCommandes() {
          const sortBy = document.getElementById('sortCommandes').value;
          const tbody = document.querySelector('#commandesTable tbody');
          const rows = Array.from(tbody.querySelectorAll('tr'));

          rows.sort((a, b) => {
            let aValue = '', bValue = '';
            
            switch(sortBy) {
              case 'id':
                aValue = a.cells[0].textContent;
                bValue = b.cells[0].textContent;
                return aValue.localeCompare(bValue);
              case 'produit':
                aValue = a.cells[1].textContent;
                bValue = b.cells[1].textContent;
                return aValue.localeCompare(bValue);
              case 'client':
                aValue = a.cells[2].textContent;
                bValue = b.cells[2].textContent;
                return aValue.localeCompare(bValue);
              default:
                return 0;
            }
          });

          rows.forEach(row => tbody.appendChild(row));
        }

        // Ajout des écouteurs d'événements pour la recherche en temps réel
        document.getElementById('searchProduits').addEventListener('input', searchProduits);
        document.getElementById('searchCommandes').addEventListener('input', searchCommandes);

        // Fonction pour charger les statistiques
        async function loadProductStats() {
            try {
                const response = await fetch('get_product_stats.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                
                if (!data || data.length === 0) {
                    console.error('Aucune donnée de statistiques reçue');
                    return;
                }

                const ctx = document.getElementById('produitStats');
                if (!ctx) {
                    console.error('Canvas non trouvé');
                    return;
                }

                // Détruire le graphique existant s'il y en a un
                const existingChart = Chart.getChart(ctx);
                if (existingChart) {
                    existingChart.destroy();
                }

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.nom_produit),
                        datasets: [
                            {
                                label: 'Nombre de ventes',
                                data: data.map(item => item.nombre_ventes),
                                backgroundColor: 'rgba(229, 9, 20, 0.7)',
                                borderColor: 'rgba(229, 9, 20, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Nombre de clients uniques',
                                data: data.map(item => item.nombre_clients),
                                backgroundColor: 'rgba(255, 255, 255, 0.7)',
                                borderColor: 'rgba(255, 255, 255, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#fff'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#fff',
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: '#fff',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            title: {
                                display: false
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Erreur lors du chargement des statistiques:', error);
            }
        }

        // Charger les statistiques au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            if (document.querySelector('#commandes').classList.contains('active')) {
                loadProductStats();
            }
        });

        // Charger les statistiques quand on affiche l'onglet commandes
        document.querySelector('[data-tab="commandes"]').addEventListener('click', loadProductStats);

        // Mettre à jour le code JavaScript pour gérer l'onglet actif via l'URL
        document.addEventListener('DOMContentLoaded', function() {
            // Récupérer le paramètre tab de l'URL
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');

            // Si un onglet est spécifié dans l'URL, l'activer
            if (activeTab) {
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                const tabButton = document.querySelector(`[data-tab="${activeTab}"]`);
                if (tabButton) {
                    tabButton.classList.add('active');
                    document.getElementById(activeTab).classList.add('active');
                    
                    // Si on est sur l'onglet commandes, initialiser la carte et les statistiques
                    if (activeTab === 'commandes') {
                        if (typeof initMap === 'function') initMap();
                        if (typeof updateChart === 'function') updateChart();
                    }
                }
            }
        });

        // Mise à jour de la gestion des onglets
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'produits';

            function activateTab(tab) {
                // Activer l'onglet approprié
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                const tabButton = document.querySelector(`[data-tab="${tab}"]`);
                const tabContent = document.getElementById(tab);
                
                if (tabButton && tabContent) {
                    tabButton.classList.add('active');
                    tabContent.classList.add('active');
                    
                    if (tab === 'commandes') {
                        if (typeof initMap === 'function') setTimeout(initMap, 100);
                        if (typeof updateChart === 'function') setTimeout(updateChart, 100);
                    }
                }
            }

            // Activer l'onglet initial
            activateTab(activeTab);

            // Gestionnaire d'événements pour les clics sur les onglets
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tab = this.dataset.tab;
                    window.location.href = this.href;
                });
            });

            // Ajouter des gestionnaires d'événements aux liens de pagination
            document.querySelectorAll('.pagination-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.href = this.href;
                });
            });
        });
      </script>
    </main>
  </div>
</body>
</html>
