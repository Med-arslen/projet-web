document.addEventListener("DOMContentLoaded", () => {
    // Add scroll event listener for header background
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Initialize all sliders
    const sliders = document.querySelectorAll('.movie-row');
    
    sliders.forEach(slider => {
        const slideContainer = slider.querySelector('.movie-slider');
        const prevButton = slider.querySelector('.prev');
        const nextButton = slider.querySelector('.next');
        const scrollAmount = slideContainer.clientWidth - 60;

        if (prevButton && nextButton) {
            // Next button click handler
            nextButton.addEventListener('click', () => {
                slideContainer.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });

            // Previous button click handler
            prevButton.addEventListener('click', () => {
                slideContainer.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });

            // Show/hide arrows based on scroll position
            slideContainer.addEventListener('scroll', () => {
                const isAtStart = slideContainer.scrollLeft === 0;
                const isAtEnd = slideContainer.scrollLeft + slideContainer.clientWidth >= slideContainer.scrollWidth - 10;

                prevButton.style.opacity = isAtStart ? '0' : '1';
                nextButton.style.opacity = isAtEnd ? '0' : '1';
            });

            // Initial arrow visibility
            prevButton.style.opacity = '0';
        }
    });

    // Enhanced Movie hover effects
    const movies = document.querySelectorAll('.movie-item');
    
    movies.forEach(movie => {
        let timeoutId;
        
        movie.addEventListener('mouseenter', () => {
            clearTimeout(timeoutId);
            // Reset all other movies
            movies.forEach(m => {
                if (m !== movie) {
                    m.style.transform = 'scale(1)';
                    m.style.zIndex = '1';
                }
            });
            
            movie.style.transform = 'scale(1.3)';
            movie.style.zIndex = '10';
            
            // Show movie info with delay
            const movieInfo = movie.querySelector('.movie-info');
            if (movieInfo) {
                movieInfo.style.bottom = '0';
            }
        });

        movie.addEventListener('mouseleave', () => {
            timeoutId = setTimeout(() => {
                movie.style.transform = 'scale(1)';
                movie.style.zIndex = '1';
                
                // Hide movie info
                const movieInfo = movie.querySelector('.movie-info');
                if (movieInfo) {
                    movieInfo.style.bottom = '-100%';
                }
            }, 200);
        });
    });

    // Handle comment form submissions
    const commentForms = document.querySelectorAll('.comment-form');
    commentForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const textInput = form.querySelector('input[type="text"]');
            const ratingInput = form.querySelector('.rating-input input');
            
            if (textInput.value.trim() && ratingInput.value) {
                // Add the new comment to the comments section
                const commentsContainer = form.closest('.comment-section').querySelector('.comments');
                const newComment = document.createElement('div');
                newComment.className = 'comment';
                newComment.innerHTML = `
                    <div class="comment-header">
                        <span class="user">User</span>
                        <span class="rating">${ratingInput.value} <i class="fas fa-star"></i></span>
                    </div>
                    <p>${textInput.value}</p>
                `;
                commentsContainer.appendChild(newComment);

                // Clear the form
                textInput.value = '';
                ratingInput.value = '5';

                // Scroll to the new comment
                commentsContainer.scrollTop = commentsContainer.scrollHeight;
            }
        });
    });

    // Add rating input validation
    const ratingInputs = document.querySelectorAll('.rating-input input');
    ratingInputs.forEach(input => {
        input.addEventListener('change', () => {
            const value = parseInt(input.value);
            if (value < 1) input.value = 1;
            if (value > 5) input.value = 5;
        });
    });

    // Handle movie card hover effects
    const movieCards = document.querySelectorAll('.movie-card');
    movieCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            const otherCards = document.querySelectorAll('.movie-card:not(:hover)');
            otherCards.forEach(other => {
                other.style.transform = 'scale(0.95)';
                other.style.opacity = '0.7';
            });
        });

        card.addEventListener('mouseleave', () => {
            const allCards = document.querySelectorAll('.movie-card');
            allCards.forEach(other => {
                other.style.transform = '';
                other.style.opacity = '';
            });
        });
    });

    // Add smooth scrolling to navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const section = document.querySelector(this.getAttribute('href'));
            if (section) {
                section.scrollIntoView({
                    behavior: 'smooth'
                });
            }
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
