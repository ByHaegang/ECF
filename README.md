
# Application Web - Vite & Gourmand 
Projet d'application web sécurisée réalisé dans le cadre de l'ECF Studi. L'application 
permet à l'entreprise « Vite & Gourmand » d'exposer ses menus et de gérer les 
commandes en ligne selon 4 rôles d'utilisateurs (Visiteur, Client, Employé, 
Administrateur). 

## Stack technique 
* **Backend :** PHP (v8.2+) / Symfony (structure, routing, contrôleurs, Twig) 
* **Accès BDD :** PDO (PHP Data Objects) avec requêtes préparées 
* **Base de données SQL :** PostgreSQL  
* **Base de données NoSQL :** MongoDB (gestion des logs et fiches spécifiques) 
* **Frontend :** HTML, CSS, JavaScript 
* **Outils & Déploiement :** VS Code, Git / GitKraken, Trello (Kanban), Heroku

## Installation de l'environnement de développement 
Pour installer et faire tourner le projet sur votre machine locale, suivez les étapes ci
dessous. 

### 1. Prérequis 
Assurez-vous d'avoir installé sur votre machine : 
* PHP (v8.2 ou supérieure) 
* Composer 
* Node.js & NPM (ou Volta) 
* Un serveur de base de données local (Apache, PostgreSQL) 
* Symfony CLI 
Page 5 sur 22 
©Studi - Reproduction interdite
                                  
### 2. Installation de l'environnement de développement (Windows) 
Si les outils ne sont pas encore installés sur votre machine, suivez les étapes ci-dessous 
sous Windows. 
1. Téléchargez **PHP 8.2+** sur 
[windows.php.net](https://windows.php.net/download/). 
2. Extrayez l'archive dans un dossier et ajoutez ce chemin à vos « variables 
d'environnement Windows ». 
Dans votre fichier `php.ini`, retirez le `;` devant les extensions suivantes : 
extension=pdo_pgsql 
extension=pgsql 
extension=mbstring 
extension=intl 
extension=openssl 
extension=curl 
3. Téléchargez et exécutez l'installeur Composer-Setup.exe depuis getcomposer.org 
4. Téléchargez l'installeur volta depuis le site officiel volta.sh 
5. Installez Node.js en exécutant la commande suivante : 
volta install node@latest 
6. Téléchargez l'installeur Windows depuis symfony.com/download puis lancer 
l’exécutable. 
7. Téléchargez et exécutez l'installeur PostgreSQL (version 15+) depuis postgresql.org. 
Laissez les composants par défaut cochés (dont pgAdmin 4).

### 3. Récupération et installation du projet 
1. **Cloner le repository Git :** 
git clone https://github.com/ByHaegang/ECF.git](https://github.com/ByHaegang/ECF.git)  
cd ECF

### 4. Installation des dépendances 
Entrez les lignes suivantes dans votre terminal : 
composer install 
npm install 
npm run build 
Configurez les variables d'environnement : 
copy .env .env.local 
Page 6 sur 22 
©Studi - Reproduction interdite                                    
Ouvrez .env.local, trouvez la ligne « DATABASE_URL = » et remplacez la avec la ligne ci
dessous avec votre mot de passe postgreSQL: 
DATABASE_URL="postgresql://postgres:VOTRE_MOT_DE_PASSE@127.0.0.1:5432/vite_g
ourmand?serverVersion=15&charset=utf8" 
MONGODB_URL="mongodb://127.0.0.1:27017/vite_gourmand_logs" 
Initialisez la base de données PostgreSQL : 
psql -U postgres -c "CREATE DATABASE vite_gourmand;" 
psql -U postgres -d vite_gourmand -f data.sql 
Entrez cette ligne dans votre terminal pour lancer votre server symfony local : 
symfony server:start 
Le site est accessible en local à l’adresse suivante : 
http://127.0.0.1:8000 
