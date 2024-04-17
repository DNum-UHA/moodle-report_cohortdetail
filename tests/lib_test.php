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

namespace report_cohortdetail;

/**
 * Tests for Cohort Detail
 *
 * @package    report_cohortdetail
 * @category   test
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/report/cohortdetail/lib.php');
    }

    /**
     * Test the cohort_get_members function.
     * It verifies that the function returns an array.
     *
     * @return void
     * @covers ::cohort_get_members
     */
    public function test_cohort_get_members_array() {
        // Using moodle method we get the id of a existing cohort.
        $cohortid = $this->getDataGenerator()->create_cohort()->id;
        // We verify that the function returns an array.
        $this->assertIsArray(cohort_get_members($cohortid));
        // We reset the database after the test.
        $this->resetAfterTest();
    }

    /**
     * Test the cohort_get_members function.
     * It verifies what happens when function is called with an empty cohort id.
     *
     * @return void
     * @covers ::cohort_get_members
     */
    public function test_cohort_get_members_empty() {
        // We call the function with an empty cohort id.
        $this->expectException(\moodle_exception::class);
        cohort_get_members('');
    }

    /**
     * Test the cohort_get_members function.
     * It verifies if we receive the message error:notnumericcohortid when we call the function with a non numeric cohort id.
     *
     * @return void
     * @covers ::cohort_get_members
     */
    public function test_cohort_get_members_not_numeric() {
        // We call the function with a non numeric cohort id.
        $this->expectExceptionMessage(get_string('error:notnumericcohortid', 'report_cohortdetail'));
        cohort_get_members('notnumeric');
    }

    /**
     * Test the cohort_get_members function.
     * It verifies if we receive an empty array when we call the function with a cohort id that does not exist.
     *
     * @return void
     * @covers ::cohort_get_members
     */
    public function test_cohort_get_members_not_found() {
        // We call the function with a non existing cohort id.
        $this->assertEmpty(cohort_get_members(-1));
    }

    /**
     * Test the cohort_get_members function.
     * It verifies if we receive an array of users when we call the function with a cohort id that exists.
     *
     * @return void
     * @covers ::cohort_get_members
     */
    public function test_cohort_get_members_found() {
        // Using moodle method we get the id of a existing cohort.
        $cohortid = $this->getDataGenerator()->create_cohort()->id;
        // We create and add a user to the cohort.
        $user = $this->getDataGenerator()->create_user();
        cohort_add_member($cohortid, $user->id);

        // We verify that the function returns an array.
        $this->assertIsArray(cohort_get_members($cohortid));
        // We reset the database after the test.
        $this->resetAfterTest();
    }

    /**
     * Test the cohort_get_members function.
     * It verifies if we receive an empty array when we call the function with a cohort id that exists but has no members.
     *
     * @return void
     * @covers ::cohort_get_members
     */
    public function test_cohort_get_members_no_members() {
        // Using moodle method we get the id of a existing cohort.
        $cohortid = $this->getDataGenerator()->create_cohort()->id;
        // We verify that the function returns an empty array.
        $this->assertEmpty(cohort_get_members($cohortid));
        // We reset the database after the test.
        $this->resetAfterTest();
    }

    /**
     * Test the get_courses_with_cohort function.
     * It verifies that the function returns an array.
     *
     * @return void
     * @covers ::get_courses_with_cohort
     */
    public function test_get_courses_with_cohort_array() {
        // Using moodle method we get the id of a existing cohort.
        $cohortid = $this->getDataGenerator()->create_cohort()->id;
        // We verify that the function returns an array.
        $this->assertIsArray(get_courses_with_cohort($cohortid));
        // We reset the database after the test.
        $this->resetAfterTest();
    }

    /**
     * Test the get_courses_with_cohort function.
     * It verifies what happens when function is called with an empty cohort id.
     *
     * @return void
     * @covers ::get_courses_with_cohort
     */
    public function test_get_courses_with_cohort_empty() {
        // We call the function with an empty cohort id.
        $this->expectException(\moodle_exception::class);
        get_courses_with_cohort('');
    }

    /**
     * Test the get_courses_with_cohort function.
     * It verifies if we receive the message error:notnumericcohortid when we call the function with a non numeric cohort id.
     *
     * @return void
     * @covers ::get_courses_with_cohort
     */
    public function test_get_courses_with_cohort_not_numeric() {
        // We call the function with a non numeric cohort id.
        $this->expectExceptionMessage(get_string('error:notnumericcohortid', 'report_cohortdetail'));
        get_courses_with_cohort('notnumeric');
    }

    /**
     * Test the get_courses_with_cohort function.
     * It verifies if we receive an empty array when we call the function with a cohort id that does not exist.
     *
     * @return void
     * @covers ::get_courses_with_cohort
     */
    public function test_get_courses_with_cohort_not_found() {
        // We call the function with a non existing cohort id.
        $this->assertEmpty(get_courses_with_cohort(-1));
    }

    /**
     * Test the get_courses_with_cohort function.
     * It verifies if we receive an array of courses when we call the function with a cohort id that exists.
     *
     * @return void
     * @covers ::get_courses_with_cohort
     */
    public function test_get_courses_with_cohort_found() {
        // Using moodle method we get the id of a existing cohort.
        $cohortid = $this->getDataGenerator()->create_cohort()->id;
        // We create and add a course to the cohort.
        $course = $this->getDataGenerator()->create_course();
        global $DB;
        $DB->insert_record('enrol', [
            'enrol' => 'cohort',
            'courseid' => $course->id,
            'customint1' => $cohortid,
        ]);

        // We verify that the function returns an array.
        $this->assertIsArray(get_courses_with_cohort($cohortid));
        // We reset the database after the test.
        $this->resetAfterTest();
    }

    /**
     * Test the get_courses_with_cohort function.
     * It verifies if we receive an empty array when we call the function with a cohort id that exists but has no courses.
     *
     * @return void
     * @covers ::get_courses_with_cohort
     */
    public function test_get_courses_with_cohort_no_courses() {
        // Using moodle method we get the id of a existing cohort.
        $cohortid = $this->getDataGenerator()->create_cohort()->id;
        // We verify that the function returns an empty array.
        $this->assertEmpty(get_courses_with_cohort($cohortid));
        // We reset the database after the test.
        $this->resetAfterTest();
    }

    /**
     * Test the report_cohortdetail_update_roles function.
     * It verifies that the function links a capability to a role.
     *
     * @return void
     * @covers ::report_cohortdetail_update_roles
     */
    public function test_report_cohortdetail_update_roles() {
        $systemcontext = \context_system::instance();

        // First we create two roles.
        $role1 = $this->getDataGenerator()->create_role();
        $role2 = $this->getDataGenerator()->create_role();

        // We create users with the roles.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // We assign the roles to the users in the system context.
        role_assign($role1, $user1->id, $systemcontext->id);
        role_assign($role2, $user2->id, $systemcontext->id);

        // We add this roles to the settings.
        set_config('roles', $role1 . ',' . $role2, 'report_cohortdetail');

        // We call the function.
        report_cohortdetail_update_roles();

        // We verify that the capability is linked to the roles.
        $this->assertTrue(has_capability('report/cohortdetail:view', $systemcontext, $user1));
        $this->assertTrue(has_capability('report/cohortdetail:view', $systemcontext, $user2));

        // We reset the database after the test.
        $this->resetAfterTest();
    }

    /**
     * Test the report_cohortdetail_update_roles function.
     * It verify that the function removes the capability from a role if the role does not exist in the settings.
     *
     * @return void
     * @covers ::report_cohortdetail_update_roles
     */
    public function test_report_cohortdetail_update_roles_remove() {
        $systemcontext = \context_system::instance();

        // First we create two roles.
        $role1 = $this->getDataGenerator()->create_role();
        $role2 = $this->getDataGenerator()->create_role();

        // We create users with the roles.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // We assign the roles to the users in the system context.
        role_assign($role1, $user1->id, $systemcontext->id);
        role_assign($role2, $user2->id, $systemcontext->id);

        // We assign the capability to the second role to verify that it is removed.
        assign_capability('report/cohortdetail:view', CAP_ALLOW, $role2, $systemcontext->id);

        // We add this roles to the settings.
        set_config('roles', $role1, 'report_cohortdetail');

        // We call the function.
        report_cohortdetail_update_roles();

        // We verify that the capability is linked to the roles.
        $this->assertTrue(has_capability('report/cohortdetail:view', $systemcontext, $user1));
        $this->assertFalse(has_capability('report/cohortdetail:view', $systemcontext, $user2));

        // We reset the database after the test.
        $this->resetAfterTest();
    }

    /**
     * Test the report_cohortdetail_update_roles function.
     * It verify that the function does not change anything if the roles already have the capability.
     *
     * @return void
     * @covers ::report_cohortdetail_update_roles
     */
    public function test_report_cohortdetail_update_roles_no_change() {
        $systemcontext = \context_system::instance();

        // First we create two roles.
        $role1 = $this->getDataGenerator()->create_role();
        $role2 = $this->getDataGenerator()->create_role();

        // We create users with the roles.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // We assign the roles to the users in the system context.
        role_assign($role1, $user1->id, $systemcontext->id);
        role_assign($role2, $user2->id, $systemcontext->id);

        // We assign the capability to the roles.
        assign_capability('report/cohortdetail:view', CAP_ALLOW, $role1, $systemcontext->id);
        assign_capability('report/cohortdetail:view', CAP_ALLOW, $role2, $systemcontext->id);

        // We add this roles to the settings.
        set_config('roles', $role1 . ',' . $role2, 'report_cohortdetail');

        // We call the function.
        report_cohortdetail_update_roles();

        // We verify that the capability is linked to the roles.
        $this->assertTrue(has_capability('report/cohortdetail:view', $systemcontext, $user1));
        $this->assertTrue(has_capability('report/cohortdetail:view', $systemcontext, $user2));

        // We reset the database after the test.
        $this->resetAfterTest();
    }

    /**
     * Test the report_cohortdetail_update_roles function.
     * It verify that the function no roles have the capability, if the settings are empty.
     *
     * @return void
     * @covers ::report_cohortdetail_update_users
     */
    public function test_report_cohortdetail_update_roles_empty() {
        $systemcontext = \context_system::instance();

        // We add this roles to the settings.
        set_config('roles', '', 'report_cohortdetail');

        // We verify that the function return nothing.
        $this->assertNull(report_cohortdetail_update_roles());

        // We reset the database after the test.
        $this->resetAfterTest();
    }
}
