# Projet Web - Gestion des Produits et Commandes

## Description
Ce projet est une application web développée pour gérer les produits et les commandes. Il permet d'ajouter, de modifier, de supprimer et de consulter des produits et des commandes via une interface utilisateur simple et intuitive.

## Structure du Projet
Le projet est organisé comme suit :

- **config.php** : Fichier de configuration contenant les paramètres de connexion à la base de données.
- **Controller/** : Contient les contrôleurs pour gérer la logique métier.
  - `CommandeController.php` : Gère les opérations liées aux commandes.
  - `ProduitController.php` : Gère les opérations liées aux produits.
- **Model/** : Contient les modèles représentant les entités de l'application.
  - `Commande.php` : Modèle pour les commandes.
  - `Produit.php` : Modèle pour les produits.
- **uploads/** : Répertoire pour stocker les fichiers téléchargés, comme les images des produits.
- **View/** : Contient les fichiers de vue pour l'interface utilisateur.
  - `index.php` : Page principale pour gérer les produits et les commandes.
  - `addCommande.php` : Formulaire pour ajouter une commande.
  - `updateproduit.php` : Formulaire pour modifier un produit.
  - `deleteproduit.php` : Page pour supprimer un produit.
  - `commandes.php` : Liste des commandes.
  - `style.css` : Feuille de style pour l'interface utilisateur.
  - `script.js` : Scripts JavaScript pour les interactions dynamiques.

## Fonctionnalités

### Gestion des Produits
- Ajouter un produit avec un nom, une description, un prix, une quantité et une image.
- Modifier les informations d'un produit existant.
- Supprimer un produit.
- Afficher la liste des produits avec leurs détails.

### Gestion des Commandes
- Ajouter une commande pour un produit spécifique.
- Afficher la liste des commandes avec les détails du client et du produit.

## Installation
1. Clonez le dépôt Git :
   ```bash
   git clone https://github.com/Med-arslen/projet-web.git
   ```
2. Placez le projet dans le répertoire racine de votre serveur web (par exemple, `htdocs` pour XAMPP).
3. Configurez la base de données dans le fichier `config.php`.
4. Importez le fichier SQL fourni pour créer les tables nécessaires.

## Utilisation
1. Accédez à l'application via votre navigateur à l'adresse : `http://localhost/projetweb/View/index.php`.
2. Utilisez l'interface pour gérer les produits et les commandes.

## Développement
- Branche principale : `main`
- Branche de fonctionnalité : `ProduitCommande`

## Auteur
Ce projet a été développé par Med Arslen.