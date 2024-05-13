# Cohort Detail #

## Table of contents ##

[[_TOC_]]

## Languages ##

- [🇬🇧 English](README.md)
- [🇫🇷 French](README_fr.md)

Cohort Detail is a report plugin for Moodle that allows you to view the members and the courses of a cohort.

## Requirements ##

- Moodle 4.3 (Build: 2023100900) or later (Tested on 4.3 and 4.4-beta)

The Github Actions validate the plugin on Moodle 4.3 with different PHP versions and Database engines :
- PHP 8.0 with MariaDB 10.6
- PHP 8.1 with MariaDB 10.6
- PHP 8.2 with MariaDB 10.6
- PHP 8.0 with PostgreSQL 13
- PHP 8.1 with PostgreSQL 13
- PHP 8.2 with PostgreSQL 13

## Features ##

- View the members of a cohort
- View the courses of a cohort (You must have the capability to view the course to see it in the list)
- Your courses with the cohort(s) that are enrolled in them

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
