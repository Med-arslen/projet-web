document.addEventListener("DOMContentLoaded", () => {
    // Check if user is admin, if not redirect to login
    const user = JSON.parse(localStorage.getItem("movievibeUser"));
    if (!user || !user.isLoggedIn || user.role !== 'admin') {
        window.location.href = "login.html";
        return;
    }

    // Update welcome message with admin name
    if (document.getElementById("userName")) {
        document.getElementById("userName").textContent = user.name;
    }

    // Rest of the admin dashboard functionality
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

    // partie bienvennue
    const currentDate = new Date().toLocaleDateString('fr-FR', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
    document.getElementById("currentDate").textContent = `📅 Aujourd'hui : ${currentDate}`;
    
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

    // recherche client
    const searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("keyup", () => {
        const filter = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll("#clientTable tbody tr");
    
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
    
    // Quitter
    document.getElementById("quitBtn").addEventListener("click", () => {
        if (confirm("Êtes-vous sûr de vouloir quitter ?")) {
            localStorage.removeItem("movievibeUser");
            window.location.href = "login.html";
        }
    });
});
