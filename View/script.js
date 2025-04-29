document.addEventListener("DOMContentLoaded", function() {
    // Form validation function
    function validateForm() {
        // Get form values
        const titre = document.getElementById('titre').value.trim();
        const genre = document.getElementById('genre').value.trim();
        const annee_sortie = document.getElementById('annee_sortie').value;
        const duree = document.getElementById('duree').value;
        const age_recommande = document.getElementById('age_recommande').value;

        // Clear previous errors
        clearErrors();

        // Validation flags
        let isValid = true;
        const currentYear = new Date().getFullYear();

        // Validate titre (required, max 50 characters)
        if (!titre) {
            showError('titre', 'Le titre du film est requis');
            isValid = false;
        } else if (titre.length > 50) {
            showError('titre', 'Le titre ne doit pas dépasser 50 caractères');
            isValid = false;
        }

        // Validate genre (required, max 30 characters)
        if (!genre) {
            showError('genre', 'Le genre est requis');
            isValid = false;
        } else if (genre.length > 30) {
            showError('genre', 'Le genre ne doit pas dépasser 30 caractères');
            isValid = false;
        }

        // Validate année de sortie (1900-current year)
        if (!annee_sortie) {
            showError('annee_sortie', "L'année de sortie est requise");
            isValid = false;
        } else {
            const year = parseInt(annee_sortie);
            if (isNaN(year)) {
                showError('annee_sortie', "L'année doit être un nombre");
                isValid = false;
            } else if (year < 1900 || year > currentYear) {
                showError('annee_sortie', `L'année doit être entre 1900 et ${currentYear}`);
                isValid = false;
            }
        }

        // Validate durée (HH:MM:SS format)
        if (!duree) {
            showError('duree', 'La durée est requise');
            isValid = false;
        } else {
            // Convert time input to HH:MM:SS format for validation
            const timeParts = duree.split(':');
            if (timeParts.length < 2 || timeParts[0] > 23 || timeParts[1] > 59) {
                showError('duree', 'Format de durée invalide (HH:MM:SS)');
                isValid = false;
            }
        }

        // Validate âge recommandé (0-18)
        if (!age_recommande) {
            showError('age_recommande', "L'âge recommandé est requis");
            isValid = false;
        } else {
            const age = parseInt(age_recommande);
            if (isNaN(age)) {
                showError('age_recommande', "L'âge doit être un nombre");
                isValid = false;
            } else if (age < 0 || age > 18) {
                showError('age_recommande', "L'âge doit être entre 0 et 18 ans");
                isValid = false;
            }
        }

        return isValid;
    }

    // Function to show error message
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        // Add error class to input
        field.classList.add('error');

        // Create error message element if it doesn't exist
        let errorElement = field.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('error-message')) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            field.parentNode.insertBefore(errorElement, field.nextSibling);
        }

        // Set error message
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    // Function to clear all errors
    function clearErrors() {
        // Remove error classes
        document.querySelectorAll('.error').forEach(el => {
            el.classList.remove('error');
        });

        // Remove error messages
        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
        });
    }

    // Add event listeners to clear errors when user starts typing
    document.getElementById('titre')?.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('error');
            const errorMsg = this.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('error-message')) {
                errorMsg.style.display = 'none';
            }
        }
    });

    document.getElementById('genre')?.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('error');
            const errorMsg = this.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('error-message')) {
                errorMsg.style.display = 'none';
            }
        }
    });

    document.getElementById('annee_sortie')?.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('error');
            const errorMsg = this.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('error-message')) {
                errorMsg.style.display = 'none';
            }
        }
    });

    document.getElementById('duree')?.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('error');
            const errorMsg = this.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('error-message')) {
                errorMsg.style.display = 'none';
            }
        }
    });

    document.getElementById('age_recommande')?.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('error');
            const errorMsg = this.nextElementSibling;
            if (errorMsg && errorMsg.classList.contains('error-message')) {
                errorMsg.style.display = 'none';
            }
        }
    });

    // Form submission handler
    const form = document.forms['userForm'];
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!validateForm()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    }

    // Add CSS for error styling (in case it's not in your CSS file)
    const style = document.createElement('style');
    style.textContent = `
        .error {
            border-color: #ff3860 !important;
        }
        .error-message {
            color: #ff3860;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: none;
        }
    `;
    document.head.appendChild(style);
});