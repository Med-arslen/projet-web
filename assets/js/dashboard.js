document.addEventListener("DOMContentLoaded", () => {
    const user = JSON.parse(localStorage.getItem("movievibeUser"));
    const welcome = document.getElementById("userWelcome");
  
    if (user && user.name) {
      welcome.textContent = `Bienvenue, ${user.name} !`;
    } else {
      // Redirection si aucun utilisateur trouvé
      window.location.href = "login.html";
    }
  
    // Fonction de déconnexion
    document.getElementById("logoutBtn").addEventListener("click", () => {
      localStorage.removeItem("movievibeUser");
      window.location.href = "login.html"; // Redirection
    });
  });
  document.getElementById("backOfficeBtn").addEventListener("click", () => {
    window.location.href = "index.html";
  });

function editUser(user) {
    document.getElementById('userId').value = user.id;
    document.getElementById('adminId').value = user.admin_id;
    document.getElementById('userName').value = user.name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userPassword').value = '';
    document.getElementById('userPhone').value = user.phone;
    document.getElementById('userRole').value = user.role;
    document.getElementById('formAction').value = 'edit';
    
    // Afficher les dates dans le formulaire si nécessaire
    if (document.getElementById('createdAt')) {
        document.getElementById('createdAt').textContent = user.formatted_created_at;
    }
    if (document.getElementById('updatedAt')) {
        document.getElementById('updatedAt').textContent = user.formatted_updated_at;
    }
}
