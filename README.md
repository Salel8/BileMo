# BileMo

Ce projet, réalisé avec PHP/Symfony, est une API qui permet de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue de téléphonies mobiles haut de gamme. Le projet a pour but de consulter la liste des téléphones et de consulter les informations d'un téléphone en particulier. Le projet permet aussi de consulter la liste des utilisateurs liés à un client ainsi que leurs informations, de créer un utilisateur et de les supprimer. Le site dispose donc d'un système de compte utilisateur avec un système de jeton à récupérer lors de la connexion et à fournir à chaque requête. Une base de données est nécessaire pour stocker les différentes informations sur les téléphones ainsi que sur les utilisateurs.

## Pré-requis

```php
PHP
Symfony
Postman
Git
Composer
Base de données MySQL
```

## Installation

Appuyez sur le bouton "Code" en vert, situé en haut de cette page. Choisissez entre HTTPS et SSH et copiez le nom du clone qui s'affiche. Créez un dossier où vous placerez le code du projet et ouvrez une fenêtre du terminal. Placez-vous dans ce dossier créé et clonez alors ce repository avec la commande git clone.

```bash
git clone git@github.com:Salel8/BileMo.git
```

Vous avez maintenant tout le projet en local mais avant de pouvoir l'utiliser, il vous faut créer votre base de données. Vous pouvez utiliser PHPMyAdmin pour créer votre base de données ou bien utiliser le jeu de données fourni dans le dossier data. Pour importer le jeu de données, rendez-vous dans la section "Importer" de PHPMyAdmin, sélectionnez le fichier "bilemo.sql" et appuyer sur "Exécuter".

Une fois la base de données créée, il ne vous manque plus qu'à connecter ce projet à votre base de données. Pour cela, il vous faut créez un fichier .env.local et dans ce fichier il vous faudra insérer ce qui suit :

```php
DRIVER="driver"
DBNAME="dbname"
PORT=0000
USER="user"
PASSWORD="password"
HOST="host"
```

Veillez à bien modifier les champs "driver", "dbname", le port, "user", "password" et "host" avec ceux correspondant à votre base de données. Si vous avez importé la base de données, le driver est "pdo_mysql", le dbname est "bilemo". En local, souvent, le port est 8889, le host est "127.0.0.1" et le user et le password est ceux configuré dans votre PHPMyAdmin.

Cette configuration étant établie, vous pouvez dorénavant profiter pleinement de l'ensemble du projet.

## Démarrage

Pour lancer le projet, il faut commencer par installer toutes les dépendances du projet. Pour cela, lancez le serveur PHP puis, via le terminal, placez-vous dans le dossier créé plus tôt contenant le code du projet. Puis lancez la commande :

```bash
composer install
```
Une fois cette commande réalisée, vous devez lancer le serveur de symfony avec la commande :

```bash
symfony server:start
```

Puis, ouvrez Postman et effectuez des requêtes pour utiliser l'API. 

Pour trouver les différentes requêtes réalisables, vous pouvez consulter la documentation à l'adresse [http://127.0.0.1:8000/api/doc](http://127.0.0.1:8000/api/doc) .


## Fabriqué avec 

HTML - CSS - Twig

PHP - Symfony

PHPMyAdmin - MySQL

Git - Composer

VSCode - Postman

## Versions

PHP 8.2.10

Symfony 6.3

## Auteur

Samir Mehal