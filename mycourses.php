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
 * This page displays the courses the user has created.
 *
 * @package    report_cohortdetail
 * @copyright  2024 DNnum UHA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/lib.php');

global $USER, $PAGE, $CFG;

$url = new moodle_url('/report/cohortdetail/index.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');

// We set the context of the page. If the courseid is in the GET argument or in the session, we set the context to course.
if (array_key_exists('courseid', $_GET) || array_key_exists('courseid', $_SESSION)) {
    if (array_key_exists('courseid', $_GET)) {
        $coursecontextid = $_GET['courseid'];
    } else {
        $coursecontextid = $_SESSION['courseid'];
    }
    try {
        $PAGE->set_context(context_course::instance($coursecontextid));
    } catch (Exception $e) {
        // If the course context is not found, we display an error message.
        throw new moodle_exception('error:coursecontextnotfound', 'report_cohortdetail');
    }
} else {
    // Else we set the context to system.
    $PAGE->set_context(context_system::instance());
}

$PAGE->set_title(get_string('pluginnamemycourse', 'report_cohortdetail'));


// Verify that the user is logged in and has the necessary permissions.
require_login();

$syscontext = context_system::instance();

// We verify if the url contain the GET argument courseid or if the session contain the courseid.

// We set the canaccess variable to false, it will be set to true if the user can access the plugin using a course role.
$canaccess = false;

// We verify if the user has the capability to view the plugin in the course context.
if (isset($_GET['courseid']) || (isset($_SESSION['courseid']) && !empty($_SESSION['courseid']))) {
    // We get the course role stored in the plugin settings.
    $courserole = get_config('report_cohortdetail', 'rolescourse');

    // If courserole contains comma, we explode it.
    if (strpos($courserole, ',') !== false) {
        $courserole = explode(',', $courserole);
    }

    if (isset($_GET['courseid']) && !empty($_GET['courseid'])) {
        // We get the course id from the GET argument.
        $courseid = required_param('courseid', PARAM_INT);
    } else {
        // We get the course id from the session.
        $courseid = $_SESSION['courseid'];
    }

    // We get the course context.
    try {
        $coursecontext = context_course::instance($courseid);
    } catch (Exception $e) {
        // If the course context is not found, we display an error message.
        throw new moodle_exception('error:coursecontextnotfound', 'report_cohortdetail');
    }

    // We get the current user role in the course.
    $userrole = get_user_roles($coursecontext, $USER->id, true);

    // We verify if one of the user roles is the same as the course role stored in the plugin settings.
    foreach ($userrole as $role) {
        if (is_array($courserole)) {
            foreach ($courserole as $roleid) {
                if ($role->roleid == $roleid) {
                    $canaccess = true;
                }
            }
        } else {
            if ($role->roleid == $courserole) {
                $canaccess = true;
            }
        }
    }
}

// We verify if the user can access the plugin.
if ($canaccess) {
    // We add into the session the course id.
    $_SESSION['courseid'] = $courseid;
}

// If the user can't access the plugin, we throw an exception.
if (!has_capability('report/cohortdetail:view', $syscontext)
    && !has_capability('moodle/site:config', $syscontext)
    && !$canaccess) {
    throw new moodle_exception('errornoaccess', 'report_cohortdetail');
}

$url = new moodle_url('/report/cohortdetail/mycourses.php', []);
$PAGE->set_url($url);

// Set up the page.
if (has_capability('moodle/site:config', $syscontext)) {
    admin_externalpage_setup('reportcohortdetail');
} else {
    $PAGE->set_context(context_system::instance());
}

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_cohortdetail'));

// We create a div to store the top buttons.
echo html_writer::start_div('topbuttonsmycourses');

// Add a return link to the page.
echo html_writer::tag('a',
    get_string('return', 'report_cohortdetail'),
    [
        'href' => new moodle_url('/report/cohortdetail/index.php'),
        'class' => 'returnlinkmycourses btn btn-secondary',
    ]
);

// Close the div.
echo html_writer::end_div();

// We open a div to store the title + table.
echo html_writer::start_div('tablemycourses');

// We create a p tag to store the title.
echo html_writer::tag('p', get_string('mycourses', 'report_cohortdetail'), ['class' => 'titlemycourses']);

// We create a table to store the courses.
$table = new html_table();

// We add the table headers.
$table->head = [
    get_string('coursename', 'report_cohortdetail'),
    get_string('cohortname', 'report_cohortdetail'),
];

// We search the users courses and the cohort linked to the course.

// First we get the user id.

$userid = $USER->id;

if (is_siteadmin()) {
    // If the user is a site admin, we get all the courses.
    $courses = get_courses('all', 'fullname ASC', 'c.id, c.fullname');

    // We remove the frontpage course.
    if (array_key_exists(1, $courses)) {
        unset($courses[1]);
    }
} else {
    // We get the courses the user is enrolled in.
    $courses = enrol_get_users_courses($userid, true, null, 'fullname ASC');
}

// We get the cohort linked to the course.
if (!empty($courses)) {
    foreach ($courses as $course) {
        $cohort = get_cohort_from_course($course->id);

        // We verify if the result is an array.
        if (is_array($cohort)) {
            // We create a variable to store the cohort id.
            $cohortidarray = [];

            // We verify that the cohort array is not empty.

            if (count($cohort) > 0) {

                // We loop through the cohort array.
                foreach ($cohort as $cohortid) {

                    // Get the cohort id.
                    $cohortid = $cohortid->customint1;
                    // Get the user context.
                    $usercontext = context_user::instance($userid);

                    // From the id get the cohort name.
                    $cohortdetails = $DB->get_record('cohort', ['id' => $cohortid]);

                    $cohortidarray[] = $cohortid;
                }

                if (count($cohortidarray) > 0) {
                    // We create a variable to store the cohort name.
                    $cohortnames = [];

                    // We loop through the cohort id array.
                    foreach ($cohortidarray as $cohortid) {
                        // From the id get the cohort name.
                        $cohortdetails = $DB->get_record('cohort', ['id' => $cohortid]);

                        if (!is_bool($cohortdetails)) {
                            // We add the cohort name to the variable.
                            $cohortnames[] = $cohortdetails->name;
                        }
                    }

                    // Create the link to the course.
                    $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);

                    if (!empty($cohortnames) && count($cohortnames) > 1) {
                        // We create the multiple badges.
                        $badges = '';
                        foreach ($cohortnames as $cohortname) {
                            $badges .= html_writer::tag(
                                'span',
                                $cohortname,
                                ['class' => 'badge bg-primary text-white mr-1']
                            );
                        }

                        // Get the category of the course.
                        $category = $DB->get_record('course_categories', ['id' => $course->category]);

                        // We verify if the category exists (not a boolean).
                        if (!is_bool($category)) {
                            // We explode the category path to get the real name of the category.
                            $categorypath = explode('/', $category->path);
                            $categoryname = '';
                            for ($i = 1; $i < count($categorypath); $i++) {
                                // First we get the real name from the database.
                                $realname = $DB->get_record('course_categories', ['id' => $categorypath[$i]]);
                                // We add the real name to the category name.
                                if ($categoryname == '') {
                                    $categoryname = '/ ' . $realname->name;
                                } else {
                                    $categoryname = $categoryname . ' / ' . $realname->name;
                                }
                            }
                        } else {
                            // If the category does not exist, we set the category name to an empty string.
                            $categoryname = '';
                        }

                        $table->data[] = [
                        // We display the course name as a link + the path.
                        html_writer::tag('a', $course->fullname, ['href' => $courseurl])
                        . html_writer::tag('p', $categoryname)
                        ,
                        // We display the cohort names as badges.
                        $badges,
                        ];
                    } else if (!empty($cohortnames) && count($cohortnames) == 1) {
                        $cohortname = $cohortnames[0];

                        // Get the category of the course.
                        $category = $DB->get_record('course_categories', ['id' => $course->category]);

                        // We verify if the category exists (not a boolean).
                        if (!is_bool($category)) {

                            // We explode the category path to get the real name of the category.
                            $categorypath = explode('/', $category->path);
                            $categoryname = '';
                            for ($i = 1; $i < count($categorypath); $i++) {
                                // First we get the real name from the database.
                                $realname = $DB->get_record('course_categories', ['id' => $categorypath[$i]]);
                                // We add the real name to the category name.
                                if ($categoryname == '') {
                                    $categoryname = '/ ' . $realname->name;
                                } else {
                                    $categoryname = $categoryname . ' / ' . $realname->name;
                                }
                            }
                        } else {
                            // If the category does not exist, we set the category name to an empty string.
                            $categoryname = '';
                        }

                        $table->data[] = [
                        // We display the course name as a link.
                        html_writer::tag('a', $course->fullname, ['href' => $courseurl])
                        . html_writer::tag('p', $categoryname),
                        // We check if the cohort exists and if it does we display the name is a bootstrap badge.
                        $cohortdetails
                        ? html_writer::tag(
                        'span',
                        $cohortname,
                        ['class' => 'badge bg-primary text-white'])
                        : '',
                        ];
                    }
                }
            } else {
                // If the cohort array is empty, we display the course name and an empty cell.

                // Get the category of the course.
                $category = $DB->get_record('course_categories', ['id' => $course->category]);

                // We verify if the category exists (not a boolean).
                if (!is_bool($category)) {

                    // We explode the category path to get the real name of the category.
                    $categorypath = explode('/', $category->path);
                    $categoryname = '';
                    for ($i = 1; $i < count($categorypath); $i++) {
                        // First we get the real name from the database.
                        $realname = $DB->get_record('course_categories', ['id' => $categorypath[$i]]);
                        // We add the real name to the category name.
                        if ($categoryname == '') {
                            $categoryname = '/ ' . $realname->name;
                        } else {
                            $categoryname = $categoryname . ' / ' . $realname->name;
                        }
                    }
                } else {
                    // If the category does not exist, we set the category name to an empty string.
                    $categoryname = '';
                }

                // We create the course url.
                $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);

                // We add the course name + the category path to the table.
                $table->data[] = [
                    // We display the course name as a link.
                    html_writer::tag('a', $course->fullname, ['href' => $courseurl])
                    . html_writer::tag('p', $categoryname),
                    '',
                ];
            }
        } else {
            // We display a moodle exception.
            throw new moodle_exception('erreurgettingdatas', 'report_cohortdetail');
        }
    }
}

// We print the table.
echo html_writer::table($table);

// We close the div.
echo html_writer::end_div();

echo $OUTPUT->footer();
