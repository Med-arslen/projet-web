document.addEventListener("DOMContentLoaded", () => {
    const formContainer = document.getElementById("produitForm");
    const tableBody = document.querySelector("#produitsTable tbody");
    const message = document.getElementById("produitError");
    const addProduitBtn = document.getElementById("addProduitBtn");
    const cancelProduitBtn = document.getElementById("cancelProduit");
    const submitProduitBtn = document.getElementById("submitProduit");

    addProduitBtn.addEventListener("click", () => {
        formContainer.style.display = "block";
        resetForm();
    });

    cancelProduitBtn.addEventListener("click", () => {
        formContainer.style.display = "none";
        resetForm();
    });

    submitProduitBtn.addEventListener("click", (e) => {
        e.preventDefault();

        const produit = {
            id_produit: document.getElementById("produitId").value || null,
            nom: document.getElementById("nom").value.trim(),
            description: document.getElementById("description").value.trim(),
            prix: parseFloat(document.getElementById("prix").value),
            quantite: parseInt(document.getElementById("quantite").value),
            image: document.getElementById("image").files[0]
        };

        const erreur = validerProduit(produit);
        if (erreur) {
            afficherErreur(erreur);
            return;
        }

        const action = produit.id_produit ? "modifier" : "ajouter";
        envoyerRequete(action, produit);
    });

    function validerProduit(produit) {
        if (!produit.nom || produit.nom.length > 100)
            return "Le nom est requis et doit avoir moins de 100 caractères.";
        if (!produit.description)
            return "La description est requise.";
        if (!produit.prix || produit.prix <= 0)
            return "Le prix doit être supérieur à 0.";
        if (!produit.quantite || produit.quantite < 0)
            return "La quantité doit être un nombre positif.";

        return null;
    }

    function envoyerRequete(action, produit) {
        const formData = new FormData();
        formData.append("action", action);
        formData.append("id_produit", produit.id_produit);
        formData.append("nom", produit.nom);
        formData.append("description", produit.description);
        formData.append("prix", produit.prix);
        formData.append("quantite", produit.quantite);
        if (produit.image) {
            formData.append("image", produit.image);
        }

        fetch("produit.php", {
            method: "POST",
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            resetForm();
            formContainer.style.display = "none";
            chargerProduits();
            afficherMessage(`Produit ${action === 'ajouter' ? 'ajouté' : 'modifié'} avec succès!`);
        })
        .catch(e => afficherErreur(e.message));
    }

    function chargerProduits() {
        fetch("produit.php?action=lire")
            .then(r => r.json())
            .then(produits => {
                tableBody.innerHTML = "";
                produits.forEach(produit => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${produit.id_produit}</td>
                        <td>${produit.nom}</td>
                        <td>${produit.description}</td>
                        <td>${produit.prix}</td>
                        <td>${produit.quantite}</td>
                        <td><img src="../uploads/${produit.image}" alt="Image du produit" width="50"></td>
                        <td>
                            <button onclick='editProduit(${JSON.stringify(produit)})'>✏️</button>
                            <button onclick='deleteProduit(${produit.id_produit})'>🗑️</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            });
    }

    function resetForm() {
        document.getElementById("produitId").value = "";
        document.getElementById("nom").value = "";
        document.getElementById("description").value = "";
        document.getElementById("prix").value = "";
        document.getElementById("quantite").value = "";
        document.getElementById("image").value = "";
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

    window.editProduit = function(produit) {
        document.getElementById("produitId").value = produit.id_produit;
        document.getElementById("nom").value = produit.nom;
        document.getElementById("description").value = produit.description;
        document.getElementById("prix").value = produit.prix;
        document.getElementById("quantite").value = produit.quantite;
        formContainer.style.display = "block";
    };

    window.deleteProduit = function(id) {
        if (confirm("Supprimer ce produit ?")) {
            fetch("produit.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "supprimer", id })
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                chargerProduits();
                afficherMessage("Produit supprimé !");
            })
            .catch(e => afficherErreur("Erreur lors de la suppression : " + e.message));
        }
    };

    chargerProduits();
});
