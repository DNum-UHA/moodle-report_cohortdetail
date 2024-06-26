# Cohort Detail #

## Sommaire ##

- [Cohort Detail](#cohort-detail)
  - [Sommaire](#sommaire)
  - [Langues](#langues)
  - [Prérequis](#prérequis)
  - [Fonctionnalités](#fonctionnalités)
  - [Paramètres du plugin](#paramètres-du-plugin)
  - [Documentation](#documentation)
  - [Installation via un fichier ZIP téléchargé](#installation-via-un-fichier-zip-téléchargé)
  - [Installation manuelle](#installation-manuelle)
  - [Tests](#tests)
  - [Tâches Grunt JavaScript](#tâches-grunt-javascript)
  - [Licence](#licence)

## Langues ##

- [🇬🇧 Anglais](README.md)
- [🇫🇷 Français](README_fr.md)

Détail des cohortes est un plugin de rapport pour Moodle qui vous permet de voir les membres et les cours d'une cohorte.

## Prérequis ##

- Moodle 4.1 (Build: 2022112800) ou ultérieur (Testé sur 4.1, 4.3 et 4.4)

Les Github Actions valident le plugin sur :

- Moodle 4.4 avec différentes versions de PHP et moteurs de base de données :
    - PHP 8.1 avec MariaDB 10.6
    - PHP 8.2 avec MariaDB 10.6
    - PHP 8.3 avec MariaDB 10.6
    - PHP 8.1 avec PostgreSQL 13
    - PHP 8.2 avec PostgreSQL 13
    - PHP 8.3 avec PostgreSQL 13

- Moodle 4.3 avec différentes versions de PHP et moteurs de base de données :
    - PHP 8.0 avec MariaDB 10.6
    - PHP 8.1 avec MariaDB 10.6
    - PHP 8.2 avec MariaDB 10.6
    - PHP 8.0 avec PostgreSQL 13
    - PHP 8.1 avec PostgreSQL 13
    - PHP 8.2 avec PostgreSQL 13

- Moodle 4.1 avec différentes versions de PHP et moteurs de base de données :
    - PHP 7.4 avec MariaDB 10.6
    - PHP 8.0 avec MariaDB 10.6
    - PHP 8.1 avec MariaDB 10.6
    - PHP 7.4 avec PostgreSQL 13
    - PHP 8.0 avec PostgreSQL 13
    - PHP 8.1 avec PostgreSQL 13

## Fonctionnalités ##

- Voir les membres d'une cohorte
- Voir les cours d'une cohorte (Vous devez avoir la capacité de voir le cours pour le voir dans la liste)
- Vos cours avec la ou les cohortes qui y sont inscrites

## Paramètres du plugin ##

Le plugin a des paramètres dans le panneau d'administration :

Rendez-vous dans _Administration du site > Plugins > Rapports > Détail des cohortes - Administration_.

- **Quels rôles systèmes peuvent accéder à ce plugin ? :** Choisissez les rôles systèmes qui peuvent accéder au plugin.
- **Quels rôles de cours peuvent accéder à ce plugin ? :** Choisissez les rôles dans un cours qui peuvent accéder au plugin.
- **Type de cohorte par défaut :** Choisissez le type de cohorte par défaut à utiliser lors de la recherche de cohortes.

Vous avez également une liste des rôles qui peuvent accéder au plugin.

## Documentation ##

Vous pouvez trouver la documentation de ce plugin dans le répertoire docs. La documentation est disponible en anglais et en français.

[Documentation en français](docs/Utilisation_du_plugin_Detail_des_cohortes.pdf)

[Documentation en anglais](docs/Usage_of_the_Cohort_Detail_plugin.pdf)

## Installation via un fichier ZIP téléchargé ##

1. Connectez-vous à votre site Moodle en tant qu'administrateur et allez à _Administration du site > Plugins > Installer des plugins_.
2. Téléchargez le fichier ZIP avec le code du plugin. Vous ne devriez être invité à ajouter des détails supplémentaires que si votre type de plugin n'est pas détecté automatiquement.
3. Vérifiez le rapport de validation du plugin et terminez l'installation.

## Installation manuelle ##

Le plugin peut également être installé en mettant le contenu de ce répertoire dans

    {votre/moodle/dirroot}/report/cohortdetail

Ensuite, connectez-vous à votre site Moodle en tant qu'administrateur et allez à _Administration du site > Notifications_ pour terminer l'installation.

Alternativement, vous pouvez exécuter

    $ php admin/cli/upgrade.php

pour terminer l'installation en ligne de commande.

## Tests ##

Ce plugin contient des tests unitaires utilisant PHPUnit. Ils sont situés dans le répertoire `tests`. Pour exécuter uniquement les tests de ce plugin, exécutez la commande suivante à la racine de Moodle :

    $ vendor/bin/phpunit report/cohortdetail/tests/lib_test.php

**ATTENTION : Ne pas installer / exécuter les tests PHPUnit sur un serveur de production.**

## Tâches Grunt JavaScript ##

Ce plugin utilise les tâches Grunt de Moodle pour vérifier et valider le code JavaScript. Pour exécuter ces tâches, vous devez avoir Node.js et npm installés sur votre système.

Tout d'abord, vous avez besoin d'une installation complète de Moodle. Ensuite, vous devez installer les dépendances en exécutant :

    $ npm install

Après cela, vous pouvez aller dans le répertoire du plugin dans le dossier amd :

        $ cd report/cohortdetail/amd

Et exécutez la commande suivante pour vérifier le code JavaScript :

        $ npx grunt

## Licence ##

2024 DNum UHA

Ce programme est un logiciel libre : vous pouvez le redistribuer et/ou le modifier selon les termes de la Licence publique générale GNU telle que publiée par la Free Software Foundation, soit la version 3 de la Licence, soit (à votre choix) toute version ultérieure.

Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou d'ADAPTATION À UN USAGE PARTICULIER. Voir la Licence publique générale GNU pour plus de détails.

Vous devriez avoir reçu une copie de la Licence publique générale GNU avec ce programme. Si ce n'est pas le cas, consultez <https://www.gnu.org/licenses/>.
