document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');

    form.addEventListener('submit', function (e) {
        const nomprenom = form.nomprenom.value.trim();
        const email = form.email.value.trim();
        const nomfilm = form.nomfilm.value.trim();
        const type_rec = form.type_rec.value;
        const detail = form.detail.value.trim();

        let isValid = true;
        let message = "";

        // Vérification du nom/prénom
        if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(nomprenom)) {
            isValid = false;
            message += "Le nom et prénom ne doivent contenir que des lettres et des espaces.\n";
        }

        // Vérification de l'email
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            isValid = false;
            message += "Veuillez entrer une adresse email valide.\n";
        }

        // Vérification du nom du film
        if (!/^[\w\sÀ-ÿ'-]+$/.test(nomfilm)) {
            isValid = false;
            message += "Le nom du film ne doit contenir que des lettres, chiffres et espaces.\n";
        }

        // Vérification du type de problème
        if (!type_rec) {
            isValid = false;
            message += "Veuillez sélectionner un type de problème.\n";
        }

        // Vérification du détail
        if (detail.length < 10) {
            isValid = false;
            message += "Le détail doit contenir au moins 10 caractères.\n";
        }

        // Si erreur, on empêche l'envoi et affiche un message
        if (!isValid) {
            e.preventDefault();
            alert(message);
        }
    });
});
