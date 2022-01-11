Projet d'API sous Symfony 5.4

# Environnement

PHP 8.1.1
Symfony 5.4
MariaDB 10.6.5

# Base de données

Deux scripts SQL sont présent à la racine du projet dans le dossier "db". Il s'agit de la base de développement, et celle utilisée pour les tests. Les deux sont à importer.

# Exemples d'appels API

------------------------------------
REGISTER | Type POST:

JSON en entrée

{
	"name": "test",
	"email": "test@test.com",
	"password": "test"
}
------------------------------------

------------------------------------
LOGIN | Type POST:

JSON en entrée

{
	"email": "test@test.com",
	"password": "test"
}
------------------------------------

------------------------------------
SEARCH PRODUCT | Type GET:

http://localhost/api-symfony/public/api/search/nutella

/!\ Il faut rensigner le type d'authentification Bearer avec le token retourné via l'appel LOGIN précédent (idem pour l'ensemble des appels qui vont suivre). /!\
------------------------------------

------------------------------------
SAVE FAVORITE | Type GET:

http://localhost/api-symfony/public/api/save/8000500223369
------------------------------------

------------------------------------
DELETE FAVORITE | Type DELETE:

http://localhost/api-symfony/public/api/delete/8000500223369
------------------------------------

------------------------------------
CLEAR FAVORITES | Type GET:

http://localhost/api-symfony/public/api/clear
------------------------------------

------------------------------------
EXLUDE PRODUCT | Type GET:

http://localhost/api-symfony/public/api/exclude/8000500223369
------------------------------------


# Test unitaires

Quelques tests sont présent dans le dossier tests, ils sont déclenchables via la commande ./vendor/bin/phpunit. Les tests "sans token" sont explicitement faux et retourne une erreur, car il est impératif que le token soit présent.