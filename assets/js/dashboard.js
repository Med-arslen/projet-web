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

// Fonction pour afficher les détails du profil utilisateur
function showUserProfile(user) {
    // Masquer la liste des utilisateurs et afficher le profil
    document.getElementById('users').style.display = 'none';
    document.getElementById('userProfile').style.display = 'block';
    
    // Remplir les informations du profil
    document.getElementById('profileName').textContent = user.name;
    document.getElementById('profileEmail').textContent = user.email;
    document.getElementById('profilePhone').textContent = user.phone || 'Non renseigné';
    document.getElementById('profileId').textContent = user.id;
    document.getElementById('profileRole').textContent = user.role;
    document.getElementById('profileStatus').textContent = user.is_blocked ? 'Bloqué' : 'Actif';
    document.getElementById('profileCreatedAt').textContent = user.formatted_created_at;
    document.getElementById('profileUpdatedAt').textContent = user.formatted_updated_at;
}

// Fonction pour modifier le profil utilisateur
function editUserProfile() {
    const userId = document.getElementById('profileId').textContent;
    const user = {
        id: userId,
        name: document.getElementById('profileName').textContent,
        email: document.getElementById('profileEmail').textContent,
        phone: document.getElementById('profilePhone').textContent,
        role: document.getElementById('profileRole').textContent
    };
    editUser(user);
    showSection('users');
}

// Fonction pour afficher le QR code de l'utilisateur
function showUserQRCode() {
    const userId = document.getElementById('profileId').textContent;
    openQRCode(userId);
}

// Fonction pour changer le statut de l'utilisateur
function toggleUserStatus() {
    const userId = document.getElementById('profileId').textContent;
    const currentStatus = document.getElementById('profileStatus').textContent;
    const action = currentStatus === 'Bloqué' ? 'unblock' : 'block';
    
    if (confirm(`Êtes-vous sûr de vouloir ${action === 'block' ? 'bloquer' : 'débloquer'} cet utilisateur ?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="${action}">
            <input type="hidden" name="id" value="${userId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Modification de la fonction editUser existante pour inclure l'affichage du profil
function editUser(user) {
    // Remplir le formulaire comme avant
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
    
    // Ajouter un bouton pour voir le profil complet
    const formSection = document.querySelector('.form-section');
    if (!document.getElementById('viewProfileBtn')) {
        const viewProfileBtn = document.createElement('button');
        viewProfileBtn.id = 'viewProfileBtn';
        viewProfileBtn.type = 'button';
        viewProfileBtn.className = 'btn-edit';
        viewProfileBtn.innerHTML = '<i class="fas fa-user-circle"></i> Voir le profil complet';
        viewProfileBtn.onclick = () => showUserProfile(user);
        formSection.appendChild(viewProfileBtn);
    }
}
