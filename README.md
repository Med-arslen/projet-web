# Projet Web - Gestion des Produits et Commandes

## Description
Ce projet est une application web développée pour gérer les produits et les commandes d'une boutique en ligne. Il offre une interface utilisateur moderne et réactive, permettant une gestion efficace des produits et un suivi détaillé des commandes avec des fonctionnalités avancées de visualisation et d'analyse.

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
- Ajouter un produit avec un nom, une description, un prix, une quantité et une image
- Modifier les informations d'un produit existant
- Supprimer un produit
- Afficher la liste des produits avec leurs détails
- Recherche avancée par ID, nom et description
- Tri dynamique par nom, prix et quantité
- Validation des données côté client et serveur

### Gestion des Commandes
- Ajouter une commande pour un produit spécifique
- Afficher la liste des commandes avec les détails du client et du produit
- Recherche avancée par ID, produit et client
- Tri dynamique par ID, produit et client
- Visualisation géographique des livraisons sur une carte interactive
- Génération de rapports PDF des commandes groupées par client
- Statistiques en temps réel des produits les plus vendus
- Interface de suivi des livraisons pour les livreurs

## Nouvelles Fonctionnalités (Avril 2025)
- Carte interactive des livraisons avec regroupement des commandes par adresse
- Génération de rapports PDF personnalisés
- Graphiques statistiques des ventes
- Système de recherche et tri avancé
- Interface adaptative (responsive design)

## Fonctionnalités Futures Proposées

### Système de Gestion des Utilisateurs
- Authentication multi-niveaux (Admin, Gestionnaire, Livreur, Client)
- Tableau de bord personnalisé pour chaque type d'utilisateur
- Système de notifications en temps réel

### Amélioration de la Gestion des Commandes
- Système de suivi en temps réel des livraisons avec GPS
- Estimation du temps de livraison basée sur l'IA
- Chat en direct entre le client et le livreur
- Système de notation des livraisons
- Historique détaillé des statuts de commande

### Optimisation des Ventes
- Système de recommandation de produits basé sur l'IA
- Analyse prédictive des ventes
- Gestion automatique des stocks avec alertes
- Tableau de bord analytique avancé
- Rapports personnalisables avec export multi-format

### Interface Client
- Espace client personnalisé
- Système de favoris et liste de souhaits
- Historique des commandes interactif
- Programme de fidélité avec système de points
- Suivi des commandes en temps réel

### Gestion des Promotions
- Création de codes promo personnalisés
- Promotions temporaires avec compte à rebours
- Ventes flash automatisées
- Système de réduction par volume
- Promotions ciblées par segmentation client

### Intégration et API
- API RESTful pour intégration externe
- Synchronisation avec les plateformes de e-commerce
- Intégration des systèmes de paiement en ligne
- Connection avec les systèmes ERP
- Webhooks pour événements système

### Maintenance et Sécurité
- Sauvegarde automatique des données
- Journal d'audit détaillé
- Protection contre les attaques CSRF/XSS
- Chiffrement des données sensibles
- Conformité RGPD

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