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
