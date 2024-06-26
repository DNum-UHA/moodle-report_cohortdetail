# Cohort Detail #

## Table of contents ##

- [Cohort Detail](#cohort-detail)
  - [Table of contents](#table-of-contents)
  - [Languages](#languages)
  - [Requirements](#requirements)
  - [Features](#features)
  - [Plugin Settings](#plugin-settings)
  - [Documentation](#documentation)
  - [Installing via uploaded ZIP file](#installing-via-uploaded-zip-file)
  - [Installing manually](#installing-manually)
  - [Tests](#tests)
  - [Javascript Grunt tasks](#javascript-grunt-tasks)
  - [License](#license)

## Languages ##

- [🇬🇧 English](README.md)
- [🇫🇷 French](README_fr.md)

Cohort Detail is a report plugin for Moodle that allows you to view the members and the courses of a cohort.

## Requirements ##

- Moodle 4.1 (Build: 2022112800) or later (Tested on 4.1, 4.3 and 4.4)

The Github Actions validate the plugin on:

- Moodle 4.4 with different PHP versions and Database engines :
  - PHP 8.1 with MariaDB 10.6
  - PHP 8.2 with MariaDB 10.6
  - PHP 8.3 with MariaDB 10.6
  - PHP 8.1 with PostgreSQL 13
  - PHP 8.2 with PostgreSQL 13
  - PHP 8.3 with PostgreSQL 13

- Moodle 4.3 with different PHP versions and Database engines :
  - PHP 8.0 with MariaDB 10.6
  - PHP 8.1 with MariaDB 10.6
  - PHP 8.2 with MariaDB 10.6
  - PHP 8.0 with PostgreSQL 13
  - PHP 8.1 with PostgreSQL 13
  - PHP 8.2 with PostgreSQL 13

- Moodle 4.1 with different PHP versions and Database engines :
  - PHP 7.4 with MariaDB 10.6
  - PHP 8.0 with MariaDB 10.6
  - PHP 8.1 with MariaDB 10.6
  - PHP 7.4 with PostgreSQL 13
  - PHP 8.0 with PostgreSQL 13
  - PHP 8.1 with PostgreSQL 13

## Features ##

- View the members of a cohort
- View the courses of a cohort (You must have the capability to view the course to see it in the list)
- Your courses with the cohort(s) that are enrolled in them

## Plugin Settings ##

The plugin has settings in the administration panel:

Go to _Site administration > Plugins > Reports > Cohort Detail - Administation_.

- **What systems roles can access this plugin?:** Choose the system roles that can access the plugin.
- **What roles in courses can access this plugin?:** Choose the roles in courses that can access the plugin.
- **Default Cohort Type:** Choose the default cohort type to use when searching for cohorts.

You also have a list of Roles that can access the plugin.

## Documentation ##

You can find the documentation for this plugin in the docs directory. The documentation is available in English and French.

[English Documentation](docs/Usage_of_the_Cohort_Detail_plugin.pdf)

[French Documentation](docs/Utilisation_du_plugin_Detail_des_cohortes.pdf)

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration > Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/report/cohortdetail

Afterwards, log in to your Moodle site as an admin and go to _Site administration > Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Tests ##

This plugin has PHPUnit tests located in the `tests` directory. To run only the tests for this plugin, execute the following command in moodle root:

    $ vendor/bin/phpunit report/cohortdetail/tests/lib_test.php

**WARNING: Do not install / run PHPUnit test on a production server.**

## Javascript Grunt tasks ##

This plugin uses Moodle Grunt tasks to check and validate the JavaScript code. To run these tasks, you need to have Node.js and npm installed in your system.

First, you need a full install of Moodle. Then, you need to install the dependencies by running:

    $ npm install

After that, you can go to the plugin directory in the amd folder:

    $ cd report/cohortdetail/amd

And execute the following command to check the JavaScript code:

    $ npx grunt

## License ##

2024 DNum UHA

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see <https://www.gnu.org/licenses/>.
