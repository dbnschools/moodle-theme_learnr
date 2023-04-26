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
 * Theme LearnR - Flavours edit form
 *
 * @package    theme_learnr
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @copyright  on behalf of Zurich University of Applied Sciences (ZHAW)
 * @copyright  based on code by bdecent gmbh <https://bdecent.de> in format_kickstart.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_learnr\form;

defined('MOODLE_INTERNAL') || die();

// Require forms library.
require_once($CFG->libdir.'/formslib.php');

// Require cohort library.
require_once($CFG->dirroot.'/cohort/lib.php');

/**
 * Flavours edit form.
 *
 * @package    theme_learnr
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @copyright  on behalf of Zurich University of Applied Sciences (ZHAW)
 * @copyright  based on code by bdecent gmbh <https://bdecent.de> in format_kickstart.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class flavour_edit_form extends \moodleform {

    /**
     * Define form elements.
     *
     * @throws \coding_exception
     */
    public function definition() {
        // Get an easier handler for the form.
        $mform = $this->_form;

        // Prepare yes-no option for multiple usage.
        $yesnooption = array(false => get_string('no'), true => get_string('yes'));

        // Add the flavour ID as hidden element.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Add general settings as header element.
        $mform->addElement('header', 'generalsettingsheader', get_string('flavoursgeneralsettings', 'theme_learnr'));
        $mform->setExpanded('generalsettingsheader');

        // Add the title as input element.
        $mform->addElement('text', 'title', get_string('flavourstitle', 'theme_learnr'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('required'), 'required');
        $mform->addHelpButton('title', 'flavourstitle', 'theme_learnr');

        // Add the description title as editor element.
        $mform->addElement('editor', 'description', get_string('flavoursdescription', 'theme_learnr'));
        $mform->setType('description', PARAM_RAW);
        $mform->addHelpButton('description', 'flavoursdescription', 'theme_learnr');

        // Add look as header element.
        $mform->addElement('header', 'looksettingsheader', get_string('configtitlelook', 'theme_learnr'));
        $mform->setExpanded('looksettingsheader');

        // Add logo as filemanager element.
        $mform->addElement('filemanager', 'flavours_look_logo',
                get_string('logo', 'admin'), null, [
                        'subdirs' => 0,
                        'maxfiles' => 1,
                        'accepted_types' => 'web_image',
                        'return_types' => FILE_INTERNAL
                ]);
        $mform->addHelpButton('flavours_look_logo', 'flavourslogo', 'theme_learnr');

        // Add logocompact as filemanager element.
        $mform->addElement('filemanager', 'flavours_look_logocompact',
                get_string('logocompact', 'admin'), null, [
                        'subdirs' => 0,
                        'maxfiles' => 1,
                        'accepted_types' => 'web_image',
                        'return_types' => FILE_INTERNAL
                ]);
        $mform->addHelpButton('flavours_look_logocompact', 'flavourslogocompact', 'theme_learnr');

        // Add favicon as filemanager element.
        $mform->addElement('filemanager', 'flavours_look_favicon',
                get_string('faviconsetting', 'theme_learnr'), null, [
                        'subdirs' => 0,
                        'maxfiles' => 1,
                        'accepted_types' => 'image',
                        'return_types' => FILE_INTERNAL
                ]);
        $mform->addHelpButton('flavours_look_favicon', 'flavoursfavicon', 'theme_learnr');

        // Add backgroundimage as filemanager element.
        $mform->addElement('filemanager', 'flavours_look_backgroundimage',
                get_string('backgroundimagesetting', 'theme_learnr'), null, [
                        'subdirs' => 0,
                        'maxfiles' => 1,
                        'accepted_types' => 'web_image',
                        'return_types' => FILE_INTERNAL
                ]);
        $mform->addHelpButton('flavours_look_backgroundimage', 'flavoursbackgroundimage', 'theme_learnr');

        // Add custom css as textarea element.
        // Note: In the current state of implementation, this setting only allows the usage of custom CSS, not SCSS.
        // It will be appended to the stack of CSS code which is shipped to the browser.
        // There is a follow-up issue on Github to add SCSS support.
        // When this is realized, the widget's title string should be changed to 'theme_boost/rawscss'.
        $mform->addElement('textarea', 'look_rawscss', get_string('flavourscustomcss', 'theme_learnr'), array('rows' => 15));
        $mform->setType('title', PARAM_TEXT);
        $mform->addHelpButton('look_rawscss', 'flavourscustomcss', 'theme_learnr');

        // Add apply-to-cohort as header element.
        $mform->addElement('header', 'applytocohortheader', get_string('flavoursapplytocohorts', 'theme_learnr'));
        // Set the header to expanded if apply-to-cohort is already enabled.
        if (isset($this->_customdata['flavour']) && $this->_customdata['flavour']->applytocohorts == true) {
            $mform->setExpanded('applytocohortheader');
        }

        // Add apply-to-cohort as select element.
        $mform->addElement('select', 'applytocohorts', get_string('flavoursapplytocohorts', 'theme_learnr'), $yesnooption);
        $mform->setDefault('applytocohorts', false);
        $mform->setType('applytocohorts', PARAM_BOOL);
        $mform->addHelpButton('applytocohorts', 'flavoursapplytocohorts', 'theme_learnr');

        // Add cohort list as autocomplete field.
        $cohortdata = cohort_get_all_cohorts(0, 0);
        $cohortoptions = [];
        foreach ($cohortdata['cohorts'] as $cohort) {
            $cohortoptions[$cohort->id] = $cohort->name;
        }
        $mform->addElement('autocomplete', 'applytocohorts_ids', get_string('cohorts', 'cohort'), $cohortoptions,
                ['multiple' => true]);
        $mform->hideIf('applytocohorts_ids', 'applytocohorts', 'neq', 1);
        $mform->addHelpButton('applytocohorts_ids', 'flavoursapplytocohorts_ids', 'theme_learnr');

        // Add apply-to-category as header element.
        $mform->addElement('header', 'applytocategoryheader', get_string('flavoursapplytocategories', 'theme_learnr'));
        // Set the header to expanded if apply-to-category is already enabled.
        if (isset($this->_customdata['flavour']) && $this->_customdata['flavour']->applytocategories == true) {
            $mform->setExpanded('applytocategoryheader');
        }

        // Add apply-to-category as select element.
        $mform->addElement('select', 'applytocategories', get_string('flavoursapplytocategories', 'theme_learnr'),
                $yesnooption);
        $mform->setDefault('applytocategories', false);
        $mform->setType('applytocategories', PARAM_BOOL);
        $mform->addHelpButton('applytocategories', 'flavoursapplytocategories', 'theme_learnr');

        // Add category list as autocomplete field.
        $categoryoptions = \core_course_category::make_categories_list();
        $mform->addElement('autocomplete', 'applytocategories_ids', get_string('categories'), $categoryoptions,
                ['multiple' => true]);
        $mform->hideIf('applytocategories_ids', 'applytocategories', 'neq', 1);
        $mform->addHelpButton('applytocategories_ids', 'flavoursapplytocategories_ids', 'theme_learnr');

        // Add include-subcategories as checkbox.
        $mform->addElement('advcheckbox', 'applytocategories_subcats',
                get_string('flavoursincludesubcategories', 'theme_learnr'));
        $mform->setType('applytocategories_subcats', PARAM_BOOL);
        $mform->addHelpButton('applytocategories_subcats', 'flavoursincludesubcategories', 'theme_learnr');
        $mform->hideIf('applytocategories_subcats', 'applytocategories', 'neq', 1);

        // Add the action buttons.
        $this->add_action_buttons();
    }
}
