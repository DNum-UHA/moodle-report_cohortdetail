<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Callback implementations for Cohort Detail
 *
 * @package    report_cohortdetail
 * @copyright  2024 DNnum UHA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * Function to add the cohort detail to the profile navigation.
  *
  * @param core_user\output\myprofile\tree $tree The user profile navigation tree.
  * @param [object] $user The user object.
  * @return void
  */
function report_cohortdetail_myprofile_navigation(core_user\output\myprofile\tree $tree,
    $user) {
    global $USER;

    // Disable the profile navigation for users who are guests or not logged in.
    if (isguestuser() || !isloggedin()) {
        return;
    }

    // We verify if the user is the same as the one we are currently viewing.
    if (\core\session\manager::is_loggedinas() || $USER->id != $user->id) {
        return;
    }

    // We verify if the user have the capability to view the cohort detail.
    $context = context_system::instance();
    if (has_capability('report/cohortdetail:view', $context) || has_capability('moodle/site:config', $context)) {
        // We create a new node.
        $node = new core_user\output\myprofile\node(
            'reports',
            'cohortdetail',
            get_string('pluginname', 'report_cohortdetail'),
            null,
            new moodle_url('/report/cohortdetail/index.php')
        );

        // We add the node to the tree.
        $tree->add_node($node);
    }
    return true;
}

/**
 * Function to add the cohort detail to the course report page.
 *
 * @param [object] $navigation The navigation object.
 * @param [object] $course The course object.
 * @param context $context The context.
 * @return void
 */
function report_cohortdetail_extend_navigation_course($navigation, $course, $context) {
    // Add for the admin.
    if (has_capability('moodle/site:config', $context)) {
        $url = new moodle_url('/report/cohortdetail/index.php');
        $node = navigation_node::create(
            get_string('pluginname', 'report_cohortdetail'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            null,
            new pix_icon('i/report', '')
        );

        $navigation->add_node($node);
    } else {
        // We get the user.
        global $USER;
        // We get the course role stored in the plugin settings.
        $courserole = get_config('report_cohortdetail', 'rolescourse');

        // We explode the rolescourse to get the roles.
        $courserolearray = explode(',', $courserole);

        // We get the current user role in the current course.
        $userrole = get_user_roles($context, $USER->id, true);

        // We verify if the one of the user role is in the courserolearray.
        foreach ($userrole as $role) {

            if (in_array($role->roleid, $courserolearray)) {
                // We create a new node.
                $url = new moodle_url('/report/cohortdetail/index.php', ['courseid' => $course->id]);
                $node = navigation_node::create(
                get_string('pluginname', 'report_cohortdetail'),
                $url,
                navigation_node::TYPE_SETTING,
                null,
                null,
                new pix_icon('i/report', '')
                );

                // We add the node to the navigation.
                $navigation->add_node($node);
            }
        }
    }
}

/**
 * A function to get the members of a specific cohort.
 *
 * @param [int] $cohortid The id of the cohort.
 * @return [array] An array of users.
 */
function cohort_get_members($cohortid) {
    global $DB;

    // We verify if the cohort id is empty.
    if (empty($cohortid)) {
        throw new moodle_exception('error:emptycohortid', 'report_cohortdetail');
    }

    // We verify that the cohort id is numeric.
    if (!is_numeric($cohortid)) {
        throw new moodle_exception('error:notnumericcohortid', 'report_cohortdetail');
    }

    $sql = "SELECT
                u.id,
                u.username,
                u.idnumber,
                u.firstname,
                u.lastname,
                u.email
            FROM
                {user} u
            JOIN
                {cohort_members} cm ON cm.userid = u.id
            WHERE
                cm.cohortid = ?";
    $members = $DB->get_records_sql($sql, [$cohortid]);

    // We verify if we have members.
    if (empty($members)) {
        return [];
    } else {
        return $members;
    }
}

/**
 * A function to get the courses linked to a specific cohort.
 *
 * @param [int] $cohortid The id of the cohort.
 * @return [array] An array of courses.
 */
function get_courses_with_cohort($cohortid) {
    global $DB;

    // We verify if the cohort id is empty.
    if (empty($cohortid)) {
        throw new moodle_exception('error:emptycohortid', 'report_cohortdetail');
    }

    // We verify that the cohort id is numeric.
    if (!is_numeric($cohortid)) {
        throw new moodle_exception('error:notnumericcohortid', 'report_cohortdetail');
    }

    $sql = "SELECT
                c.id AS courseid,
                c.fullname AS coursename,
                c.category AS categoryid
            FROM
                {course} c
            JOIN
                {enrol} e ON e.courseid = c.id
            WHERE
                e.enrol = 'cohort'
            AND
                e.customint1 = ?";

    $courses = $DB->get_records_sql($sql, [$cohortid]);

    // We verify if we have courses.
    if (empty($courses)) {
        return [];
    } else {
        return $courses;
    }
}

/**
 * A function to get the cohort linked to a specific course.
 *
 * @param [int] $courseid The id of the course.
 * @return [array] An array of cohorts objects (In each object, there is a customint1 field with the cohort id).
 */
function get_cohort_from_course($courseid) {
    global $DB;

    $sql = "SELECT
                e.customint1
            FROM
                {enrol} e
            WHERE
                e.courseid = :courseid
            AND
                e.enrol = 'cohort'
            AND e.customint1 IS NOT NULL";

    $cohort = $DB->get_records_sql($sql, ["courseid" => $courseid]);

    return $cohort;
}

/**
 * A function to link a role to a capability.
 *
 * @return void
 */
function report_cohortdetail_update_roles() {
    global $DB;
    $context = context_system::instance();

    // Get the role from the settings.
    $roles = get_config('report_cohortdetail', 'roles');

    if (empty($roles) || $roles === false) {
        return;
    }

    // Get all the roles and add the custom capability to them (if they do not have it).
    $roles = explode(',', $roles);

    /* Before adding the capability to the role,
    we verify if we have roles that are not in the $roles variable but have the capability.*/

    // Get all the roles that have the capability.
    $roleswithcapability = $DB->get_records('role_capabilities', ['capability' => 'report/cohortdetail:view']);

    foreach ($roleswithcapability as $rolewithcapability) {
        if (!in_array($rolewithcapability->roleid, $roles)) {
            // We remove the capability from the role.
            unassign_capability('report/cohortdetail:view', $rolewithcapability->roleid, $context->id);
        }
    }

    // We add the capability to the roles.
    foreach ($roles as $role) {
        $role = intval($role);

        // We verify if the role already have the capability.
        $capability = $DB->get_record('role_capabilities', ['capability' => 'report/cohortdetail:view', 'roleid' => $role]);
        if (empty($capability) || $capability == false) {
            assign_capability('report/cohortdetail:view', CAP_ALLOW, $role, $context->id);
        }
    }
}
