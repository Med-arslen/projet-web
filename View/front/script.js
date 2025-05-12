document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("reclamationForm");

  if (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault(); // Empêche la soumission traditionnelle du formulaire

      // Vérifie si le formulaire a déjà été soumis
      const formSent = document.getElementById("formSent");
      if (formSent.value === "true") {
        return; // Si déjà soumis, ne rien faire
      }

      // Marque le formulaire comme soumis
      formSent.value = "true";

      const formData = new FormData(form);

      fetch('../backoffice/ajouter_rec.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Si succès, redirection vers feedback.html
          window.location.href = 'feedback.php'; // Redirection vers feedback.html
        } else {
          // Si échec, on réactive le champ pour permettre la correction
          alert(data.message);
          formSent.value = "false"; // Permet de soumettre à nouveau si nécessaire
        }
      })
      .catch(error => {
        console.error('Erreur FETCH:', error);
        alert('Une erreur est survenue. Veuillez réessayer plus tard.');
        formSent.value = "false"; // Permet de soumettre à nouveau si nécessaire
      });
    });
  }
});
