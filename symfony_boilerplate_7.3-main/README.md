# TechFlow Backoffice — Gestion d'une entreprise de produits électroniques

Backoffice Symfony 7.3 pour gérer les utilisateurs, produits (physiques et numériques) et clients d'une entreprise de vente de matériel et logiciels informatiques.

## Installation

```bash
git clone https://github.com/Yoshicolors/Projet-Symfony-Entreprise.git
cd symfony_boilerplate_7.3-main

composer install

cp .env .env.local
```

Modifiez le fichier `.env.local` pour configurer votre base de données :

```
DATABASE_URL="mysql://root:@127.0.0.1:3306/backoffice_db?serverVersion=8&charset=utf8mb4"
```

Créez la base de données et exécutez les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Chargez les données de test :

```bash
php bin/console doctrine:fixtures:load
```

Compilez Tailwind CSS :

```bash
php bin/console tailwind:build
```

Lancez le serveur :

```bash
symfony serve
```

ou

```bash
php -S localhost:8000 -t public
```

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | admin@backoffice.com | admin123 |
| Manager | manager@backoffice.com | manager123 |
| Utilisateur | user@backoffice.com | user123 |

## Fonctionnalités

### Authentification et sécurité
- Formulaire de connexion sécurisé
- 3 rôles : ROLE_USER, ROLE_MANAGER, ROLE_ADMIN
- Voters pour la gestion des accès (UserVoter, ProductVoter, ClientVoter)
- Hiérarchie des rôles (Admin > Manager > User)

### Gestion des utilisateurs (Admin uniquement)
- Liste des utilisateurs (email, rôle, nom, prénom)
- Ajout, modification (sauf mot de passe) et suppression
- Onglet visible uniquement pour les administrateurs

### Gestion des produits (visible par tous)
- Liste des produits triés par prix décroissant (requête personnalisée)
- Formulaire multi-étapes dynamique :
  - Étape 1 : Choix du type (physique / numérique)
  - Étape 2 : Détails (nom, description, prix)
  - Étape 3a : Logistique si physique (poids, dimensions, stock)
  - Étape 3b : Licence si numérique (durée, activations)
  - Étape optionnelle : Confirmation si prix > 1000 €
  - Récapitulatif avant validation
- Export CSV via un service dédié (ProductExportService)
- Import CSV via commande Symfony
- Boutons d'ajout/modification/suppression uniquement pour les admins

### Gestion des clients (Admin et Manager)
- Liste des clients avec filtrage par recherche
- Ajout et modification avec validations :
  - Format email (xxx@xxx.xx)
  - Prénom/nom sans caractères spéciaux
  - Unicité de l'email client
- Ajout de client via commande CLI
- Fixtures avec 20 clients réalistes

## Commandes disponibles

Importer des produits depuis un CSV :

```bash
php bin/console app:import-products products.csv
```

Ajouter un client de manière interactive :

```bash
php bin/console app:add-client
```

## Architecture

```
src/
├── Command/
│   ├── AddClientCommand.php
│   └── ImportProductsCommand.php
├── Controller/
│   ├── ClientController.php
│   ├── DashboardController.php
│   ├── ProductController.php
│   ├── SecurityController.php
│   └── UserController.php
├── DataFixtures/
│   ├── ClientFixtures.php
│   └── UserFixtures.php
├── Entity/
│   ├── Client.php
│   ├── Product.php
│   └── User.php
├── Form/
│   ├── ClientType.php
│   ├── UserType.php
│   └── Product/
│       └── Step/
│           ├── ProductConfirmationStepType.php
│           ├── ProductDetailsStepType.php
│           ├── ProductLicenseStepType.php
│           ├── ProductLogisticsStepType.php
│           └── ProductTypeStepType.php
├── Repository/
│   ├── ClientRepository.php
│   ├── ProductRepository.php
│   └── UserRepository.php
├── Security/
│   └── Voter/
│       ├── ClientVoter.php
│       ├── ProductVoter.php
│       └── UserVoter.php
└── Service/
    └── ProductExportService.php
```

## Technologies

- Symfony 7.3
- Doctrine ORM
- Tailwind CSS (via TailwindBundle)
- Twig
- PHP 8.2+