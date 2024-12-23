# Création d'une API simple avec Symfony

## Description du Projet

Cette application permet de gérer des équipes, des joueurs et des scores de matchs. Elle offre une API RESTful pour effectuer des opérations CRUD sur ces entités.

## Prérequis

- PHP 8.1+
- Composer
- Symfony CLI
- MySQL ou PostgreSQL

---

## Installation

### Clonage du Projet

```bash
git clone https://github.com/CestPasFo/gestion-scores.git
cd gestion-scores
```

### Installation de composer

```bash
composer install
```

### Configuration de la base de données
Configurez le fichier ".env" avec vos paramètres de base de données
```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/gestion_score"
```

### Créez la base de données 
```bash
php bin/console doctrine:database:create
```

### Générez les migrations
```bash
php bin/console make:migration
```

### Appliquez les migrations
```bash
php bin/console doctrine:migrations:migrate
```

### Lancement du Serveur
```bash
symfony server:start
```

---

### Structure des Endpoints API
#### Équipes
```
GET /api/equipes : Lister toutes les équipes
GET /api/equipes/{id} : Obtenir une équipe spécifique
POST /api/equipes : Créer une équipe
PUT /api/equipes/{id} : Mettre à jour une équipe
DELETE /api/equipes/{id} : Supprimer une équipe
```

#### Joueurs
```
GET /api/joueurs : Lister tous les joueurs
GET /api/joueurs/{id} : Obtenir un joueur spécifique
POST /api/joueurs : Créer un joueur
PUT /api/joueurs/{id} : Mettre à jour un joueur
DELETE /api/joueurs/{id} : Supprimer un joueur
```

#### Scores
```
GET /api/scores : Lister tous les scores
GET /api/scores/{id} : Obtenir un score spécifique
POST /api/scores : Créer un score
PUT /api/scores/{id} : Mettre à jour un score
DELETE /api/scores/{id} : Supprimer un score
```

---

### Exemples de Requêtes
#### Création d'une Équipe
```json
{
  "nom": "Équipe A",
  "nbdefaite": 0,
  "nbvictoire": 0,
  "nbmatch": 0
}
```

#### Création d'un Joueur
```json
{
  "nom": "Dupont",
  "firstname": "Jean",
  "equipeId": 1
}
```

#### Création d'un Score
```json
{
  "equipeA_id": 1,
  "equipeB_id": 2,
  "score": "3-2"
}
```
## Gestion de compte

---
- **Création de compte** : Les utilisateurs peuvent s'inscrire en fournissant un email et un mot de passe. Les mots de passe sont stockés de manière sécurisée après avoir été hachés.
- **Connexion** : Les utilisateurs peuvent se connecter à leur compte en utilisant leur email et mot de passe. En cas d'erreur, un message approprié est affiché.
- **Déconnexion** : Les utilisateurs peuvent se déconnecter, ce qui les redirige vers la page de connexion.

---

### Authentification Utilisateur
#### Création d'un Compte (Register)
```json
{
  "email": "test@example.com",
  "plainPassword": "password123"
}
```

#### Connexion (Login)
```json
{
  "email": "test@example.com",
  "password": "password123"
}
```

---

### Tests
#### Lancer les tests :
```bash
php bin/phpunit
```

---

### Déploiement
#### Technologies Utilisées
Symfony 7.x
Doctrine ORM
PHP 8.1+
MySQL/PostgreSQL

---

### Auteur
Cédric J

