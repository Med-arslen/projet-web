document.addEventListener("DOMContentLoaded", () => {
  const stars = document.querySelectorAll(".star-rating i");
  const form = document.getElementById("feedbackForm");
  let selectedRating = 0;

  // Gestion des étoiles
  stars.forEach((star, index) => {
    star.addEventListener("click", () => {
      selectedRating = index + 1;
      stars.forEach((s, i) => {
        s.classList.toggle("fa-solid", i < selectedRating);
        s.classList.toggle("fa-regular", i >= selectedRating);
      });
      document.getElementById("noteError").style.display = "none";
    });
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    let valid = true;

    // Vérifier la note
    if (selectedRating === 0) {
      document.getElementById("noteError").style.display = "block";
      valid = false;
    }

    // Vérifier simplicité
    const simplicite = form.querySelector('input[name="simplicite"]:checked');
    if (!simplicite) {
      document.getElementById("simpliciteError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("simpliciteError").style.display = "none";
    }

    // Vérifier temps
    const temps = form.querySelector('input[name="temps"]:checked');
    if (!temps) {
      document.getElementById("tempsError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("tempsError").style.display = "none";
    }

    // Vérifier commentaire
    const commentaire = document.getElementById("commentaire").value.trim();
    if (commentaire === "") {
      document.getElementById("commentaireError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("commentaireError").style.display = "none";
    }

    // Si tout est valide, rediriger vers la page de confirmation
    if (valid) {
      console.log("Formulaire validé, redirection vers merci.html");
      window.location.href = "merci.html"; // Redirection vers la page de confirmation
    } else {
      console.log("Formulaire invalide");
    }
  });
});
