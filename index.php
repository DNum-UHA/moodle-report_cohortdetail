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
 * Display Cohort Detail report
 *
 * @package    report_cohortdetail
 * @copyright  2024 DNum UHA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We load the forms for the system and category cohort.
use report_cohortdetail\cohortdetail_form_system;
use report_cohortdetail\cohortdetail_form_category;

// We import the necessary files.
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once(__DIR__ . '/cohortdetail_form.php');
require_once(__DIR__ . '/lib.php');

// We get the necessary global variables.
global $USER, $PAGE, $CFG;

// We set the url of the page.
$url = new moodle_url('/report/cohortdetail/index.php');
$PAGE->set_url($url);

// We set the layout of the page.
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

// We set the title of the page with the plugin name.
$PAGE->set_title(get_string('pluginname', 'report_cohortdetail'));

// Verify that the user is logged in and has the necessary permissions.
require_login();

$syscontext = context_system::instance();

// We set the canaccess variable to false, it will be set to true if the user can access the plugin using a course role.
$canaccess = false;

// We verify if the user has the capability to view the plugin in the course context.

// We verify if the url contain the GET argument courseid or if the session contain the courseid.
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
    if (is_array($courserole)) {
        foreach ($userrole as $role) {
            foreach ($courserole as $roleid) {
                if ($role->roleid == $roleid) {
                    $canaccess = true;
                }
            }
        }
    } else {
        foreach ($userrole as $role) {
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

// Set up the page's url.
$PAGE->set_url('/report/cohortdetail/index.php');

// Set up the page.
if (has_capability('moodle/site:config', $syscontext)) {
    admin_externalpage_setup('reportcohortdetail');
} else {
    $PAGE->set_context(context_system::instance());
}

// We get the default cohort type from the settings of the plugin (defaultcohorttype).
$defaultcohorttype = get_config('report_cohortdetail', 'defaultcohorttype');

if ($defaultcohorttype === "system" || $defaultcohorttype === "category") {
    // We create the forms for the system and category.
    $mformsystem = new cohortdetail_form_system();
    $mformcategory = new cohortdetail_form_category();
} else {
    throw new moodle_exception('invalidcohorttype', 'report_cohortdetail');
}

// We verify if the form has been submitted and check if it's cancelled.
if ($mformsystem->is_cancelled() || $mformcategory->is_cancelled()) {
    // If the form was cancelled, then redirect to the index page.
    redirect(new moodle_url('/report/cohortdetail/index.php'));
}

// We import the javascript file, so the user can change the cohort type.
$PAGE->requires->js_call_amd('report_cohortdetail/choice', 'init');

// We display the header of the page.
echo $OUTPUT->header();

// We display the heading of the page.
echo $OUTPUT->heading(get_string('pluginname', 'report_cohortdetail'));

// The data that will be sent to the mustache template.
$datafortemplate = [];

// We create the table to display the members or the courses linked to the cohort.
$table = new html_table();

// We set up to variables to verify what table we want to display (members or courses linked).
$tableokuser = false;
$tableokcourse = false;

// We create the fromform variable.
$fromform = null;

// If the form is submitted, we get the data (for the system form).
if ($mformsystem->is_submitted()) {
    $fromform = $mformsystem->get_data();
}

// If the form is submitted, we get the data (for the category form).
if ($mformcategory->is_submitted()) {
    $fromform = $mformcategory->get_data();
}
// We verify if the submitted form is not null and if the cohort is set (if the user has cliqued on one of the submit button).
if ($fromform != null && isset($fromform->cohort)) {
    // We verify if the user has cliqued on the members button or the courseslinked button.
    if (isset($fromform->members)) {
        // We get the cohort id.
        $cohortid = $fromform->cohort;
        // We get the cohort.
        $cohort = $DB->get_record('cohort', ['id' => $cohortid]);

        if (!$cohort) {
            // If the cohort is not found, we display an error message.
            $datafortemplate['alertcohort'] = html_writer::tag('div',
                get_string('cohortnotfound', 'report_cohortdetail'),
                ['class' => 'alert alert-danger mt-2']);
        } else {
            // We get the members of the cohort.
            $members = cohort_get_members($cohortid);

            // We verify if the cohort has members.
            if (empty($members)) {
                // If the cohort has no members, we display a bootrap alert in the form.
                $datafortemplate['alertmember'] = html_writer::tag('div',
                    get_string('nomembers', 'report_cohortdetail'),
                    ['class' => 'alert alert-danger mt-2']);
            } else {
                // We create a table to display the members of the cohort.
                $table->head = [
                    get_string('emailtable', 'report_cohortdetail'),
                    get_string('idnumbertable', 'report_cohortdetail'),
                    get_string('fullnametable', 'report_cohortdetail'),
                ];
                // We create an empty array to store the data of the table.
                $table->data = [];

                // We loop through the members to add them to the table.
                foreach ($members as $member) {
                    $table->data[] = [
                        // If the email is not empty we add it to the table in a bootstrap badge.
                        !empty($member->email) ?
                            html_writer::tag('span',
                                $member->email,
                                [
                                    'class' => 'badge bg-secondary text-dark',
                                ])
                        :
                            '',
                        // If the idnumber is not empty we add it to the table in a bootstrap badge.
                        !empty($member->idnumber) ?
                            html_writer::tag('span',
                                $member->idnumber,
                                [
                                    'class' => 'badge bg-primary text-white',
                                ])
                        :
                            '',
                        // We add the full name to the table.
                        $member->firstname . ' ' . $member->lastname,
                    ];
                }

                // Set the tableokuser to true.
                $tableokuser = true;
            }
        }

    } else if (isset($fromform->courseslinked)) {
        // We get the cohort id.
        $cohortid = $fromform->cohort;

        // Get the user context.
        $usrcontext = context_user::instance($USER->id);

        // We get the cohort.
        $cohort = $DB->get_record('cohort', ['id' => $cohortid]);

        // We verify if the cohort is found.
        if ($cohort) {
            // We get the courses linked to the cohort.
            $courses = get_courses_with_cohort($cohortid);

            // We sort the courses by name.
            usort($courses, function ($a, $b) {
                return strcmp($a->coursename, $b->coursename);
            });

            // We set the table head.
            $table->head = [
                get_string('coursename', 'report_cohortdetail'),
                get_string('categorypath', 'report_cohortdetail'),
            ];

            // We create an empty array to store the data of the table.
            $table->data = [];

            // We verify if we have courses linked to the cohort if not we display a bootstrap alert.
            if (empty($courses)) {
                $datafortemplate['alertcourse'] = html_writer::tag('div',
                    get_string('nocourses', 'report_cohortdetail'),
                    ['class' => 'alert alert-danger mt-2']);
            } else {

                /* We loop through the courses and if the user can config the cohort in the enrolment method,
                we add the course to the table. */

                foreach ($courses as $course) {
                    // We get the course context.
                    $coursecontext = context_course::instance($course->courseid);

                    // We verify if the user can config the cohort in the enrolment method.
                    if (has_capability('enrol/cohort:config', $coursecontext) || is_siteadmin()) {
                        // We get the category path.
                        $category = $DB->get_record('course_categories', ['id' => $course->categoryid]);
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
                        // We create the link to the course.
                        $link = html_writer::link(
                        new moodle_url('/course/view.php', ['id' => $course->courseid]),
                        $course->coursename
                        );
                        // We add the course to the table.
                        $table->data[] = [
                            $link,
                            $categoryname,
                        ];
                    }
                }
                // Set the tableokcourse to true.
                $tableokcourse = true;
            }
        } else {
            // If the cohort is not found, we display an error message.
            $datafortemplate['alertcohort'] = html_writer::tag('div',
                get_string('cohortnotfound', 'report_cohortdetail'),
                ['class' => 'alert alert-danger mt-2']);
        }

    }
    // We prepare the two forms for the mustache template.
    $datafortemplate['mformsystem'] = $mformsystem->render();
    $datafortemplate['mformcategory'] = $mformcategory->render();
} else {
    // We prepare the two forms for the mustache template.
    $datafortemplate['mformsystem'] = $mformsystem->render();
    $datafortemplate['mformcategory'] = $mformcategory->render();
}

// We prepare the table for the mustache template.
if ($tableokuser && !$tableokcourse) {
    $datafortemplate['table'] = html_writer::table($table);
} else if ($tableokcourse && !$tableokuser) {
    $datafortemplate['table'] = html_writer::table($table);
} else if ($tableokuser && $tableokcourse) {
    $datafortemplate['table'] = get_string('erreurgettingdatas', 'report_cohortdetail');
}

// We prepare the my courses button for the mustache template.
$datafortemplate['mycourses'] = html_writer::link(
    new moodle_url('/report/cohortdetail/mycourses.php'),
    get_string('mycourses', 'report_cohortdetail'),
    ['class' => 'btn btn-secondary']
);

// We create the data to store the system or category default value.
$datafortemplate['system'] = "";

$datafortemplate['category'] = "";

// We set the default value for the select.
if ($defaultcohorttype === "system") {
    $datafortemplate['system'] = "selected";
} else if ($defaultcohorttype === "category") {
    $datafortemplate['category'] = "selected";
}

// Call a mustache template to display the page.
echo $OUTPUT->render_from_template('report_cohortdetail/indexform', $datafortemplate);

// We display the footer of the page.
echo $OUTPUT->footer();
