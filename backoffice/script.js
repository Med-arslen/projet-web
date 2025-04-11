// Activer menu + animation
document.querySelectorAll(".menu a").forEach(link => {
    link.addEventListener("click", () => {
      document.querySelectorAll(".menu a").forEach(el => el.classList.remove("active"));
      link.classList.add("active");
      link.classList.add("animate-click");
      setTimeout(() => link.classList.remove("animate-click"), 500);
    });
  });
  
  // Filtrage live
  document.getElementById("searchInput").addEventListener("input", function () {
    const value = this.value.toLowerCase();
    document.querySelectorAll("#clientTable tbody tr").forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
    });
  });
  
  // Ajout client
  document.getElementById("addClientBtn").addEventListener("click", () => {
    const table = document.querySelector("#clientTable tbody");
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${Math.floor(Math.random()*9999)}</td>
      <td>Test</td>
      <td>User</td>
      <td>test@example.com</td>
      <td>${new Date().toLocaleDateString()}</td>
      <td>Standard</td>
    `;
    table.appendChild(row);
  });
  
  // Télécharger le tableau (CSV)
  document.getElementById("downloadBtn").addEventListener("click", () => {
    let csv = "ID,Nom,Prénom,Email,Date,Forfait\n";
    document.querySelectorAll("#clientTable tbody tr").forEach(row => {
      const cols = row.querySelectorAll("td");
      const line = Array.from(cols).map(td => td.textContent).join(",");
      csv += line + "\n";
    });
  
    const blob = new Blob([csv], { type: "text/csv" });
    const a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "clients.csv";
    a.click();
  });
  
  // Partager (via Web Share API ou fallback)
  document.getElementById("shareBtn").addEventListener("click", () => {
    if (navigator.share) {
      navigator.share({
        title: "Liste Clients",
        text: "Voici la liste des clients MovieVibe",
        url: window.location.href
      }).catch(console.error);
    } else {
      alert("Partage non supporté sur ce navigateur.");
    }
  });
  
  // Quitter
  document.getElementById("quitBtn").addEventListener("click", () => {
    if (confirm("Êtes-vous sûr de vouloir quitter ?")) {
      window.close(); // Ne marche pas toujours selon navigateur
    }
  });
  