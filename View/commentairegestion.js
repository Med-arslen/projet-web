document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('commentForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Stop form from submitting immediately

        // Get form fields
        const filmSelect = document.getElementById('id_film');
        const auteur = document.getElementById('auteur');
        const contenu = document.getElementById('contenu');
        const note = document.getElementById('note');

        // Clear previous errors
        clearErrors();

        // Validate fields
        let hasErrors = false;

        if (!filmSelect.value) {
            showError(filmSelect, 'Veuillez sélectionner un film');
            hasErrors = true;
        }

        if (!auteur.value.trim()) {
            showError(auteur, 'L\'auteur est requis');
            hasErrors = true;
        }

        if (!contenu.value.trim()) {
            showError(contenu, 'Le contenu est requis');
            hasErrors = true;
        }

        if (!note.value) {
            showError(note, 'La note est requise');
            hasErrors = true;
        } else if (parseInt(note.value) < 0 || parseInt(note.value) > 10) {
            showError(note, 'La note doit être entre 0 et 10');
            hasErrors = true;
        }

        // If no errors, submit the form
        if (!hasErrors) {
            form.submit();
        }
    });

    function showError(element, message) {
        const formGroup = element.closest('.form-group');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        formGroup.appendChild(errorDiv);
        element.style.borderColor = 'var(--error)';
    }

    function clearErrors() {
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(error => error.remove());
        
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => input.style.borderColor = '');
    }
});
