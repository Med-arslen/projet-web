document.addEventListener("DOMContentLoaded", () => {
    // Header scroll effect
    const header = document.querySelector("header");
    window.addEventListener("scroll", () => {
        if (window.scrollY > 100) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    });

    // Movie hover effects
    const movies = document.querySelectorAll(".box a");
    movies.forEach(movie => {
        movie.addEventListener("mouseenter", () => {
            movie.style.transform = "scale(1.1)";
            movie.style.zIndex = "2";
            movie.style.transition = "all 0.3s ease";
        });

        movie.addEventListener("mouseleave", () => {
            movie.style.transform = "scale(1)";
            movie.style.zIndex = "1";
        });
    });

    // Search functionality
    const searchIcon = document.querySelector(".fa-search");
    const searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.placeholder = "Titles, people, genres";
    searchInput.classList.add("search-input");
    
    if (searchIcon) {
        searchIcon.parentElement.addEventListener("click", (e) => {
            e.preventDefault();
            const nav = document.querySelector(".sub-nav");
            if (!nav.contains(searchInput)) {
                searchIcon.parentElement.after(searchInput);
                searchInput.focus();
            } else {
                searchInput.remove();
            }
        });
    }

    // Check if user is logged in
    const user = JSON.parse(localStorage.getItem("movievibeUser"));
    const welcomeUser = document.getElementById("welcomeUser");
    const userBanner = document.querySelector(".user-banner");
    const logoutBtn = document.getElementById("logoutBtn");

    if (user && user.isLoggedIn) {
        if (welcomeUser) {
            welcomeUser.textContent = `Bienvenue ${user.name} !`;
        }
        if (userBanner) {
            userBanner.style.display = "flex";
        }
        if (logoutBtn) {
            logoutBtn.style.display = "block";
        }
    } else {
        if (userBanner) {
            userBanner.style.display = "none";
        }
        if (logoutBtn) {
            logoutBtn.style.display = "none";
        }
    }

    // Handle logout
    if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
            localStorage.removeItem("movievibeUser");
            window.location.href = "login.html";
        });
    }
});

