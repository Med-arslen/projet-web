document.getElementById("reclamationForm").addEventListener("submit", function(event) {
    let valid = true;
    let message = "";

    // Validation du nom et prénom (uniquement lettres et espaces)
    let nomprenom = document.getElementById("nomprenom").value;
    if (!nomprenom.match(/^[a-zA-Z\s]+$/)) {
        valid = false;
        message += "Le nom et prénom ne peuvent contenir que des lettres et des espaces.\n";
    }

    // Validation de l'email (format email classique)
    let email = document.getElementById("email").value;
    if (!email.match(/^[^@]+@[^@]+\.[^@]+$/)) {
        valid = false;
        message += "L'email n'est pas valide.\n";
    }

    // Validation du nom du film (pas vide)
    let nomfilm = document.getElementById("nomfilm").value;
    if (nomfilm.trim() === "") {
        valid = false;
        message += "Le nom du film est requis.\n";
    }

    // Validation du type de réclamation (sélection obligatoire)
    let type_rec = document.getElementById("type_rec").value;
    if (type_rec === "") {
        valid = false;
        message += "Le type de réclamation est requis.\n";
    }

    // Validation des détails (pas vide)
    let detail = document.getElementById("detail").value;
    if (detail.trim() === "") {
        valid = false;
        message += "Les détails sont requis.\n";
    }

    // Si validation échoue, empêcher l'envoi et afficher les messages d'erreur
    if (!valid) {
        alert(message);
        event.preventDefault(); // Empêche l'envoi du formulaire
    }
});
