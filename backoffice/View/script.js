document.addEventListener("DOMContentLoaded", () => {
    const formContainer = document.getElementById("filmForm");
    const tableBody = document.querySelector("#filmsTable tbody");
    const message = document.getElementById("filmError");
    const addFilmBtn = document.getElementById("addFilmBtn");
    const cancelFilmBtn = document.getElementById("cancelFilm");
    const submitFilmBtn = document.getElementById("submitFilm");

    addFilmBtn.addEventListener("click", () => {
        formContainer.style.display = "block";
        resetForm();
    });

    cancelFilmBtn.addEventListener("click", () => {
        formContainer.style.display = "none";
        resetForm();
    });

    submitFilmBtn.addEventListener("click", (e) => {
        e.preventDefault();

        const film = {
            id_film: document.getElementById("filmId").value || null,
            titre: document.getElementById("titre").value.trim(),
            genre: document.getElementById("genre").value.trim(),
            anne_sortie: parseInt(document.getElementById("anne_sortie").value),
            duree: document.getElementById("duree").value,
            age_recommande: parseInt(document.getElementById("age_recommande").value)
        };

        const erreur = validerFilm(film);
        if (erreur) {
            afficherErreur(erreur);
            return;
        }

        const action = film.id_film ? "modifier" : "ajouter";
        envoyerRequete(action, film);
    });

    function validerFilm(film) {
        const currentYear = new Date().getFullYear();
        const dureeRegex = /^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/;

        if (!film.titre || film.titre.length > 20)
            return "Le titre est requis et doit avoir moins de 20 caractères.";
        if (!film.genre || film.genre.length > 20)
            return "Le genre est requis et doit avoir moins de 20 caractères.";
        if (!film.anne_sortie || film.anne_sortie < 1888 || film.anne_sortie > currentYear)
            return "Année invalide.";
        if (!film.duree || !dureeRegex.test(film.duree))
            return "Durée invalide (format HH:MM:SS).";
        if (film.age_recommande < 0 || film.age_recommande > 18)
            return "Âge recommandé entre 0 et 18.";

        return null;
    }

    function envoyerRequete(action, film) {
        fetch("film.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action, film })
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            resetForm();
            formContainer.style.display = "none";
            chargerFilms();
            afficherMessage(`Film ${action === 'ajouter' ? 'ajouté' : 'modifié'} avec succès!`);
        })
        .catch(e => afficherErreur(e.message));
    }

    function chargerFilms() {
        fetch("film.php?action=lire")
            .then(r => r.json())
            .then(films => {
                tableBody.innerHTML = "";
                films.forEach(film => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${film.id_film}</td>
                        <td>${film.titre}</td>
                        <td>${film.genre}</td>
                        <td>${film.anne_sortie}</td>
                        <td>${film.duree}</td>
                        <td>${film.age_recommande}</td>
                        <td>
                            <button onclick='editFilm(${JSON.stringify(film)})'>✏️</button>
                            <button onclick='deleteFilm(${film.id_film})'>🗑️</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            });
    }

    function resetForm() {
        document.getElementById("filmId").value = "";
        document.getElementById("titre").value = "";
        document.getElementById("genre").value = "";
        document.getElementById("anne_sortie").value = "";
        document.getElementById("duree").value = "";
        document.getElementById("age_recommande").value = "";
        message.style.display = "none";
    }

    function afficherErreur(msg) {
        message.textContent = msg;
        message.style.display = "block";
        message.style.color = "#dc3545";
    }

    function afficherMessage(msg) {
        message.textContent = msg;
        message.style.display = "block";
        message.style.color = "#28a745";
        setTimeout(() => message.style.display = "none", 3000);
    }

    window.editFilm = function(film) {
        document.getElementById("filmId").value = film.id_film;
        document.getElementById("titre").value = film.titre;
        document.getElementById("genre").value = film.genre;
        document.getElementById("anne_sortie").value = film.anne_sortie;
        document.getElementById("duree").value = film.duree;
        document.getElementById("age_recommande").value = film.age_recommande;
        formContainer.style.display = "block";
    };

    window.deleteFilm = function(id) {
        if (confirm("Supprimer ce film ?")) {
            fetch("film.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "supprimer", id })
            })
            .then(r => r.json())
            .then(() => {
                chargerFilms();
                afficherMessage("Film supprimé !");
            });
        }
    };

    chargerFilms();
});
