<?php
include '../config.php';
include '../Controller/CommandeController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_client = $_POST['nom_client'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $cart = json_decode($_POST['cart'], true);

    $controller = new CommandeController($pdo);

    foreach ($cart as $item) {
        $controller->createCommande($item['id'], $nom_client, $adresse, $item['quantity']);
    }

    header('Location: page.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ajouter une Commande</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
</head>
<body>
  <div class="wrapper">
    <main class="main-content">
      <section class="commandes-section">
        <div class="content-header">
          <h1>Ajouter une Commande</h1>
          <p class="subtitle">Veuillez remplir les informations ci-dessous</p>
        </div>

        <form method="POST">
          <div class="form-grid">
            <div class="form-group">
              <label for="nom_client">Nom</label>
              <input type="text" name="nom_client" id="nom_client" required>
            </div>
            <div class="form-group">
              <label for="adresse">Adresse</label>
              <textarea name="adresse" id="adresse" required></textarea>
            </div>
            <div class="form-group">
              <label for="map">Emplacement</label>
              <div id="map" style="height: 400px; width: 100%;"></div>
              <p id="address" style="margin-top: 10px; font-weight: bold;"></p>
              <input type="hidden" name="latitude" id="latitude">
              <input type="hidden" name="longitude" id="longitude">
            </div>
          </div>
          <input type="hidden" name="cart" id="cart-data">
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Confirmer</button>
          </div>
        </form>
      </section>
    </main>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
  <script>
    // Inject cart data into the hidden input field
    const cart = localStorage.getItem('cart');
    document.getElementById('cart-data').value = cart;

    async function getAddress(lat, lng) {
      try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
        const data = await response.json();
        return data.display_name || 'Adresse introuvable';
      } catch (error) {
        console.error('Erreur lors de la récupération de l\'adresse:', error);
        return 'Erreur lors de la récupération de l\'adresse';
      }
    }

    // Initialize the map
    const map = L.map('map').setView([33.8869, 9.5375], 7); // Centered on Tunisia

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(map);

    // Add a marker on click
    let marker;
    map.on('click', async function(e) {
      const { lat, lng } = e.latlng;

      // Update marker position
      if (marker) {
        marker.setLatLng([lat, lng]);
      } else {
        marker = L.marker([lat, lng]).addTo(map);
      }

      // Update hidden input fields
      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lng;

      // Fetch and display the address
      const address = await getAddress(lat, lng);
      document.getElementById('address').textContent = address;
    });
  </script>
</body>
</html>