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
 * Theme Boost Union - Smart menu edit form
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

use theme_learnr\smartmenu;

/**
 * Form for editing or adding a smart menu item.
 */
class smartmenu_edit_form extends \moodleform {

    /**
     * Define form elements.
     *
     * @throws \coding_exception
     */
    public function definition() {
        // Get an easier handler for the form.
        $mform = $this->_form;

        // Add the smart menu ID as hidden element.
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Add general settings as header element.
        $mform->addElement('header', 'generalsettingsheader',
                get_string('smartmenusgeneralsectionheader', 'theme_learnr'));
        $mform->setExpanded('generalsettingsheader');

        // Add the title as input element.
        $mform->addElement('text', 'title', get_string('smartmenusmenutitle', 'theme_learnr'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('required'), 'required');
        $mform->addHelpButton('title', 'smartmenusmenutitle', 'theme_learnr');

        // Add the description title as editor element.
        $mform->addElement('editor', 'description', get_string('smartmenusmenudescription', 'theme_learnr'));
        $mform->setType('description', PARAM_CLEANHTML);
        $mform->addHelpButton('description', 'smartmenusmenudescription', 'theme_learnr');

        // Add structure as header element.
        $mform->addElement('header', 'structureheader',
                get_string('smartmenusmenustructureheader', 'theme_learnr'));
        $mform->setExpanded('structureheader');

        // Add locations as autocompletefield.
        $locationtypes = smartmenu::get_locations();
        $location = $mform->addElement('autocomplete', 'location', get_string('smartmenusmenulocation', 'theme_learnr'),
                $locationtypes);
        $mform->addHelpButton('location', 'smartmenusmenulocation', 'theme_learnr');
        $location->setMultiple(true);

        // Add mode as select element.
        $modeoptions = [
                smartmenu::MODE_SUBMENU => get_string('smartmenusmodesubmenu', 'theme_learnr'),
                smartmenu::MODE_INLINE => get_string('smartmenusmodeinline', 'theme_learnr'),
        ];
        $mform->addElement('select', 'mode', get_string('smartmenusmenumode', 'theme_learnr'), $modeoptions);
        $mform->setDefault('mode', smartmenu::MODE_SUBMENU);
        $mform->setType('mode', PARAM_INT);
        $mform->addHelpButton('mode', 'smartmenusmenumode', 'theme_learnr');

        // Add presentation as header element.
        $mform->addElement('header', 'presentationheader',
                get_string('smartmenusmenupresentationheader', 'theme_learnr'));
        $mform->setExpanded('presentationheader');

        // Add type as select element.
        $types = smartmenu::get_types();
        $mform->addElement('select', 'type', get_string('smartmenusmenutype', 'theme_learnr'), $types);
        $mform->setDefault('type', smartmenu::TYPE_LIST);
        $mform->setType('type', PARAM_INT);
        $mform->addHelpButton('type', 'smartmenusmenutype', 'theme_learnr');

        // Add show description as select element.
        $showdescriptionoptions = [
                smartmenu::DESC_NEVER => get_string('smartmenusmenushowdescriptionnever', 'theme_learnr'),
                smartmenu::DESC_ABOVE => get_string('smartmenusmenushowdescriptionabove', 'theme_learnr'),
                smartmenu::DESC_BELOW => get_string('smartmenusmenushowdescriptionbelow', 'theme_learnr'),
                smartmenu::DESC_HELP => get_string('smartmenusmenushowdescriptionhelp', 'theme_learnr'),
        ];
        $mform->addElement('select', 'showdesc', get_string('smartmenusmenushowdescription', 'theme_learnr'),
                $showdescriptionoptions);
        $mform->setDefault('showdesc', smartmenu::DESC_NEVER);
        $mform->setType('showdesc', PARAM_INT);
        $mform->addHelpButton('showdesc', 'smartmenusmenushowdescription', 'theme_learnr');

        // Add more menu behavior as select element.
        $moremenuoptions = [
                smartmenu::MOREMENU_DONOTCHANGE => get_string('dontchange', 'theme_learnr'),
                smartmenu::MOREMENU_INTO => get_string('smartmenusmenumoremenubehaviorforceinto', 'theme_learnr'),
                smartmenu::MOREMENU_OUTSIDE => get_string('smartmenusmenumoremenubehaviorkeepoutside', 'theme_learnr'),
        ];
        $mform->addElement('select', 'moremenubehavior', get_string('smartmenusmenumoremenubehavior', 'theme_learnr'),
                $moremenuoptions);
        $mform->setDefault('moremenubehavior', smartmenu::MOREMENU_DONOTCHANGE);
        $mform->setType('moremenubehavior', PARAM_INT);
        $mform->addHelpButton('moremenubehavior', 'smartmenusmenumoremenubehavior', 'theme_learnr');

        // Add CSS class as input element.
        $mform->addElement('text', 'cssclass', get_string('smartmenusmenucssclass', 'theme_learnr'));
        $mform->addHelpButton('cssclass', 'smartmenusmenucssclass', 'theme_learnr');
        $mform->setType('cssclass', PARAM_TEXT);

        // Add card size as select element.
        $cardsizeoptions = [
                smartmenu::CARDSIZE_TINY => get_string('smartmenusmenucardsizetiny', 'theme_learnr').' (50px)',
                smartmenu::CARDSIZE_SMALL => get_string('smartmenusmenucardsizesmall', 'theme_learnr').' (100px)',
                smartmenu::CARDSIZE_MEDIUM => get_string('smartmenusmenucardsizemedium', 'theme_learnr').' (150px)',
                smartmenu::CARDSIZE_LARGE => get_string('smartmenusmenucardsizelarge', 'theme_learnr').' (200px)',
        ];
        $mform->addElement('select', 'cardsize', get_string('smartmenusmenucardsize', 'theme_learnr'), $cardsizeoptions);
        $mform->setDefault('cardsize', smartmenu::CARDSIZE_TINY);
        $mform->setType('cardsize', PARAM_INT);
        $mform->hideIf('cardsize', 'type', 'neq', smartmenu::TYPE_CARD);
        $mform->addHelpButton('cardsize', 'smartmenusmenucardsize', 'theme_learnr');

        // Add card form as select element.
        $cardformoptions = [
                smartmenu::CARDFORM_SQUARE =>
                        get_string('smartmenusmenucardformsquare', 'theme_learnr').' (1/1)',
                smartmenu::CARDFORM_PORTRAIT =>
                        get_string('smartmenusmenucardformportrait', 'theme_learnr').' (2/3)',
                smartmenu::CARDFORM_LANDSCAPE =>
                        get_string('smartmenusmenucardformlandscape', 'theme_learnr').' (3/2)',
                smartmenu::CARDFORM_FULLWIDTH =>
                        get_string('smartmenusmenucardformfullwidth', 'theme_learnr'),
        ];
        $mform->addElement('select', 'cardform',
                get_string('smartmenusmenucardform', 'theme_learnr'), $cardformoptions);
        $mform->setDefault('cardform', smartmenu::CARDFORM_SQUARE);
        $mform->setType('cardform', PARAM_INT);
        $mform->hideIf('cardform', 'type', 'neq', smartmenu::TYPE_CARD);
        $mform->addHelpButton('cardform', 'smartmenusmenucardform', 'theme_learnr');

        // Add card overflow behaviour as select element.
        $cardoverflowoptions = [
                smartmenu::CARDOVERFLOWBEHAVIOUR_NOWRAP =>
                        get_string('smartmenusmenucardoverflowbehaviornowrap', 'theme_learnr'),
                smartmenu::CARDOVERFLOWBEHAVIOUR_WRAP =>
                        get_string('smartmenusmenucardoverflowbehaviorwrap', 'theme_learnr'),
        ];
        $mform->addElement('select', 'cardoverflowbehavior',
                get_string('smartmenusmenucardoverflowbehavior', 'theme_learnr'), $cardoverflowoptions);
        $mform->setDefault('cardoverflowbehaviour', smartmenu::CARDOVERFLOWBEHAVIOUR_NOWRAP);
        $mform->setType('cardoverflowbehaviour', PARAM_INT);
        $mform->hideIf('cardoverflowbehavior', 'type', 'neq', smartmenu::TYPE_CARD);
        $mform->addHelpButton('cardoverflowbehavior', 'smartmenusmenucardoverflowbehavior', 'theme_learnr');

        // Add restrict visibility by roles as header element.
        $mform->addElement('header', 'restrictbyrolesheader',
                get_string('smartmenusrestrictbyrolesheader', 'theme_learnr'));
        // Set the header to expanded if the restriction is already set.
        if (isset($this->_customdata['menu']) &&
                count(json_decode($this->_customdata['menu']->roles)) > 0) {
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
        if (isset($this->_customdata['menu']) &&
                count(json_decode($this->_customdata['menu']->cohorts)) > 0) {
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
        if (isset($this->_customdata['menu']) &&
                count(json_decode($this->_customdata['menu']->languages)) > 0) {
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
        if (isset($this->_customdata['menu']) &&
                ($this->_customdata['menu']->start_date > 0 || $this->_customdata['menu']->end_date > 0)) {
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

        // Add the action buttons (as we have two buttons, we need a group).
        $actionbuttons = [];
        $actionclasses = ['class' => 'form-submit'];
        $actionbuttons[] = &$mform->createElement('submit', 'saveandreturn',
                get_string('savechangesandreturn'), $actionclasses);
        $actionbuttons[] = &$mform->createElement('submit', 'saveanddisplay',
                get_string('smartmenussavechangesandconfigure', 'theme_learnr'), $actionclasses);
        $actionbuttons[] = &$mform->createElement('cancel');
        $mform->addGroup($actionbuttons, 'actionbuttons', '', [' '], false);
        $mform->closeHeaderBefore('actionbuttons');
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

        // If the menu type is card.
        if ($data['type'] == smartmenu::TYPE_CARD) {
            // Verify that the card size is not empty.
            // (This should be already the case as this wiget is just a select element without neutral option).
            if (empty($data['cardsize'])) {
                $errors['cardsize'] = get_string('required');
            }

            // Verify that the card form is not empty.
            // (This should be already the case as this wiget is just a select element without neutral option).
            if (empty($data['cardform'])) {
                $errors['cardform'] = get_string('required');
            }

            // Verify that the overflow behaviour is selected.
            // (This should be already the case as this wiget is just a select element without neutral option).
            if (empty($data['cardoverflowbehavior'])) {
                $errors['cardoverflowbehavior'] = get_string('required');
            }
        }

        // Return errors.
        return $errors;
    }
}
