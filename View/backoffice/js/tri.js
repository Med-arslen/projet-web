// Ajouter un événement de clic pour le bouton "Trier du plus grand au plus petit"
document.getElementById('descButton').addEventListener('click', function(event) {
    event.preventDefault(); // Empêche l'envoi du formulaire
    fetchReclamations('desc'); // Tri par ordre décroissant
});

// Fonction pour récupérer les réclamations triées via AJAX
function fetchReclamations(order) {
    // Envoi de la requête AJAX pour récupérer les réclamations triées
    fetch('tri.php?sort=' + order + '&ajax=1')
        .then(response => response.json()) // Traiter la réponse JSON
        .then(data => {
            // Vider le corps du tableau avant de le remplir avec les nouvelles données
            let tableBody = document.querySelector('table tbody');
            tableBody.innerHTML = ''; // Vider le tableau existant

            // Parcourir les réclamations et ajouter une ligne pour chaque réclamation
            data.forEach(reclamationn => {
                let row = document.createElement('tr');
                row.innerHTML = `
                    <td>${reclamationn.id_rec}</td>
                    <td>${reclamationn.nomprenom}</td>
                    <td>${reclamationn.email}</td>
                    <td>${reclamationn.nomfilm}</td>
                    <td>${reclamationn.type_rec}</td>
                    <td>${reclamationn.detail}</td>
                    <td>${reclamationn.reponse_rec}</td>
                `;
                tableBody.appendChild(row); // Ajouter la ligne au tableau
            });
        })
        .catch(error => {
            // Gérer les erreurs et les afficher dans la console
            console.error('Erreur:', error);
        });
}
