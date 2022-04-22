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
 * A drawer based layout for the boost theme.
 *
 * @package   theme_boost
 * @copyright 2021 Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

$blockscolumna = $OUTPUT->blocks('columna');
$blockscolumnb = $OUTPUT->blocks('columnb');
$blockscolumnc = $OUTPUT->blocks('columnc');

$columnabtn = $OUTPUT->addblockbutton('columna');
$columnaregion = $OUTPUT->custom_block_region('columna');

$columnbbtn = $OUTPUT->addblockbutton('columnb');
$columnbregion = $OUTPUT->custom_block_region('columnb');

$columncbtn = $OUTPUT->addblockbutton('columnc');
$columncregion = $OUTPUT->custom_block_region('columnc');

$checkblocka = (strpos($blockscolumna, 'data-block=') !== false || !empty($addblockbutton));
$checkblockb = (strpos($blockscolumnb, 'data-block=') !== false || !empty($addblockbutton));
$checkblockc = (strpos($blockscolumnc, 'data-block=') !== false || !empty($addblockbutton));

$displaycoursedashboard = $this->page->pagelayout == 'course' && isset($COURSE->id) && $COURSE->id > 1;

$hascoursedashblocks = false ;
if ($checkblocka || $checkblockb || $checkblockc) {
    $hascoursedashblocks = true;
}

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);
user_preference_allow_ajax_update('drawer-open-block', PARAM_BOOL);

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING')) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');

$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));

if ($this->page->theme->settings->showblockdrawer == 0) {
$hasblocks = (strpos($blockshtml, 'data-block=') !== false);
}

if (!$hasblocks) {
    $blockdraweropen = false;
}

$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$alertbox = '';
if ($this->page->pagelayout == 'mydashboard' || $this->page->pagelayout == 'frontpage' || $this->page->pagelayout == 'mycourses' ) {
    $alertbox = (empty($this->page->theme->settings->alertbox)) ? false : format_text($this->page->theme->settings->alertbox);
}

$fptextbox =false;
if ($this->page->pagelayout == 'mydashboard' || $this->page->pagelayout == 'frontpage') {
$fptextbox = (empty($this->page->theme->settings->fptextbox)) ? false : format_text($this->page->theme->settings->fptextbox);

}
$hasmarketingtiles = false;
if ($this->page->pagelayout == 'mydashboard' || $this->page->pagelayout == 'frontpage') {
    $hasmarketingtiles = true;
}

$showcourseindexnav = (empty($this->page->theme->settings->showcourseindexnav)) ? false : $this->page->theme->settings->showcourseindexnav;
$showblockdrawer = (empty($this->page->theme->settings->showblockdrawer)) ? false : $this->page->theme->settings->showblockdrawer;
$showcoursedashboard = (empty($this->page->theme->settings->showcoursedashboard)) ? false : $this->page->theme->settings->showcoursedashboard;
$showpageimage = (empty($this->page->theme->settings->showpageimage)) ? false : $this->page->theme->settings->showpageimage;

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($this->page->has_secondary_navigation()) {
    $tablistnav = $this->page->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($this->page->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $this->page->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $this->page->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$this->page->include_region_main_settings_in_header_actions() && !$this->page->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $this->page->activityheader;
$headercontent = $header->export_for_template($renderer);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'showcourseindexnav' => $showcourseindexnav,
    'showpageimage' => $showpageimage,
    'alertbox' => $alertbox,
    'fptextbox' => $fptextbox,
    'hasmarketingtiles' => $hasmarketingtiles,
    'addblockbutton' => $addblockbutton,
    'columnabtn' => $columnabtn,
    'columnaregion' => $columnaregion,
    'columnbbtn' => $columnbbtn,
    'columnbregion' => $columnbregion,
    'columncbtn' => $columncbtn,
    'columncregion' => $columncregion,
    'displaycoursedashboard' => $displaycoursedashboard,
    'hascoursedashblocks' => $hascoursedashblocks,
    'showblockdrawer' => $showblockdrawer,
    'showcoursedashboard' => $showcoursedashboard,
];

$this->page->requires->jquery();
$this->page->requires->js('/theme/learnr/javascript/blockslider.js');

echo $OUTPUT->render_from_template('theme_learnr/drawers', $templatecontext);
