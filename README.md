# symfony-bundle
Group of Symfony bundle

## Docker compose file 
```yaml
version: '3.3'

services:
  php:
    image: adrienlbt/php:${PHP_VERSION}-dev
    container_name: hexagonal-make-bundle
    volumes:
      - ${APP_LOCAL_PATH}:/var/www/html
```

## Lancer le conteneur 
```sh
docker compose -f docker-compose.dev.yml --env-file .env --env-file .env.local up -d
```

# CI

## phpcbf 
```bash
docker-compose -f docker-compose.dev.yml --env-file .env --env-file .env.local exec -T php vendor/bin/phpcbf
```

## phpcs 
```bash
docker-compose -f docker-compose.dev.yml --env-file .env --env-file .env.local exec -T php vendor/bin/phpcs --standard=./phpcs.xml
docker-compose -f docker-compose.dev.yml --env-file .env --env-file .env.local exec -T php vendor/bin/phpcs --report=gitblame --standard=./phpcs.xml.dist
```

## phpstan 
```bash
docker-compose -f docker-compose.dev.yml --env-file .env --env-file .env.local exec -T php vendor/bin/phpstan analyse -c phpstan.neon.dist
```

## phpunit
```bash
docker-compose -f docker-compose.dev.yml --env-file .env --env-file .env.local exec -T php rm -rf var/coverage .phpunit.cache var/cache/test/* tests/tmp/cache/*
docker-compose -f docker-compose.dev.yml --env-file .env --env-file .env.local exec -T php vendor/bin/simple-phpunit --testdox --no-progress
```

## phpunit check coverage
```bash
docker-compose -f docker-compose.dev.yml --env-file .env --env-file .env.local exec -T php vendor/bin/coverage-check var/coverage/clover.xml 70
```



# TODO
- [OK] Créer une image docker PHP qui peut servir de base pour tous mes projet adrienlbt/php:...
- [OK] Pousser cette image sur mon hub docker
- [OK] Créer une image PHP de dev pour le bundle qui utilise l'image php précédement créé
- [OK] Créer un conteneur Docker avec l'image du bundle
- [OK] Créer l'arborescence du bundle
- [OK] Implémenter l'affichage d'un hello world via une commande
- [OK] Installer la version dans le projet poc-symfony et jouer la commande
- [OK] Implémenter la surcharge du namespace du symfony-maker bundle
- [OK] Installer la version dans le projet poc-symfony
- [OK] Jouer une commande du symfony-maker bundle et vérifier que c'est bien la surcharge qui est utilisée
- [OK] Mettre en place phpstan ou pslam, phpcs
- [OK] Checker les tests
- [OK] Nettoyer le code
- Publier
- Vérification de la coverage => La coverage ne se génère pas. (Pb simple-phpunit ?)
- [OK] Utiliser les configurations des dossiers pour les chemins dans la commande
- Au remove du package il faut retirer le namespace dans le composer.json
- Renommer les configs. Car ce n'est pas les path mais les noms des dossiers qu'on configure


# Debug TU
-----------------------------------------------------------------

[OK] git clone du src dans tmp/cache/maker-repo
[OK] création du fake projet via le symfony-skeleton
[OK] ajout du repo dans le composer du skeleton vers le clone maker-repo
"repositories": {
        "adrienlbt/adrienlbt/hexagonal-maker-bundle": {
            "type": "path",
            "url": "/var/www/html/tests/tmp/cache/maker-repo",
            "options": {
                "versions": {
                    "adrienlbt/hexagonal-maker-bundle": "9999.99"
                }
            }
        }
    }
[OK] ajouter le require du maker-bundle dans le composer json du skeleton
[OK] lancement du require 
[OK]Une application est crée à partir du skeleton


----- 
Autoload dans l'applicatif 
(a tester via une recipe symfony)
[Ca fonctionne] * Soit ajouter, dans le composer json de l'application au niveau de l'autoload de dev, la surcharge du namespace
[A tester] * Soit créer un fichier autoload.php dans le bundle qui contient 
spl_autoload_register(function ($class) {
    if (strpos($class, 'NamespaceExistant\\') === 0) {
        $file = __DIR__ . '/NamespaceExistant/' . str_replace('\\', '/', substr($class, 17)) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
Puis ajouter dans le composer.json de l'application, 
{
  "autoload": {
    "files": ["chemain/vers/le/bundle/autoload.php"]
  }
}
---
# Créer la recipe symfony
https://symfony.com/doc/current/setup/flex_private_recipes.html#install-the-recipes-in-your-project
https://github.com/symfony/recipes

## Contenu de la recette
### Commande symfony
Le bundle possède une commande symfony src/Command/HexagonalMakerAutoload.php qui va :
    - Ajouter les autoload psr-4 dans le composer json de l'application cible
    - Lancer un composer dump-autoload sur l'application cible
La recette symfony ajoutera cette commande dans le composer json de l'application au niveau des autos-scripts.
La commande devrait se jouer après installation du bundle. Et donc la commande symfony HexagonalMakerAutoload devrait 
être enregistré (on croise les doigts, sinon on passera par le chemin du vendor et un script shell).
### Fichier de configuration 
Ajout du fichier de config du bundle dans l'application source. 
Contient actuellement les chemins vers les dossiers Application, Domain & Infrastructure dans l'application
### Partage de la recette
#### Public
Normalement le bundle est censé être publique et disponible sur packagist.
Du coup je vais devoir publié la recette sur https://github.com/symfony/recipes-contrib
=> Créer une branche adrientlbt/hexagonal-maker-bundle/1.0
=> Push avec le contenu (arbo, manifest.json, ...)
=> Création de la PR
#### Private
Malheuresement si la recipe n'est pas accepté. Le bundle aura du mal a être utilisé par des personnes public
sans qu'il change le composer.json de leur application manuellement. 
Il faudra qu'ils ajoutent le lien du repo de la recipe :
```json
{
    "extra": {
        "symfony": {
            "endpoint": [
                "https://api.github.com/repos/AdrienWac/recipes-symfony/contents/index.json",
                "flex://defaults"
            ]
        }
    }
}
``` 
### Comment tester la recipe ?
## Cas #1: la recipe est publique
Dans ce cas la recipe est donc dans le repo https://github.com/symfony/recipes-contrib
- Il faudrait la copier dans le dossier tests/tmp/cache/maker_app_*/recipes 
(possible avec un git clone ? ).
*Dans un premier temps je copie le contenu manuellement (lien symbolique depuis le recipes)*
- Créer le dossier tests/tmp/cache/maker_app_*/.flex
- Créer le fichier tests/tmp/cache/maker_app_*/.flex/config.json avec le contenu suivant 
{
  "aliases": [],
  "repositories": [
    {
      "type": "path",
      "url": "../recipes"
    }
  ]
}
- Lancer la commande composer install adrienlbt/maker-hexagonal dans le test
- Faire les assertions nécessaire
    - Autoload de l'application
    - Présence du fichier de config du bundle dans l'application
    - ...




