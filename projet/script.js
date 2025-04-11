document.addEventListener("DOMContentLoaded", () => {
    const movies = document.querySelectorAll(".movie");

    movies.forEach(movie => {
        movie.addEventListener("mouseenter", () => {
            movie.style.transform = "scale(1.2)";
            movie.style.transition = "transform 0.3s ease-in-out";
        });

        movie.addEventListener("mouseleave", () => {
            movie.style.transform = "scale(1)";
        });
    });

    const sidebarToggle = document.querySelector(".sidebar-toggle");
    const sidebar = document.querySelector(".sidebar");

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("active");
        });
    }

    const searchBar = document.querySelector(".search-bar");
    if (searchBar) {
        searchBar.addEventListener("focus", () => {
            searchBar.style.outline = "2px solid #e50914";
        });

        searchBar.addEventListener("blur", () => {
            searchBar.style.outline = "none";
        });
    }
});
