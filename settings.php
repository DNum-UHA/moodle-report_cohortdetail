<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     report_cohortdetail
 * @category    admin
 * @copyright   2024 DNum UHA
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

if ($hassiteconfig) {

    $context = context_system::instance();

    // Create a new category under "Plugins" for your plugin.
    $settings = new admin_settingpage(
        'report_reportcohortdetail',
        get_string('pluginnameadmin', 'report_cohortdetail')
    );

    /*
    * Table to view the roles who can access the plugin.
    */

    // Create a new table to view the roles who can access the plugin.
    $table = new html_table();
    $table->head = [
        get_string('typetable', 'report_cohortdetail'),
        get_string('roletable', 'report_cohortdetail'),
    ];
    $table->data = [];

    // Get all the sys roles who can access the plugin.
    $roles = get_config('report_cohortdetail', 'roles');

    if ($roles !== false && !empty($roles)) {
        // Convert the roles to an array.
        $roles = explode(',', $roles);

        $rolesarray = [];

        if (!empty($roles)) {

            // Get the name of the roles because we only have the id.
            foreach ($roles as $role) {
                // First we get the role object from the database.
                $role = $DB->get_record('role', ['id' => $role]);

                // After we get the localname of the role.
                $localname = role_get_name($role, $context);

                // We add the name to the array.
                $rolesarray[] = [
                'id' => $role->id,
                'name' => $localname,
                ];
            }

            if ($rolesarray !== false && !empty($rolesarray) && count($rolesarray) > 0) {
                // Add the roles to the table.
                foreach ($rolesarray as $role) {
                    $table->data[] = [
                        '<span class="badge badge-pill badge-primary">'
                        . get_string('sysroletypetable', 'report_cohortdetail')
                        . '</span>',
                        $role['name'],
                    ];
                }
            }
        }
    }

    // Get all the course roles who can access the plugin.
    $rolescourse = get_config('report_cohortdetail', 'rolescourse');

    if ($rolescourse !== false && !empty($rolescourse)) {
        // Convert the roles to an array.
        $rolescourse = explode(',', $rolescourse);

        $rolesarraycourse = [];

        if (!empty($rolescourse)) {

            // Get the name of the roles because we only have the id.
            foreach ($rolescourse as $role) {
                // First we get the role object from the database.
                $role = $DB->get_record('role', ['id' => $role]);

                // After we get the localname of the role.
                $localname = role_get_name($role, $context);

                // We add the name to the array.
                $rolesarraycourse[] = [
                'id' => $role->id,
                'name' => $localname,
                ];
            }

            if ($rolesarraycourse !== false && !empty($rolesarraycourse) && count($rolesarraycourse) > 0) {
                // Add the roles to the table.
                foreach ($rolesarraycourse as $role) {
                    $table->data[] = [
                        '<span class="badge badge-pill badge-secondary">'
                        . get_string('courseroletypetable', 'report_cohortdetail')
                        . '</span>',
                        $role['name'],
                    ];
                }
            }
        }
    }

    if ($table->data == false || empty($table->data)) {
        $table->data[] = [
        get_string('norightstable', 'report_cohortdetail'),
        '',
        '',
        ];
    }

    // Add the table to the page.

    // Convert the table to HTML.
    $tablehtml = html_writer::table($table);

    // Add the table to the page.
    $settings->add(
        new admin_setting_heading(
            'report_reportcohortdetail_table',
            get_string('adminrighttitle', 'report_cohortdetail'),
            $tablehtml
        )
    );

    /*
    * Rights Setting
    */

    // Add a heading for the plugin settings.
    $settings->add(
    new admin_setting_heading(
        'report_reportcohortdetail',
        get_string('adminpermtitle', 'report_cohortdetail'),
        ''
    )
    );

    /*
    * System Role Setting
    *
    * We get all the system roles in moodle minus the roles that are in the database of the plugin.
    * We add a new setting to the page.
    */

    $context = context_system::instance();

    // Get all the roles.
    $rolesraw = role_get_names($context);
    $rolessys = get_roles_for_contextlevels(CONTEXT_SYSTEM);

    // Filter the roles to get only the system roles.
    $rolesraw = array_filter($rolesraw, function ($role) use ($rolessys) {
        return in_array($role->id, $rolessys);
    });

    $roles = [];

    /* Extract the localname and the id of the roles.
    To create a new array like this [0] => ['Manager'] */
    foreach ($rolesraw as $role) {
        $roles[$role->id] = $role->localname;
    }

    // Order the array by the name of the roles.
    asort($roles);

    // Add a new setting to the page.
    $settingrole =
    new admin_setting_configmultiselect(
        'report_cohortdetail/roles',
        get_string('sysrolesearchtitle', 'report_cohortdetail'),
        get_string('sysrolesearchdesc', 'report_cohortdetail'),
        [],
        $roles,
    );

    // Add a callback to the setting.
    $settingrole->set_updatedcallback('report_cohortdetail_update_roles');

    // Add the setting to the page.
    $settings->add($settingrole);

    /*
    * Course Role Setting
    *
    * We get all the course roles in moodle minus the roles that are in the database of the plugin.
    * We add a new setting to the page.
    */

    // We get all the course roles.
    $rolesrawcourse = role_get_names();
    $rolescourse = get_roles_for_contextlevels(CONTEXT_COURSE);

    // Filter the roles to get only the course roles.
    $rolesrawcourse = array_filter($rolesrawcourse, function ($role) use ($rolescourse) {
        return in_array($role->id, $rolescourse);
    });

    $rolescourse = [];

    /* Extract the localname and the id of the roles.
    To create a new array like this [0] => ['Manager'] */
    foreach ($rolesrawcourse as $role) {
        $rolescourse[$role->id] = $role->localname;
    }

    // Order the array by the name of the roles.
    asort($rolescourse);

    // Add a new setting to the page.

    $settingrolecourse =
    new admin_setting_configmultiselect(
        'report_cohortdetail/rolescourse',
        get_string('courserolesearchtitle', 'report_cohortdetail'),
        get_string('courserolesearchdesc', 'report_cohortdetail'),
        [],
        $rolescourse,
    );

    // Add the setting to the page.
    $settings->add($settingrolecourse);

    /*
    * Default cohort type
    *
    * A setting to choose the default cohort type (system or category).
    */

    // Add a new setting to the page (two options).
    $settings->add(
    new admin_setting_configselect(
        'report_cohortdetail/defaultcohorttype',
        get_string('defaultcohorttype', 'report_cohortdetail'),
        get_string('defaultcohorttypedesc', 'report_cohortdetail'),
        'system',
        [
        'system' => get_string('systemcohorts', 'report_cohortdetail'),
        'category' => get_string('categorycohorts', 'report_cohortdetail'),
        ]
    )
    );

    $ADMIN->add("reports", new admin_externalpage("reportcohortdetail", get_string("pluginname", "report_cohortdetail"),
    "$CFG->wwwroot/report/cohortdetail/index.php", "moodle/cohort:view"));
}
