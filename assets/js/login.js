document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const errorDiv = document.getElementById('loginError');

    // Check if user is already logged in
    const user = JSON.parse(localStorage.getItem('movievibeUser'));
    if (user && user.isLoggedIn) {
        if (user.role === 'admin') {
            window.location.href = 'index.html';
        } else {
            window.location.href = 'page.html';
        }
    }

    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Get form values
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        // Clear previous error
        errorDiv.style.display = 'none';

        // Check admin credentials
        if (email === 'support@1piece.tn' && password === 'faresxemna') {
            // Store admin session
            const adminUser = {
                name: 'Admin',
                email: email,
                role: 'admin',
                isLoggedIn: true
            };
            localStorage.setItem('movievibeUser', JSON.stringify(adminUser));
            
            // Redirect to admin dashboard
            window.location.href = 'index.html';
        } else {
            // Show error for invalid credentials
            errorDiv.textContent = 'Invalid email or password';
            errorDiv.style.display = 'block';
            // Clear password field
            document.getElementById('loginPassword').value = '';
        }
    });
});
