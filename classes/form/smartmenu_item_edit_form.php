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
 * Theme Boost Union - Smart menu item edit form
 *
 * @package    theme_learnr
 * @copyright  2023 bdecent GmbH <https://bdecent.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_learnr\form;

defined('MOODLE_INTERNAL') || die();

// Require forms library.
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/cohort/lib.php');

use theme_learnr\smartmenu_item;
use theme_learnr\smartmenu;

/**
 * Smart menu items edit form.
 */
class smartmenu_item_edit_form extends \moodleform {

    /**
     * Define form elements.
     *
     * @throws \coding_exception
     */
    public function definition() {
        global $DB, $PAGE, $CFG;

        // Require and register the QuickForm colorpicker element.
        require_once($CFG->dirroot.'/theme/learnr/form/element-colorpicker.php');
        \MoodleQuickForm::registerElementType(
                'theme_learnr_colorpicker',
                $CFG->dirroot.'/theme/learnr/form/element-colorpicker.php',
                'moodlequickform_themeboostunion_colorpicker'
        );

        // Get an easier handler for the form.
        $mform = $this->_form;

        // Add the smart menu item ID as hidden element.
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Add the smart menu ID as hidden element (and set it to 0 if it is not given).
        $mform->addElement('hidden', 'menu', 0);
        $mform->setType('menu', PARAM_INT);
        $menuid = (isset($this->_customdata['menu'])) ? $this->_customdata['menu'] : 0;
        $mform->setDefault('menu', $menuid);

        // Add general settings as header element.
        $mform->addElement('header', 'generalsettingsheader',
                get_string('smartmenusgeneralsectionheader', 'theme_learnr'));
        $mform->setExpanded('generalsettingsheader');

        // Add the title as input element.
        $mform->addElement('text', 'title', get_string('smartmenusmenuitemtitle', 'theme_learnr'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('required'), 'required');
        $mform->addHelpButton('title', 'smartmenusmenuitemtitle', 'theme_learnr');

        // Add structure as header element.
        $mform->addElement('header', 'structureheader',
                get_string('smartmenusmenuitemstructureheader', 'theme_learnr'));
        $mform->setExpanded('structureheader');

        // Add the menu item type as select element.
        $typesoptions = smartmenu_item::get_types();
        $mform->addElement('select', 'type', get_string('smartmenusmenuitemtype', 'theme_learnr'), $typesoptions);
        $mform->setDefault('type', smartmenu_item::TYPESTATIC);
        $mform->setType('type', PARAM_INT);
        $mform->addRule('type', get_string('required'), 'required');
        $mform->addHelpButton('type', 'smartmenusmenuitemtype', 'theme_learnr');

        // Add menu item URL (for the static menu item type) as input element.
        $mform->addElement('text', 'url', get_string('smartmenusmenuitemurl', 'theme_learnr'));
        $mform->setType('url', PARAM_URL);
        $mform->hideIf('url', 'type', 'neq', smartmenu_item::TYPESTATIC);
        $mform->addHelpButton('url', 'smartmenusmenuitemurl', 'theme_learnr');

        // Add mode as select element.
        $modeoptions = [
                smartmenu_item::MODE_INLINE => get_string('smartmenusmodeinline', 'theme_learnr'),
                smartmenu_item::MODE_SUBMENU => get_string('smartmenusmodesubmenu', 'theme_learnr'),
        ];
        $mform->addElement('select', 'mode', get_string('smartmenusmenuitemmode', 'theme_learnr'), $modeoptions);
        $mform->setDefault('mode', smartmenu_item::MODE_INLINE);
        $mform->setType('mode', PARAM_INT);
        $mform->addHelpButton('mode', 'smartmenusmenuitemmode', 'theme_learnr');

        // Add category (for the dynamic courses menu item type) as autocomplete element.
        $categoriesoptions = \core_course_category::make_categories_list();
        $catwidget = $mform->addElement('autocomplete', 'category',
                get_string('smartmenusmenuitemtypedynamiccourses', 'theme_learnr').': '.
                get_string('smartmenusdynamiccoursescoursecategory', 'theme_learnr'), $categoriesoptions);
        $mform->setType('category', PARAM_INT);
        $mform->hideIf('category', 'type', 'neq', smartmenu_item::TYPEDYNAMIC);
        $catwidget->setMultiple(true);
        $mform->addHelpButton('category', 'smartmenusdynamiccoursescoursecategory', 'theme_learnr');

        // Add roles (for the dynamic courses menu item type) as autocomplete element.
        $courseroles = get_roles_for_contextlevels(CONTEXT_COURSE);
        list($insql, $inparams) = $DB->get_in_or_equal(array_values($courseroles));
        $roles = $DB->get_records_sql("SELECT * FROM {role} WHERE id $insql", $inparams);
        $rolesoptions = role_fix_names($roles, null, ROLENAME_ALIAS, true);
        $roleswidget = $mform->addElement('autocomplete', 'enrolmentrole',
                get_string('smartmenusmenuitemtypedynamiccourses', 'theme_learnr').': '.
                get_string('smartmenusdynamiccoursesenrolmentrole', 'theme_learnr'), $rolesoptions);
        $mform->setType('enrolmentrole', PARAM_INT);
        $mform->hideIf('enrolmentrole', 'type', 'neq', smartmenu_item::TYPEDYNAMIC);
        $roleswidget->setMultiple(true);
        $mform->addHelpButton('enrolmentrole', 'smartmenusdynamiccoursesenrolmentrole', 'theme_learnr');

        // Add completion status (for the dynamic courses menu item type) as autocomplete element.
        $completionstatusoptions = [
                smartmenu_item::COMPLETION_ENROLLED =>
                        get_string('smartmenusdynamiccoursescompletionstatusenrolled', 'theme_learnr'),
                smartmenu_item::COMPLETION_INPROGRESS =>
                        get_string('smartmenusdynamiccoursescompletionstatusinprogress', 'theme_learnr'),
                smartmenu_item::COMPLETION_COMPLETED =>
                        get_string('smartmenusdynamiccoursescompletionstatuscompleted', 'theme_learnr'),
        ];
        $completionstatuswidget = $mform->addElement('autocomplete', 'completionstatus',
                get_string('smartmenusmenuitemtypedynamiccourses', 'theme_learnr').': '.
                get_string('smartmenusdynamiccoursescompletionstatus', 'theme_learnr'), $completionstatusoptions);
        $mform->setType('completionstatus', PARAM_INT);
        $mform->hideIf('completionstatus', 'type', 'neq', smartmenu_item::TYPEDYNAMIC);
        $completionstatuswidget->setMultiple(true);
        $mform->addHelpButton('completionstatus', 'smartmenusdynamiccoursescompletionstatus', 'theme_learnr');

        // Add date range (for the dynamic courses menu item type) as autocomplete element.
        $daterangeoptions = [
                smartmenu_item::RANGE_PAST =>
                        get_string('smartmenusdynamiccoursesdaterangepast', 'theme_learnr'),
                smartmenu_item::RANGE_PRESENT =>
                        get_string('smartmenusdynamiccoursesdaterangepresent', 'theme_learnr'),
                smartmenu_item::RANGE_FUTURE =>
                        get_string('smartmenusdynamiccoursesdaterangefuture', 'theme_learnr'),
        ];
        $daterangewidget = $mform->addElement('autocomplete', 'daterange',
                get_string('smartmenusmenuitemtypedynamiccourses', 'theme_learnr').': '.
                get_string('smartmenusdynamiccoursesdaterange', 'theme_learnr'), $daterangeoptions);
        $mform->setType('daterange', PARAM_INT);
        $mform->hideIf('daterange', 'type', 'neq', smartmenu_item::TYPEDYNAMIC);
        $daterangewidget->setMultiple(true);
        $mform->addHelpButton('daterange', 'smartmenusdynamiccoursesdaterange', 'theme_learnr');

        // Add additional form elements for custom course fields.
        smartmenu_item::load_custom_field_config($mform);

        // Add presentation as header element.
        $mform->addElement('header', 'presentationheader',
                get_string('smartmenusmenuitempresentationheader', 'theme_learnr'));
        $mform->setExpanded('presentationheader');

        // Add icon as input element.
        // Build icon list.
        $theme = \theme_config::load($PAGE->theme->name);
        $faiconsystem = \core\output\icon_system_fontawesome::instance($theme->get_icon_system());
        $iconlist = $faiconsystem->get_core_icon_map();
        array_unshift($iconlist, '');
        // Create element.
        $iconwidget = $mform->addElement('select', 'menuicon',
                get_string('smartmenusmenuitemicon', 'theme_learnr'), $iconlist);
        $mform->setType('menuicon', PARAM_TEXT);
        $iconwidget->setMultiple(false);
        $mform->addHelpButton('menuicon', 'smartmenusmenuitemicon', 'theme_learnr');
        // Include the fontawesome icon picker to the element.
        $systemcontextid = \context_system::instance()->id;
        $PAGE->requires->js_call_amd('theme_learnr/fontawesome-popover', 'init', ['#id_menuicon', $systemcontextid]);

        // Add title presentation and select element.
        $displayoptions = [
            smartmenu_item::DISPLAY_SHOWTITLEICON =>
                    get_string('smartmenusmenuitemdisplayoptionsshowtitleicon', 'theme_learnr'),
            smartmenu_item::DISPLAY_HIDETITLE => get_string('smartmenusmenuitemdisplayoptionshidetitle', 'theme_learnr'),
            smartmenu_item::DISPLAY_HIDETITLEMOBILE =>
                    get_string('smartmenusmenuitemdisplayoptionshidetitlemobile', 'theme_learnr'),
        ];
        $mform->addElement('select', 'display', get_string('smartmenusmenuitemdisplayoptions', 'theme_learnr'),
                $displayoptions);
        $mform->setDefault('display', smartmenu_item::DISPLAY_SHOWTITLEICON);
        $mform->setType('display', PARAM_INT);
        $mform->addHelpButton('display', 'smartmenusmenuitemdisplayoptions', 'theme_learnr');

        // Add tooltip as input element.
        $mform->addElement('text', 'tooltip', get_string('smartmenusmenuitemtooltip', 'theme_learnr'));
        $mform->setType('tooltip', PARAM_TEXT);
        $mform->addHelpButton('tooltip', 'smartmenusmenuitemtooltip', 'theme_learnr');

        // Add link target as select element.
        $targetoptions = [
                smartmenu_item::TARGET_SAME => get_string('smartmenusmenuitemlinktargetsamewindow', 'theme_learnr'),
                smartmenu_item::TARGET_NEW => get_string('smartmenusmenuitemlinktargetnewtab', 'theme_learnr'),
        ];
        $mform->addElement('select', 'target', get_string('smartmenusmenuitemlinktarget', 'theme_learnr'),
                $targetoptions);
        $mform->setDefault('target', smartmenu_item::TARGET_SAME);
        $mform->setType('target', PARAM_INT);
        $mform->addHelpButton('target', 'smartmenusmenuitemlinktarget', 'theme_learnr');

        // Add responsive hiding as checkbox group.
        $responsivegroup = [];
        // Hide on desktop.
        $responsivegroup[] = $mform->createElement('advcheckbox', 'desktop',
                get_string('smartmenusmenuitemresponsivedesktop', 'theme_learnr'), null, ['group' => 1]);
        // Hide on tablet.
        $responsivegroup[] = $mform->createElement('advcheckbox', 'tablet',
                get_string('smartmenusmenuitemresponsivetablet', 'theme_learnr'), null, ['group' => 1]);
        // Hide on mobile.
        $responsivegroup[] = $mform->createElement('advcheckbox', 'mobile',
                get_string('smartmenusmenuitemresponsivemobile', 'theme_learnr'), null, ['group' => 1]);
        $mform->addGroup($responsivegroup, 'responsive',
                get_string('smartmenusmenuitemresponsive', 'theme_learnr'), '', false);
        $mform->addHelpButton('responsive', 'smartmenusmenuitemresponsive', 'theme_learnr');

        // Add order as input element.
        $mform->addElement('text', 'sortorder', get_string('smartmenusmenuitemorder', 'theme_learnr'));
        $mform->setType('sortorder', PARAM_INT);
        $mform->addRule('sortorder', get_string('required'), 'required');
        $mform->addRule('sortorder', get_string('err_numeric', 'form'), 'numeric', null, 'client');
        $mform->addHelpButton('sortorder', 'smartmenusmenuitemorder', 'theme_learnr');
        if (isset($this->_customdata['nextorder'])) {
            $mform->setDefault('sortorder', $this->_customdata['nextorder']);
        }

        // Add CSS class as input element.
        $mform->addElement('text', 'cssclass', get_string('smartmenusmenuitemcssclass', 'theme_learnr'));
        $mform->setType('cssclass', PARAM_TEXT);
        $mform->addHelpButton('cssclass', 'smartmenusmenuitemcssclass', 'theme_learnr');

        // Add course name presentation (for the dynamic courses menu item type) as select element.
        $displayfieldoptions = [
                smartmenu_item::FIELD_FULLNAME => get_string('smartmenusmenuitemdisplayfieldcoursefullname', 'theme_learnr'),
                smartmenu_item::FIELD_SHORTNAME => get_string('smartmenusmenuitemdisplayfieldcourseshortname', 'theme_learnr'),
        ];
        $mform->addElement('select', 'displayfield',
                get_string('smartmenusmenuitemtypedynamiccourses', 'theme_learnr').': '.
                get_string('smartmenusmenuitemdisplayfield', 'theme_learnr'), $displayfieldoptions);
        $mform->setDefault('displayfield', smartmenu_item::FIELD_FULLNAME);
        $mform->setType('displayfield', PARAM_INT);
        $mform->hideIf('displayfield', 'type', 'neq', smartmenu_item::TYPEDYNAMIC);
        $mform->addHelpButton('displayfield', 'smartmenusmenuitemdisplayfield', 'theme_learnr');

        // Add number of words (for the dynamic courses menu item type) as input element.
        $mform->addElement('text', 'textcount',
                get_string('smartmenusmenuitemtypedynamiccourses', 'theme_learnr').': '.
                get_string('smartmenusmenuitemtextcount', 'theme_learnr'));
        $mform->setType('textcount', PARAM_INT);
        $mform->addRule('textcount', get_string('err_numeric', 'form'), 'numeric', null, 'client');
        $mform->hideIf('textcount', 'type', 'neq', smartmenu_item::TYPEDYNAMIC);
        $mform->addHelpButton('textcount', 'smartmenusmenuitemtextcount', 'theme_learnr');

        // If the menu is configured to be presented as cards.
        if (isset($this->_customdata['menutype']) && $this->_customdata['menutype'] == smartmenu::TYPE_CARD) {
            // Add card appearance as header element.
            $mform->addElement('header', 'cardpresentationheader',
                    get_string('smartmenusmenuitemcardappearanceheader', 'theme_learnr'));
            $mform->setExpanded('cardpresentationheader');

            // Add card image as filepicker element.
            $filepickeroptions = smartmenu_item::image_filepickeroptions();
            $mform->addElement('filemanager', 'image', get_string('smartmenusmenuitemcardimage', 'theme_learnr'), null,
                    $filepickeroptions);
            $mform->addHelpButton('image', 'smartmenusmenuitemcardimage', 'theme_learnr');

            // Add card text position as select element.
            $textpositionoptions = [
                    smartmenu_item::POSITION_BELOW =>
                            get_string('smartmenusmenuitemtextpositionbelowimage', 'theme_learnr'),
                    smartmenu_item::POSITION_OVERLAYTOP =>
                            get_string('smartmenusmenuitemtextpositionoverlaytop', 'theme_learnr'),
                    smartmenu_item::POSITION_OVERLAYBOTTOM =>
                            get_string('smartmenusmenuitemtextpositionoverlaybottom', 'theme_learnr'),
            ];
            $mform->addElement('select', 'textposition',
                    get_string('smartmenusmenuitemtextposition', 'theme_learnr'), $textpositionoptions);
            $mform->setDefault('textposition', smartmenu_item::POSITION_BELOW);
            $mform->setType('textposition', PARAM_INT);
            $mform->addHelpButton('textposition', 'smartmenusmenuitemtextposition', 'theme_learnr');

            // Add card text color as color picker element.
            $mform->addElement('theme_learnr_colorpicker', 'textcolor',
                    get_string('smartmenusmenuitemcardtextcolor', 'theme_learnr'));
            $mform->setType('textcolor', PARAM_TEXT);
            $mform->addHelpButton('textcolor', 'smartmenusmenuitemcardtextcolor', 'theme_learnr');

            // Add card background color as color picker element.
            $mform->addElement('theme_learnr_colorpicker', 'backgroundcolor',
                    get_string('smartmenusmenuitemcardbackgroundcolor', 'theme_learnr'));
            $mform->setType('backgroundcolor', PARAM_TEXT);
            $mform->addHelpButton('backgroundcolor', 'smartmenusmenuitemcardbackgroundcolor', 'theme_learnr');
        }

        // Add restrict visibility by roles as header element.
        $mform->addElement('header', 'restrictbyrolesheader',
                get_string('smartmenusrestrictbyrolesheader', 'theme_learnr'));
        // Set the header to expanded if the restriction is already set.
        if (isset($this->_customdata['menuitem']) &&
                count(json_decode($this->_customdata['menuitem']->roles)) > 0) {
            $mform->setExpanded('restrictbyrolesheader');
        }

        // Add by roles as autocomplete element.
        $rolelist = role_get_names(\context_system::instance());
        $roleoptions = [];
        foreach ($rolelist as $role) {
            $roleoptions[$role->id] = $role->localname;
        }
        $byroleswidget = $mform->addElement('autocomplete', 'roles', get_string('smartmenusbyrole', 'theme_learnr'),
                $roleoptions);
        $byroleswidget->setMultiple(true);
        $mform->addHelpButton('roles', 'smartmenusbyrole', 'theme_learnr');

        // Add context as select element.
        $rolecontext = [
                smartmenu::ANYCONTEXT => get_string('any'),
                smartmenu::SYSTEMCONTEXT => get_string('coresystem'),
        ];
        $mform->addElement('select', 'rolecontext', get_string('smartmenusrolecontext', 'theme_learnr'), $rolecontext);
        $mform->setDefault('rolecontext', smartmenu::ANYCONTEXT);
        $mform->setType('rolecontext', PARAM_INT);
        $mform->addHelpButton('rolecontext', 'smartmenusrolecontext', 'theme_learnr');

        // Add restrict visibility by cohorts as header element.
        $mform->addElement('header', 'restrictbycohortsheader',
                get_string('smartmenusrestrictbycohortsheader', 'theme_learnr'));
        // Set the header to expanded if the restriction is already set.
        if (isset($this->_customdata['menuitem']) &&
                count(json_decode($this->_customdata['menuitem']->cohorts)) > 0) {
            $mform->setExpanded('restrictbycohortsheader');
        }

        // Add by cohorts as autocomplete element.
        $cohortslist = \cohort_get_all_cohorts();
        $cohortoptions = $cohortslist['cohorts'];
        if ($cohortoptions) {
            array_walk($cohortoptions, function(&$value) {
                $value = $value->name;
            });
        }
        $bycohortswidget = $mform->addElement('autocomplete', 'cohorts', get_string('smartmenusbycohort', 'theme_learnr'),
                $cohortoptions);
        $bycohortswidget->setMultiple(true);
        $mform->addHelpButton('cohorts', 'smartmenusbycohort', 'theme_learnr');

        // Add operator as select element.
        $operatoroptions = [
                smartmenu::ANY => get_string('any'),
                smartmenu::ALL => get_string('all'),
        ];
        $mform->addElement('select', 'operator', get_string('smartmenusoperator', 'theme_learnr'), $operatoroptions);
        $mform->setDefault('operator', smartmenu::ANY);
        $mform->setType('operator', PARAM_INT);
        $mform->addHelpButton('operator', 'smartmenusoperator', 'theme_learnr');

        // Add restrict visibility by language as header element.
        $mform->addElement('header', 'restrictbylanguageheader',
                get_string('smartmenusrestrictbylanguageheader', 'theme_learnr'));
        // Set the header to expanded if the restriction is already set.
        if (isset($this->_customdata['menuitem']) &&
                count(json_decode($this->_customdata['menuitem']->languages)) > 0) {
            $mform->setExpanded('restrictbylanguageheader');
        }

        // Add by language as autocomplete element.
        $languagelist = get_string_manager()->get_list_of_translations();
        $langoptions = [];
        foreach ($languagelist as $key => $lang) {
            $langoptions[$key] = $lang;
        }
        $bylanguagewidget = $mform->addElement('autocomplete', 'languages',
                get_string('smartmenusbylanguage', 'theme_learnr'), $langoptions);
        $bylanguagewidget->setMultiple(true);
        $mform->addHelpButton('languages', 'smartmenusbylanguage', 'theme_learnr');

        // Add restrict visibility by date as header element.
        $mform->addElement('header', 'restrictbydateheader',
                get_string('smartmenusrestrictbydateheader', 'theme_learnr'));
        // Set the header to expanded if the restriction is already set.
        if (isset($this->_customdata['menuitem']) &&
                ($this->_customdata['menuitem']->start_date > 0 || $this->_customdata['menuitem']->end_date > 0)) {
            $mform->setExpanded('restrictbydateheader');
        }

        // Add from as datepicker element.
        $mform->addElement('date_time_selector', 'start_date',
                get_string('smartmenusbydatefrom', 'theme_learnr'), ['optional' => true]);
        $mform->addHelpButton('start_date', 'smartmenusbydatefrom', 'theme_learnr');

        // Add until as datepicker element.
        $mform->addElement('date_time_selector', 'end_date',
                get_string('smartmenusbydateuntil', 'theme_learnr'), ['optional' => true]);
        $mform->addHelpButton('end_date', 'smartmenusbydateuntil', 'theme_learnr');

        // Add the action buttons.
        $this->add_action_buttons();
    }

    /**
     * Validates form data.
     *
     * @param array $data Array containing form data.
     * @param array $files Array containing uploaded files.
     * @return array Array of errors, if any.
     */
    public function validation($data, $files) {
        // Call parent form validation first.
        $errors = parent::validation($data, $files);

        // If the menu item type is static.
        if ($data['type'] == smartmenu_item::TYPESTATIC) {
            // Verify that the URL field is not empty.
            if (empty($data['url'])) {
                $errors['url'] = get_string('required');
            }
        }

        // Return errors.
        return $errors;
    }
}
