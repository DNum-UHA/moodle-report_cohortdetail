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
 * This file defines the form for the cohortdetail report.
 *
 * @package    report_cohortdetail
 * @copyright  2024 DNnum UHA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_cohortdetail;

// Include the necessary libraries.
use moodleform;
use context_system;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->libdir.'/accesslib.php');

/**
 * A class that defines the form for the cohortdetail report.
 *
 * It is used to search for a cohort and display the members of the system cohort.
 *
 * @package    report_cohortdetail
 * @category   form
 */
class cohortdetail_form_system extends moodleform {

    /**
     * Method definition
     * Add elements to form
     * The form is used to search for a cohort and display the members of the cohort.
     * @return void
     */
    public function definition() {
        // Get the USER global variable.
        global $DB;

        // Create a new form object.
        $mform = $this->_form;

        // Get the system context.
        $context = context_system::instance();
        // Get all the cohorts.
        $sql = "SELECT c.id as ci, c.name as cn, c.description as cd
        FROM {cohort} c
        WHERE c.visible = :visible AND c.contextid = :context
        ORDER BY cn";

        // Execute the SQL query.
        $cohorts = $DB->get_records_sql($sql, ['visible' => 1, 'context' => $context->id]);

        // Check if there are cohorts.
        if (empty($cohorts)) {
            // Display a bootstrap alert if there are no cohorts.
            $mform->addElement(
                'html',
                '<div class="alert alert-danger" role="alert">'
                . get_string('nocohorts', 'report_cohortdetail')
                . '</div>'
            );
            return;
        }

        // Create an array of cohorts with the cohort name and description for the autocomplete element.
        $cohorts = array_map(fn($cohort) => $cohort->cn . ' (' . $cohort->cd . ')', $cohorts);

        // Set the options for the autocomplete element.
        $options = [
            'class' => 'autocompletecohort',
            'id' => 'cohortsystem',
        ];

        // Add an autocomplete element to the form.
        $mform->addElement('autocomplete', 'cohort', get_string('searchtitle', 'report_cohortdetail'), $cohorts, $options);

        // Add a button group to the form.
        $buttonarray = [];

        // Add a button to search all member of the cohort in the button group.
        $buttonarray[] = $mform->createElement(
            'submit',
            'members',
            get_string('searchmember', 'report_cohortdetail'),
            ['id' => 'memberssystem']
        );

        // Add a button to get all the courses linked to the cohort in the button group.
        $buttonarray[] = $mform->createElement(
            'submit',
            'courseslinked',
            get_string('searchcourse', 'report_cohortdetail'),
            ['id' => 'courseslinkedsystem']
        );

        // Add a cancel button to the button group.
        $buttonarray[] = $mform->createElement(
            'cancel',
        );

        // Set the options for the button group.
        $optionsbuttonar = [
            'class' => 'buttonarcohort',
            'id' => 'buttonarcohortsystem',
        ];

        // Add the button group to the form.
        $mform->addGroup($buttonarray, 'buttonarcohort', '', null, false, $optionsbuttonar);
    }
}

/**
 * A class that defines the form for the cohortdetail report.
 *
 * It is used to search for a cohort and display the members of the cohort from categories.
 *
 * @package    report_cohortdetail
 * @category   form
 */
class cohortdetail_form_category extends moodleform {

    /**
     * Method definition
     * Add elements to form
     * The form is used to search for a cohort and display the members of the cohort.
     * @return void
     */
    public function definition() {
        // Get the USER global variable.
        global $DB;

        // Create a new form object.
        $mform = $this->_form;

        // Get the system context.
        $context = context_system::instance();
        // Get all the cohorts.
        $sql = "SELECT c.id as ci, c.name as cn, c.description as cd
        FROM {cohort} c
        WHERE c.visible = :visible AND c.contextid != :context
        ORDER BY cn";

        // Execute the SQL query.
        $cohorts = $DB->get_records_sql($sql, ['visible' => 1, 'context' => $context->id]);

        // Check if there are cohorts.
        if (empty($cohorts)) {
            // Display a bootstrap alert if there are no cohorts.
            $mform->addElement(
                'html',
                '<div class="alert alert-danger" role="alert">'
                . get_string('nocohorts', 'report_cohortdetail')
                . '</div>'
            );
            return;
        }

        // Create an array of cohorts with the cohort name and description for the autocomplete element.
        $cohorts = array_map(fn($cohort) => $cohort->cn . ' (' . $cohort->cd . ')', $cohorts);

        // Set the options for the autocomplete element.
        $options = [
            'class' => 'autocompletecohort',
            'id' => 'cohortcategory',
        ];

        // Add an autocomplete element to the form.
        $mform->addElement('autocomplete', 'cohort', get_string('searchtitle', 'report_cohortdetail'), $cohorts, $options);

        // Add a button group to the form.
        $buttonarray = [];

        // Add a button to search all member of the cohort in the button group.
        $buttonarray[] = $mform->createElement(
            'submit',
            'members',
            get_string('searchmember', 'report_cohortdetail'),
            ['id' => 'memberscategory']
        );

        // Add a button to get all the courses linked to the cohort in the button group.
        $buttonarray[] = $mform->createElement(
            'submit',
            'courseslinked',
            get_string('searchcourse', 'report_cohortdetail'),
            ['id' => 'courseslinkedcategory']
        );

        // Add a cancel button to the button group.
        $buttonarray[] = $mform->createElement(
            'cancel',
        );

        // Set the options for the button group.
        $optionsbuttonar = [
            'class' => 'buttonarcohort',
            'id' => 'buttonarcohortcategory',
        ];

        // Add the button group to the form.
        $mform->addGroup($buttonarray, 'buttonarcohortcategory', '', null, false, $optionsbuttonar);
    }
}
