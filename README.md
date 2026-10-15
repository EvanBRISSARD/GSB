# GSB — Galaxy Swiss Bourdin

**Application web de gestion des visites médicales — Projet BTS SIO, option SLAM**

[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MariaDB](https://img.shields.io/badge/MariaDB-3.0.2-003545?logo=mariadb&logoColor=white)](https://mariadb.org/)
[![HTML5](https://img.shields.io/badge/HTML-5-E34F26?logo=html5&logoColor=white)](https://developer.mozilla.org/fr/docs/Web/HTML)
[![Git](https://img.shields.io/badge/Git-2.56.0.windows.2-F05032?logo=git&logoColor=white)](https://git-scm.com/)
[![GitHub](https://img.shields.io/badge/GitHub-Hébergement-181717?logo=github&logoColor=white)](https://github.com/EvanBRISSARD/GSB)

---

## Sommaire

- [Présentation](#présentation)
- [Contexte professionnel](#contexte-professionnel)
- [Analyse du besoin](#analyse-du-besoin)
- [Fonctionnalités](#fonctionnalités)
- [Technologies et versions](#technologies-et-versions)
- [Architecture du projet](#architecture-du-projet)
- [Installation et lancement](#installation-et-lancement)
- [Cadre de réalisation](#cadre-de-réalisation)
- [Auteurs](#auteurs)

---

## Présentation

**GSB (Galaxy Swiss Bourdin)** est une application web développée dans le cadre de notre formation au **BTS Services Informatiques aux Organisations (SIO), option Solutions Logicielles et Applications Métiers (SLAM)**.

Elle vise à faciliter la gestion des activités des visiteurs médicaux d'un laboratoire pharmaceutique fictif : consultation des médicaments, des praticiens et suivi des visites.

Le projet nous permet de mettre en pratique :

- le développement web (HTML, CSS) et la programmation côté serveur (PHP) ;
- la conception et l'interrogation d'une base de données relationnelle (SQL, MariaDB) ;
- l'organisation du code selon une architecture **MVC** ;
- le travail en équipe avec **Git** et **GitHub**.

**Dépôt :** [EvanBRISSARD/GSB](https://github.com/EvanBRISSARD/GSB)

---

## Contexte professionnel

Le laboratoire pharmaceutique **Galaxy Swiss Bourdin** est issu de la fusion entre le groupe américain Galaxy et le conglomérat européen Swiss Bourdin.

- **Siège administratif européen :** Paris
- **Siège social de la multinationale :** Philadelphie (Pennsylvanie, États-Unis)

Le laboratoire emploie des visiteurs médicaux chargés de rencontrer les professionnels de santé pour leur présenter ses produits et actualiser leurs connaissances. La **France** a été choisie comme territoire d'expérimentation pour améliorer le suivi et la gestion de ces visites.

---

## Analyse du besoin

Une première solution avait été développée sous **Microsoft Access en 2003**. L'objectif est de reprendre cette logique métier pour concevoir une application **accessible depuis un navigateur web**, qui doit à terme fonctionner sur ordinateur, tablette et téléphone.

Besoins identifiés :

- consulter les informations relatives aux médicaments du laboratoire ;
- accéder aux informations concernant les praticiens ;
- faciliter la préparation des visites médicales ;
- centraliser les comptes rendus de visite ;
- structurer les données pour garantir leur cohérence et leur exploitation ;
- gérer les différents niveaux hiérarchiques de la force commerciale.

---

## Fonctionnalités

### Développées / en cours d'évolution

| Fonctionnalité | Description |
| --- | --- |
| Gestion des médicaments | Consultation des informations relatives aux produits pharmaceutiques |
| Gestion des praticiens | Consultation des informations relatives aux professionnels de santé |
| Navigation | Liens permettant d'accéder aux différentes pages |
| Gestion des données | Exploitation des données enregistrées en base |

### Prévues dans le périmètre métier

- [ ] Création et consultation des comptes rendus de visite
- [ ] Association des visites aux praticiens concernés
- [ ] Gestion des produits présentés pendant les visites
- [ ] Gestion des visiteurs médicaux et de leur hiérarchie
- [ ] Authentification et sécurisation de l'accès aux données

> Les fonctionnalités prévues dépendent de l'avancement du projet et du cahier des charges : elles ne sont pas toutes déjà implémentées.

---

## Technologies et versions

| Technologie | Version utilisée | Rôle |
| --- | --- | --- |
| PHP | 8.5.10 (CLI, ZTS, OPcache activé) | Logique applicative côté serveur |
| MariaDB | Client 15.2 (voir note ci-dessous) | Base de données relationnelle |
| SQL | 3.0.2-MariaDB | Interrogation et manipulation des données |
| HTML | 5 | Structure des pages |
| CSS | — | Mise en forme de l'interface |
| Git / GitHub | git version 2.56.0.windows.2 | Gestion de versions et collaboration |
| Visual Studio Code / autres IDE | — | Édition du code |

**Environnement de développement :** Windows 64 bits.

---

## Architecture du projet

Le projet suit une architecture **MVC** (Modèle – Vue – Contrôleur) qui sépare les responsabilités et facilite la maintenance.

```text
GSB/
├── assets/       # Ressources (images, styles, etc.)
├── bdd/          # Scripts SQL et éléments liés à la base de données
├── controleur/   # Contrôleurs : reçoivent les requêtes et orchestrent la réponse
├── modele/       # Accès aux données et logique métier
├── vues/         # Pages et interfaces utilisateur
├── index.php     # Point d'entrée de l'application
├── README.md     # Documentation du projet
└── LICENSE       # Licence du dépôt
```

*L'arborescence peut évoluer au fil des développements.*

---

## Installation et lancement

### Prérequis

- **PHP 8.5** (ou version compatible) avec l'extension de connexion à la base (`mysqli` ou `pdo_mysql`, à activer dans `php.ini`)
- **MariaDB** ou MySQL
- **Git**
- Un serveur web local, ou le serveur de développement intégré à PHP

### 1. Cloner le dépôt

```bash
# En HTTPS
git clone https://github.com/EvanBRISSARD/GSB.git

# Ou en SSH (nécessite une clé SSH autorisée sur GitHub)
git clone git@github.com:EvanBRISSARD/GSB.git

cd GSB
```

### 2. Préparer la base de données

1. Démarrer le serveur MariaDB.
2. Repérer le script SQL dans le dossier `bdd/`.
3. Créer la base et importer les tables et données, par exemple :
   ```bash
   mariadb -u root -p < bdd/gsbv2026v3.sql
   ```
4. Renseigner les paramètres de connexion (hôte, base, utilisateur, mot de passe) dans le fichier de configuration utilisé par l'application.


### 3. Lancer l'application

```bash
php -S localhost:8000
```

Puis ouvrir [http://localhost:8000](http://localhost:8000) dans un navigateur.

---

## Cadre de réalisation

Projet **pédagogique** réalisé en BTS SIO, option SLAM. Il s'appuie sur un contexte professionnel fictif et sert de support pour mettre en pratique les compétences acquises en développement d'applications, bases de données et gestion de projet, ainsi que pour préparer la présentation aux épreuves du BTS.

---

## Auteurs

Projet réalisé en binôme par deux étudiants en BTS SIO — option SLAM :

| Auteur | GitHub |
| --- | --- |
| **Evan BRISSARD** | [@EvanBRISSARD](https://github.com/EvanBRISSARD) |
| **Lucas MARIQUE** | [@Lucas](https://github.com/Lucas09082007) |

---

*Projet pédagogique — BTS SIO SLAM*