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
 * A module for the report_cohortdetail report.
 * It change the form by disable the display of the cohort type not selected.
 *
 * @module     report_cohortdetail/choice
 * @copyright  2024 DNum UHA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = () => {
    // We get the cohort choice element
    const cohortChoice = document.querySelectorAll('input[name="choice"]');

    // We add an event listener to the cohort choice elements
    cohortChoice.forEach((choice) => {
        choice.addEventListener('change', (event) => {
            // We get the choice value
            const selectedCohort = event.target.value;

            // We add the value to the localsession

            sessionStorage.setItem('selectedCohort', selectedCohort);

            // If the value is system we verify if the system form is visible and if the category form is hidden
            if (selectedCohort === 'system') {
                let sysform = document.querySelector('.searchformsys');
                // We verify if it contains the class d-none
                if (sysform.classList.contains('d-none')) {
                    sysform.classList.remove('d-none');
                }
                let catform = document.querySelector('.searchformcategory');
                // We verify if it contains the class d-none
                if (!catform.classList.contains('d-none')) {
                    catform.classList.add('d-none');
                }
            } else if (selectedCohort === 'category') {
                let sysform = document.querySelector('.searchformsys');
                // We verify if it contains the class d-none
                if (!sysform.classList.contains('d-none')) {
                    sysform.classList.add('d-none');
                }
                let catform = document.querySelector('.searchformcategory');
                // We verify if it contains the class d-none
                if (catform.classList.contains('d-none')) {
                    catform.classList.remove('d-none');
                }
            }
        });
    });

    // We verify if the localsession contains the selectedCohort value
    let valuesession = sessionStorage.getItem('selectedCohort');
    if (valuesession) {
        // We check the good radio button
        document.querySelector('input[name="choice"][value="' + valuesession + '"]').checked = true;
    }

    // At the initial loading of the page we verify the value of the cohort choice element
    const selectedCohort = document.querySelector('input[name="choice"]:checked').value;

    // If the value is system we verify if the system form is visible and if the category form is hidden
    if (selectedCohort === 'system') {
        let sysform = document.querySelector('.searchformsys');
        // We verify if it contains the class d-none
        if (sysform.classList.contains('d-none')) {
            sysform.classList.remove('d-none');
        }
        let catform = document.querySelector('.searchformcategory');
        // We verify if it contains the class d-none
        if (!catform.classList.contains('d-none')) {
            catform.classList.add('d-none');
        }
    } else if (selectedCohort === 'category') {
        let sysform = document.querySelector('.searchformsys');
        // We verify if it contains the class d-none
        if (!sysform.classList.contains('d-none')) {
            sysform.classList.add('d-none');
        }
        let catform = document.querySelector('.searchformcategory');
        // We verify if it contains the class d-none
        if (catform.classList.contains('d-none')) {
            catform.classList.remove('d-none');
        }
    }
};
