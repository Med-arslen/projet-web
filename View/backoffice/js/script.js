// Script principal pour la gestion des réclamations

// Activation du menu + animation + détection automatique
document.addEventListener("DOMContentLoaded", () => {
  const menuLinks = document.querySelectorAll(".menu a");
  const currentPath = window.location.pathname;

  menuLinks.forEach(link => {
    // Animation au clic
    link.addEventListener("click", () => {
      menuLinks.forEach(el => el.classList.remove("active"));
      link.classList.add("active", "animate-click");
      setTimeout(() => link.classList.remove("animate-click"), 500);
    });

    // Activation automatique selon l'URL
    const href = link.getAttribute("href");
    if (href && currentPath.endsWith(href)) {
      menuLinks.forEach(el => el.classList.remove("active"));
      link.classList.add("active");
    }
  });
});

// Filtrage live dans la table des réclamations ou feedbacks
const searchInput = document.getElementById("searchEventInput");
if (searchInput) {
  searchInput.addEventListener("input", function () {
    const value = this.value.toLowerCase();
    document.querySelectorAll("#eventTable tbody tr").forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
    });
  });
}

// Toggle du sous-menu dans la sidebar
function toggleSubMenu(element) {
  const submenu = element.nextElementSibling;
  if (submenu && submenu.classList.contains("submenu")) {
    const isVisible = submenu.style.display === "block";
    submenu.style.display = isVisible ? "none" : "block";

    const icon = element.querySelector(".submenu-icon");
    if (icon) icon.style.transform = isVisible ? "rotate(0deg)" : "rotate(180deg)";
  }
}

// Ajout fictif d'une réclamation (démo bouton "Nouveaux Événements")
const newEventBtn = document.getElementById("newEventBtn");
if (newEventBtn) {
  newEventBtn.addEventListener("click", () => {
    const table = document.querySelector("#eventTable tbody");
    if (!table) return;

    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${Math.floor(Math.random() * 9999)}</td>
      <td>Type de Réclamation</td>
      <td>${new Date().toLocaleDateString()}</td>
      <td>Aucune réponse</td>
    `;
    table.appendChild(row);
  });
}

// Télécharger les données du tableau en CSV
const downloadBtn = document.getElementById("downloadBtn");
if (downloadBtn) {
  downloadBtn.addEventListener("click", () => {
    let csv = "ID,Type de Réclamation,Date,Réponse\n";
    document.querySelectorAll("#eventTable tbody tr").forEach(row => {
      const cols = row.querySelectorAll("td");
      const line = Array.from(cols).map(td => td.textContent).join(",");
      csv += line + "\n";
    });

    const blob = new Blob([csv], { type: "text/csv" });
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "reclamations.csv";
    a.click();
  });
}

// Partage via Web Share API
const shareBtn = document.getElementById("shareBtn");
if (shareBtn) {
  shareBtn.addEventListener("click", () => {
    if (navigator.share) {
      navigator.share({
        title: "Liste des Réclamations",
        text: "Voici la liste des réclamations sur MovieVibe",
        url: window.location.href
      }).catch(console.error);
    } else {
      alert("Partage non supporté sur ce navigateur.");
    }
  });
}

// Quitter l'application
const quitBtn = document.getElementById("quitBtn");
if (quitBtn) {
  quitBtn.addEventListener("click", () => {
    if (confirm("Êtes-vous sûr de vouloir quitter ?")) {
      window.close(); // Peut ne pas fonctionner selon navigateur
    }
  });
}

// Initialisation et gestion du QR code
let qrModal;

document.addEventListener('DOMContentLoaded', function() {
    qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    console.log('Modal QR code initialisé');
});

async function showQRCode(id) {
    try {
        if (!qrModal) {
            qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
        }
        
        const qrContainer = document.getElementById('qrCodeContainer');
        const qrError = document.getElementById('qrError');
        
        // Afficher le modal avec le spinner
        qrModal.show();
        qrContainer.innerHTML = `
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
        `;
        qrError.style.display = 'none';

        // Faire la requête
        const response = await fetch(`index.php?qrcode_id=${id}`);
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.error || 'Erreur lors de la génération du QR code');
        }

        // Afficher uniquement le QR code
        qrContainer.innerHTML = `
            <div class="text-center">
                <img src="${data.qr_url}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                <p class="mt-3 text-muted">Scannez ce QR code pour voir les détails de la réclamation</p>
            </div>
        `;
    } catch (error) {
        console.error('Erreur:', error);
        const qrError = document.getElementById('qrError');
        qrError.textContent = `Erreur: ${error.message}`;
        qrError.style.display = 'block';
        qrContainer.innerHTML = '';
    }
}
