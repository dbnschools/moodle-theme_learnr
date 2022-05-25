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
 * Theme LearnR - Core renderer
 *
 * @package    theme_learnr
 * @copyright  2022 Dearborn Public Schools, Chris Kenniburg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_learnr\output;

use html_writer;
use custom_menu;
use stdClass;
use moodle_url;
use context_course;


require_once ($CFG->dirroot . '/completion/classes/progress.php');


class core_renderer extends \theme_boost\output\core_renderer {

    public function courseprogressbar() {
        global $PAGE;
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $hasprogressbar = (empty($this->page->theme->settings->showprogressbar)) ? false : true;

        // Student Dash
        if (\core_completion\progress::get_course_progress_percentage($PAGE->course)) {
            $comppc = \core_completion\progress::get_course_progress_percentage($PAGE->course);
            $comppercent = number_format($comppc, 0);
        }
        else {
            $comppercent = 0;
        }

        $progresschartcontext = ['progress' => $comppercent, 'hasprogressbar' => $hasprogressbar];
        $progress = $this->render_from_template('theme_learnr/progress-bar', $progresschartcontext);

        return $progress;
    }

    public function headerimage() {
        global $CFG;
        // Get course overview files.
        if (empty($CFG->courseoverviewfileslimit)) {
            return '';
        }
        $fs = get_file_storage();
        $context = context_course::instance($this->page->course->id);
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', false, 'filename', false);
        if (count($files)) {
            $overviewfilesoptions = course_overviewfiles_options($this->page->course->id);
            $acceptedtypes = $overviewfilesoptions['accepted_types'];
            if ($acceptedtypes !== '*') {
                foreach ($files as $key => $file) {
                    if (!file_extension_in_typegroup($file->get_filename() , $acceptedtypes)) {
                        unset($files[$key]);
                    }
                }
            }
            if (count($files) > $CFG->courseoverviewfileslimit) {
                // Return no more than $CFG->courseoverviewfileslimit files.
                $files = array_slice($files, 0, $CFG->courseoverviewfileslimit, true);
            }
        }
        // Get course overview files as images - set $courseimage.
        // The loop means that the LAST stored image will be the one displayed if >1 image file.
        $courseimage = '';
        foreach ($files as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $courseimage = file_encode_url("$CFG->wwwroot/pluginfile.php", '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename() , !$isimage);
            }
        }
        $html = '';
        $headerbg = $this->page->theme->setting_file_url('pagebackgroundimage', 'pagebackgroundimage');
        $defaultimgurl = $this->image_url('headerbg', 'theme');
        $headerbgimgurl = $this->page->theme->setting_file_url('pagebackgroundimage', 'pagebackgroundimage', true);

        $showpageimage = (empty($this->page->theme->settings->showpageimage)) ? false : ($this->page->theme->settings->showpageimage && $this->page->course->id > 1) && $this->page->pagetype == 'course-edit' || $this->page->pagetype == 'enrol-instances'|| $this->page->pagetype == 'enrol-editinstance' || $this->page->pagelayout != 'mydashboard' && $this->page->pagelayout != 'frontpage' && $this->page->pagelayout != 'mycourses';

        // Create html for header.
        if ($showpageimage){
            $html = html_writer::start_div('headerbkg');
            // If course image display it in separate div to allow css styling of inline style.
            if ($courseimage && !$this->page->theme->settings->sitewideimage == 1 && $showpageimage) {
                $html .= html_writer::start_div('courseimage', array(
                    'style' => 'background-image: url("' . $courseimage . '"); background-size: cover; background-position:center;
                    width: 100%; height: 100%;'
                ));
                $html .= html_writer::end_div(); // End withimage inline style div.
            }
            else if (isset($headerbg) && $showpageimage) {
                $html .= html_writer::start_div('customimage', array(
                    'style' => 'background-image: url("' . $headerbgimgurl . '"); background-size: cover; background-position:center;
                    width: 100%; height: 100%;'
                ));
                $html .= html_writer::end_div(); // End withoutimage inline style div.
                
            }
            else {
                $html .= html_writer::start_div('defaultheaderimage', array(
                    'style' => 'background-image: url("' . $defaultimgurl . '"); background-size: cover; background-position:center;
                    width: 100%; height: 100%;'
                ));
                $html .= html_writer::end_div(); // End default inline style div.
            }
            $html .= html_writer::end_div();
        }
        return $html;
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        global $DB, $OUTPUT, $COURSE;

        $pagetype = $this->page->pagetype;
        $homepage = get_home_page();
        $homepagetype = null;
        $mycourses = get_string('latestcourses', 'theme_learnr');
        $mycoursesurl = new moodle_url('/my/');
        $mycoursesmenu = $this->learnr_mycourses();
        $hasmycourses = $this->page->pagelayout == 'course' && (isset($this->page->theme->settings->showlatestcourses) && $this->page->theme->settings->showlatestcourses == 1);
        
        //$plugin = enrol_get_plugin('easy');
        $globalhaseasyenrollment = enrol_get_plugin('easy');
        $coursehaseasyenrollment = '';
        if ($globalhaseasyenrollment) {
            $coursehaseasyenrollment = $DB->record_exists('enrol', array(
                'courseid' => $this->page->course->id,
                'enrol' => 'easy'
            ));
            $easyenrollinstance = $DB->get_record('enrol', array(
                'courseid' => $this->page->course->id,
                'enrol' => 'easy'
            ));
        }
        $easycodetitle = '';
        $easycodelink = '';
        if ($globalhaseasyenrollment && $this->page->pagelayout == 'course' && $coursehaseasyenrollment){
        $easycodetitle = get_string('header_coursecodes', 'enrol_easy');
        $easycodelink = new moodle_url('/enrol/editinstance.php', array(
                'courseid' => $this->page->course->id,
                'id' => $easyenrollinstance->id,
                'type' => 'easy'
            ));
        }
        $easyenrolbtntext = get_string('easyenrollbtn', 'theme_learnr');
        
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $showenrollinktoteacher = has_capability('moodle/course:viewhiddenactivities', $context) && $globalhaseasyenrollment && $coursehaseasyenrollment && $this->page->pagelayout == 'course';
        $showblockdrawer = (empty($this->page->theme->settings->showblockdrawer)) ? false : $this->page->theme->settings->showblockdrawer;

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

        $displayheaderblocks = ($this->page->pagelayout == 'course' && isset($COURSE->id) && $COURSE->id > 1) &&  $this->page->theme->settings->showheaderblockpanel;
        $showheaderblockpanel = (empty($this->page->theme->settings->showheaderblockpanel)) ? false : $this->page->theme->settings->showheaderblockpanel;

        $hasheaderblocks = false;
        if (($checkblocka || $checkblockb || $checkblockc) && $this->page->theme->settings->showheaderblockpanel == 1 && $this->page->pagelayout == 'course') {
            $hasheaderblocks = true;
        }

        // Add a special case since /my/courses is a part of the /my subsystem.
        if ($homepage == HOMEPAGE_MY || $homepage == HOMEPAGE_MYCOURSES) {
            $homepagetype = 'my-index';
        } else if ($homepage == HOMEPAGE_SITE) {
            $homepagetype = 'site-index';
        }
        if ($this->page->include_region_main_settings_in_header_actions() &&
                !$this->page->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->hasmycourses = $hasmycourses;
        $header->mycourses = $mycourses;
        $header->showenrollinktoteacher = $showenrollinktoteacher;
        $header->showblockdrawer = $showblockdrawer;
        $header->hasheaderblocks = $hasheaderblocks;
        $header->displayheaderblocks = $displayheaderblocks;
        $header->showheaderblockpanel = $showheaderblockpanel;
        $header->columnabtn = $columnabtn;
        $header->columnbbtn = $columnbbtn;
        $header->columncbtn = $columncbtn;
        $header->columnaregion = $columnaregion;
        $header->columnbregion = $columnbregion;
        $header->columncregion = $columncregion;
        $header->mycoursesmenu = $mycoursesmenu;
        $header->easycodetitle = $easycodetitle;
        $header->easycodelink = $easycodelink;
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();
        if (!empty($pagetype) && !empty($homepagetype) && $pagetype == $homepagetype) {
            $header->welcomemessage = \core_user::welcome_message();
        }
        return $this->render_from_template('theme_learnr/core/full_header', $header);
    }

    /**
     * Returns standard navigation between activities in a course.
     *
     * @return string the navigation HTML.
     */
    public function top_activity_navigation() {
        // First we should check if we want to add navigation.
        $context = $this->page->context;
        if (($this->page->pagelayout !== 'incourse' && $this->page->pagelayout !== 'frametop')
            || $context->contextlevel != CONTEXT_MODULE) {
            return '';
        }
        // If the activity is in stealth mode, show no links.
        if ($this->page->cm->is_stealth()) {
            return '';
        }
        $course = $this->page->cm->get_course();
        $courseformat = course_get_format($course);
        // If the theme implements course index and the current course format uses course index and the current
        // page layout is not 'frametop' (this layout does not support course index), show no links.
        if ($this->page->theme->settings->activitynavdisplay ==2 || $this->page->theme->settings->activitynavdisplay ==4 ) {
                return '';
        }
        // Get a list of all the activities in the course.
        $modules = get_fast_modinfo($course->id)->get_cms();
        // Put the modules into an array in order by the position they are shown in the course.
        $mods = [];
        $activitylist = [];
        foreach ($modules as $module) {
            // Only add activities the user can access, aren't in stealth mode and have a url (eg. mod_label does not).
            if (!$module->uservisible || $module->is_stealth() || empty($module->url)) {
                continue;
            }
            $mods[$module->id] = $module;

            // No need to add the current module to the list for the activity dropdown menu.
            if ($module->id == $this->page->cm->id) {
                continue;
            }
            // Module name.
            $modname = $module->get_formatted_name();
            // Display the hidden text if necessary.
            if (!$module->visible) {
                $modname .= ' ' . get_string('hiddenwithbrackets');
            }
            // Module URL.
            $linkurl = new moodle_url($module->url, array('forceview' => 1));
            // Add module URL (as key) and name (as value) to the activity list array.
            $activitylist[$linkurl->out(false)] = $modname;
        }
        $nummods = count($mods);
        // If there is only one mod then do nothing.
        if ($nummods == 1) {
            return '';
        }
        // Get an array of just the course module ids used to get the cmid value based on their position in the course.
        $modids = array_keys($mods);
        // Get the position in the array of the course module we are viewing.
        $position = array_search($this->page->cm->id, $modids);
        $prevmod = null;
        $nextmod = null;
        // Check if we have a previous mod to show.
        if ($position > 0) {
            $prevmod = $mods[$modids[$position - 1]];
        }
        // Check if we have a next mod to show.
        if ($position < ($nummods - 1)) {
            $nextmod = $mods[$modids[$position + 1]];
        }
        $activitynav = new \core_course\output\activity_navigation($prevmod, $nextmod);
        $renderer = $this->page->get_renderer('core', 'course');
        return $renderer->render_from_template('theme_learnr/core_course/top_activity_navigation', $activitynav);
    }

    /**
     * Returns standard navigation between activities in a course.
     *
     * @return string the navigation HTML.
     */
    public function activity_navigation() {
        // First we should check if we want to add navigation.
        $context = $this->page->context;
        if (($this->page->pagelayout !== 'incourse' && $this->page->pagelayout !== 'frametop')
            || $context->contextlevel != CONTEXT_MODULE) {
            return '';
        }
        // If the activity is in stealth mode, show no links.
        if ($this->page->cm->is_stealth()) {
            return '';
        }
        $course = $this->page->cm->get_course();
        $courseformat = course_get_format($course);
        // If the theme implements course index and the current course format uses course index and the current
        // page layout is not 'frametop' (this layout does not support course index), show no links.
        if ($this->page->theme->settings->activitynavdisplay ==1 || $this->page->theme->settings->activitynavdisplay ==4 ) {
                return '';
        }
        // Get a list of all the activities in the course.
        $modules = get_fast_modinfo($course->id)->get_cms();
        // Put the modules into an array in order by the position they are shown in the course.
        $mods = [];
        $activitylist = [];
        foreach ($modules as $module) {
            // Only add activities the user can access, aren't in stealth mode and have a url (eg. mod_label does not).
            if (!$module->uservisible || $module->is_stealth() || empty($module->url)) {
                continue;
            }
            $mods[$module->id] = $module;
            // No need to add the current module to the list for the activity dropdown menu.
            if ($module->id == $this->page->cm->id) {
                continue;
            }
            // Module name.
            $modname = $module->get_formatted_name();
            // Display the hidden text if necessary.
            if (!$module->visible) {
                $modname .= ' ' . get_string('hiddenwithbrackets');
            }
            // Module URL.
            $linkurl = new moodle_url($module->url, array('forceview' => 1));
            // Add module URL (as key) and name (as value) to the activity list array.
            $activitylist[$linkurl->out(false)] = $modname;
        }
        $nummods = count($mods);
        // If there is only one mod then do nothing.
        if ($nummods == 1) {
            return '';
        }
        // Get an array of just the course module ids used to get the cmid value based on their position in the course.
        $modids = array_keys($mods);
        // Get the position in the array of the course module we are viewing.
        $position = array_search($this->page->cm->id, $modids);
        $prevmod = null;
        $nextmod = null;
        // Check if we have a previous mod to show.
        if ($position > 0) {
            $prevmod = $mods[$modids[$position - 1]];
        }
        // Check if we have a next mod to show.
        if ($position < ($nummods - 1)) {
            $nextmod = $mods[$modids[$position + 1]];
        }
        $activitynav = new \core_course\output\activity_navigation($prevmod, $nextmod, $activitylist);
        $renderer = $this->page->get_renderer('core', 'course');
        return $renderer->render($activitynav);
    }


    public function fp_marketingtiles() {

        $hasmarketing1 = (empty($this->page->theme->settings->marketing1)) ? false : format_string($this->page->theme->settings->marketing1);
        $marketing1content = (empty($this->page->theme->settings->marketing1content)) ? false : format_text($this->page->theme->settings->marketing1content);
        $marketing1buttontext = (empty($this->page->theme->settings->marketing1buttontext)) ? false : format_string($this->page->theme->settings->marketing1buttontext);
        $marketing1buttonurl = (empty($this->page->theme->settings->marketing1buttonurl)) ? false : $this->page->theme->settings->marketing1buttonurl;
        $marketing1target = (empty($this->page->theme->settings->marketing1target)) ? false : $this->page->theme->settings->marketing1target;
        $marketing1image = (empty($this->page->theme->settings->marketing1image)) ? false : $this->page->theme->setting_file_url('marketing1image', 'marketing1image', true);
        $marketing1icon = (empty($this->page->theme->settings->marketing1icon)) ? false : format_string($this->page->theme->settings->marketing1icon);
        
        $hasmarketing2 = (empty($this->page->theme->settings->marketing2)) ? false : format_string($this->page->theme->settings->marketing2);
        $marketing2content = (empty($this->page->theme->settings->marketing2content)) ? false : format_text($this->page->theme->settings->marketing2content);
        $marketing2buttontext = (empty($this->page->theme->settings->marketing2buttontext)) ? false : format_string($this->page->theme->settings->marketing2buttontext);
        $marketing2buttonurl = (empty($this->page->theme->settings->marketing2buttonurl)) ? false : $this->page->theme->settings->marketing2buttonurl;
        $marketing2target = (empty($this->page->theme->settings->marketing2target)) ? false : $this->page->theme->settings->marketing2target;
        $marketing2image = (empty($this->page->theme->settings->marketing2image)) ? false : $this->page->theme->setting_file_url('marketing2image', 'marketing2image', true);
        $marketing2icon = (empty($this->page->theme->settings->marketing2icon)) ? false : format_string($this->page->theme->settings->marketing2icon);
        
        $hasmarketing3 = (empty($this->page->theme->settings->marketing3)) ? false : format_string($this->page->theme->settings->marketing3);
        $marketing3content = (empty($this->page->theme->settings->marketing3content)) ? false : format_text($this->page->theme->settings->marketing3content);
        $marketing3buttontext = (empty($this->page->theme->settings->marketing3buttontext)) ? false : format_string($this->page->theme->settings->marketing3buttontext);
        $marketing3buttonurl = (empty($this->page->theme->settings->marketing3buttonurl)) ? false : $this->page->theme->settings->marketing3buttonurl;
        $marketing3target = (empty($this->page->theme->settings->marketing3target)) ? false : $this->page->theme->settings->marketing3target;
        $marketing3image = (empty($this->page->theme->settings->marketing3image)) ? false : $this->page->theme->setting_file_url('marketing3image', 'marketing3image', true);
        $marketing3icon = (empty($this->page->theme->settings->marketing3icon)) ? false : format_string($this->page->theme->settings->marketing3icon);
        
        $fp_marketingtiles = ['hasmarkettiles' => ($hasmarketing1 || $hasmarketing2 || $hasmarketing3) ? true : false, 'markettiles' => array(
            array(
                'hastile' => $hasmarketing1,
                'tileimage' => $marketing1image,
                'content' => $marketing1content,
                'title' => $hasmarketing1,
                'hasbutton' => $marketing1buttonurl,
                'button' => "<a href = '$marketing1buttonurl' title = '$marketing1buttontext' alt='$marketing1buttontext' class='btn btn-primary' target='$marketing1target'> $marketing1buttontext </a>",
                'marketingicon' => $marketing1icon,
            ) ,
            array(
                'hastile' => $hasmarketing2,
                'tileimage' => $marketing2image,
                'content' => $marketing2content,
                'title' => $hasmarketing2,
                'hasbutton' => $marketing2buttonurl,
                'button' => "<a href = '$marketing2buttonurl' title = '$marketing2buttontext' alt='$marketing2buttontext' class='btn btn-primary' target='$marketing2target'> $marketing2buttontext </a>",
                'marketingicon' => $marketing2icon,
            ) ,
            array(
                'hastile' => $hasmarketing3,
                'tileimage' => $marketing3image,
                'content' => $marketing3content,
                'title' => $hasmarketing3,
                'hasbutton' => $marketing3buttonurl,
                'button' => "<a href = '$marketing3buttonurl' title = '$marketing3buttontext' alt='$marketing3buttontext' class='btn btn-primary' target='$marketing3target'> $marketing3buttontext </a>",
                'marketingicon' => $marketing3icon,
            ) ,
        ) , 
    ];
        return $this->render_from_template('theme_learnr/fpmarkettiles', $fp_marketingtiles);
    }

    // The following code is a copied work of the code from theme Essential https://moodle.org/plugins/theme_essential, @copyright Gareth J Barnard
    protected static function timeaccesscompare($a, $b) {
        // Timeaccess is lastaccess entry and timestart an enrol entry.
        if ((!empty($a->timeaccess)) && (!empty($b->timeaccess))) {
            // Both last access.
            if ($a->timeaccess == $b->timeaccess) {
                return 0;
            }
            return ($a->timeaccess > $b->timeaccess) ? -1 : 1;
        }
        else if ((!empty($a->timestart)) && (!empty($b->timestart))) {
            // Both enrol.
            if ($a->timestart == $b->timestart) {
                return 0;
            }
            return ($a->timestart > $b->timestart) ? -1 : 1;
        }
        // Must be comparing an enrol with a last access.
        // -1 is to say that 'a' comes before 'b'.
        if (!empty($a->timestart)) {
            // 'a' is the enrol entry.
            return -1;
        }
        // 'b' must be the enrol entry.
        return 1;
    }
    // End copied code

    // The following code is a derivative work of the code from theme Essential https://moodle.org/plugins/theme_essential, by Gareth J Barnard
    public function learnr_mycourses() {
        $context = $this->page->context;
        $menu = new custom_menu();
        
            $branchtitle = get_string('latestcourses', 'theme_learnr');
            $branchlabel = $branchtitle;
            $branchurl = new moodle_url('/my/courses.php');
            $branchsort = 10000;
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            $dashlabel = get_string('viewallcourses', 'theme_learnr');
            $dashurl = new moodle_url("/my/courses.php");
            $dashtitle = $dashlabel;
            $nomycourses = get_string('nomycourses', 'theme_learnr');
            $courses = enrol_get_my_courses(null, 'sortorder ASC');
             
                if ($courses) {
                    // We have something to work with.  Get the last accessed information for the user and populate.
                    global $DB, $USER;
                    $lastaccess = $DB->get_records('user_lastaccess', array('userid' => $USER->id) , '', 'courseid, timeaccess');
                    if ($lastaccess) {
                        foreach ($courses as $course) {
                            if (!empty($lastaccess[$course->id])) {
                                $course->timeaccess = $lastaccess[$course->id]->timeaccess;
                            }
                        }
                    }
                    // Determine if we need to query the enrolment and user enrolment tables.
                    $enrolquery = false;
                    foreach ($courses as $course) {
                        if (empty($course->timeaccess)) {
                            $enrolquery = true;
                            break;
                        }
                    }
                    if ($enrolquery) {
                        // We do.
                        $params = array(
                            'userid' => $USER->id
                        );
                        $sql = "SELECT ue.id, e.courseid, ue.timestart
                            FROM {enrol} e
                            JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)";
                        $enrolments = $DB->get_records_sql($sql, $params, 0, 0);
                        if ($enrolments) {
                            // Sort out any multiple enrolments on the same course.
                            $userenrolments = array();
                            foreach ($enrolments as $enrolment) {
                                if (!empty($userenrolments[$enrolment->courseid])) {
                                    if ($userenrolments[$enrolment->courseid] < $enrolment->timestart) {
                                        // Replace.
                                        $userenrolments[$enrolment->courseid] = $enrolment->timestart;
                                    }
                                }
                                else {
                                    $userenrolments[$enrolment->courseid] = $enrolment->timestart;
                                }
                            }
                            // We don't need to worry about timeend etc. as our course list will be valid for the user from above.
                            foreach ($courses as $course) {
                                if (empty($course->timeaccess)) {
                                    $course->timestart = $userenrolments[$course->id];
                                }
                            }
                        }
                    }
                    uasort($courses, array($this,'timeaccesscompare'));
                }
                else {
                    return $nomycourses;
                }
                $sortorder = $lastaccess;
                $i = 0;
                foreach ($courses as $course) {
                    if ($course->visible && $i < 7) {
                        $branch->add(format_string($course->fullname) , new moodle_url('/course/view.php?id=' . $course->id) , format_string($course->shortname));
                    }
                    $i += 1;
                }
                $branch->add($dashlabel, $dashurl, $dashtitle);
                $content = '';
                foreach ($menu->get_children() as $item) {
                    $context = $item->export_for_template($this);
                    $content .= $this->render_from_template('theme_learnr/mycourses', $context);
                }
        return $content;
    }
    // End derivative work

    public function fpicons() {
        $context = $this->page->context;
        $hasslideicon = (empty($this->page->theme->settings->slideicon && isloggedin() && !isguestuser())) ? false : $this->page->theme->settings->slideicon;
        $slideiconbuttonurl = 'data-toggle="collapse" data-target="#collapseExample';
        $slideiconbuttontext = (empty($this->page->theme->settings->slideiconbuttontext)) ? false : format_string($this->page->theme->settings->slideiconbuttontext);
        $hasnav1icon = (empty($this->page->theme->settings->nav1icon && isloggedin() && !isguestuser())) ? false : $this->page->theme->settings->nav1icon;
        $hasnav2icon = (empty($this->page->theme->settings->nav2icon && isloggedin() && !isguestuser())) ? false : $this->page->theme->settings->nav2icon;
        $hasnav3icon = (empty($this->page->theme->settings->nav3icon && isloggedin() && !isguestuser())) ? false : $this->page->theme->settings->nav3icon;
        $hasnav4icon = (empty($this->page->theme->settings->nav4icon && isloggedin() && !isguestuser())) ? false : $this->page->theme->settings->nav4icon;
        $hasnav5icon = (empty($this->page->theme->settings->nav5icon && isloggedin() && !isguestuser())) ? false : $this->page->theme->settings->nav5icon;
        $hasnav6icon = (empty($this->page->theme->settings->nav6icon && isloggedin() && !isguestuser())) ? false : $this->page->theme->settings->nav6icon;
        $hasnav7icon = (empty($this->page->theme->settings->nav7icon && isloggedin() && !isguestuser())) ? false : $this->page->theme->settings->nav7icon;
        $hasnav8icon = (empty($this->page->theme->settings->nav8icon && isloggedin() && !isguestuser())) ? false : $this->page->theme->settings->nav8icon;
        $nav1buttonurl = (empty($this->page->theme->settings->nav1buttonurl)) ? false : $this->page->theme->settings->nav1buttonurl;
        $nav2buttonurl = (empty($this->page->theme->settings->nav2buttonurl)) ? false : $this->page->theme->settings->nav2buttonurl;
        $nav3buttonurl = (empty($this->page->theme->settings->nav3buttonurl)) ? false : $this->page->theme->settings->nav3buttonurl;
        $nav4buttonurl = (empty($this->page->theme->settings->nav4buttonurl)) ? false : $this->page->theme->settings->nav4buttonurl;
        $nav5buttonurl = (empty($this->page->theme->settings->nav5buttonurl)) ? false : $this->page->theme->settings->nav5buttonurl;
        $nav6buttonurl = (empty($this->page->theme->settings->nav6buttonurl)) ? false : $this->page->theme->settings->nav6buttonurl;
        $nav7buttonurl = (empty($this->page->theme->settings->nav7buttonurl)) ? false : $this->page->theme->settings->nav7buttonurl;
        $nav8buttonurl = (empty($this->page->theme->settings->nav8buttonurl)) ? false : $this->page->theme->settings->nav8buttonurl;
        $nav1buttontext = (empty($this->page->theme->settings->nav1buttontext)) ? false : format_string($this->page->theme->settings->nav1buttontext);
        $nav2buttontext = (empty($this->page->theme->settings->nav2buttontext)) ? false : format_string($this->page->theme->settings->nav2buttontext);
        $nav3buttontext = (empty($this->page->theme->settings->nav3buttontext)) ? false : format_string($this->page->theme->settings->nav3buttontext);
        $nav4buttontext = (empty($this->page->theme->settings->nav4buttontext)) ? false : format_string($this->page->theme->settings->nav4buttontext);
        $nav5buttontext = (empty($this->page->theme->settings->nav5buttontext)) ? false : format_string($this->page->theme->settings->nav5buttontext);
        $nav6buttontext = (empty($this->page->theme->settings->nav6buttontext)) ? false : format_string($this->page->theme->settings->nav6buttontext);
        $nav7buttontext = (empty($this->page->theme->settings->nav7buttontext)) ? false : format_string($this->page->theme->settings->nav7buttontext);
        $nav8buttontext = (empty($this->page->theme->settings->nav8buttontext)) ? false : format_string($this->page->theme->settings->nav8buttontext);
        $nav1target = (empty($this->page->theme->settings->nav1target)) ? false : $this->page->theme->settings->nav1target;
        $nav2target = (empty($this->page->theme->settings->nav2target)) ? false : $this->page->theme->settings->nav2target;
        $nav3target = (empty($this->page->theme->settings->nav3target)) ? false : $this->page->theme->settings->nav3target;
        $nav4target = (empty($this->page->theme->settings->nav4target)) ? false : $this->page->theme->settings->nav4target;
        $nav5target = (empty($this->page->theme->settings->nav5target)) ? false : $this->page->theme->settings->nav5target;
        $nav6target = (empty($this->page->theme->settings->nav6target)) ? false : $this->page->theme->settings->nav6target;
        $nav7target = (empty($this->page->theme->settings->nav7target)) ? false : $this->page->theme->settings->nav7target;
        $nav8target = (empty($this->page->theme->settings->nav8target)) ? false : $this->page->theme->settings->nav8target;
        $slidetextbox = (empty($this->page->theme->settings->slidetextbox && isloggedin())) ? false : format_text($this->page->theme->settings->slidetextbox, FORMAT_HTML, array(
            'noclean' => true
        ));

        $fp_icons = [
            'hasslidetextbox' => (!empty($this->page->theme->settings->slidetextbox && isloggedin())) , 
            'slidetextbox' => $slidetextbox, 'hasfptextboxlogout' => !isloggedin() ,
            'hasfpiconnav' => ($hasnav1icon || $hasnav2icon || $hasnav3icon || $hasnav4icon || $hasnav5icon || $hasnav6icon || $hasnav7icon || $hasnav8icon || $hasslideicon ? true : false) && ($this->page->pagelayout == 'mydashboard' || $this->page->pagelayout == 'frontpage' || $this->page->pagelayout == 'mycourses'), 
            'fpiconnav' => array(
                array(
                    'hasicon' => $hasnav1icon,
                    'linkicon' => $hasnav1icon,
                    'link' => $nav1buttonurl,
                    'linktext' => $nav1buttontext,
                    'linktarget' => $nav1target
                ) ,
                array(
                    'hasicon' => $hasnav2icon,
                    'linkicon' => $hasnav2icon,
                    'link' => $nav2buttonurl,
                    'linktext' => $nav2buttontext,
                    'linktarget' => $nav2target
                ) ,
                array(
                    'hasicon' => $hasnav3icon,
                    'linkicon' => $hasnav3icon,
                    'link' => $nav3buttonurl,
                    'linktext' => $nav3buttontext,
                    'linktarget' => $nav3target
                ) ,
                array(
                    'hasicon' => $hasnav4icon,
                    'linkicon' => $hasnav4icon,
                    'link' => $nav4buttonurl,
                    'linktext' => $nav4buttontext,
                    'linktarget' => $nav4target
                ) ,
                array(
                    'hasicon' => $hasnav5icon,
                    'linkicon' => $hasnav5icon,
                    'link' => $nav5buttonurl,
                    'linktext' => $nav5buttontext,
                    'linktarget' => $nav5target
                ) ,
                array(
                    'hasicon' => $hasnav6icon,
                    'linkicon' => $hasnav6icon,
                    'link' => $nav6buttonurl,
                    'linktext' => $nav6buttontext,
                    'linktarget' => $nav6target
                ) ,
                array(
                    'hasicon' => $hasnav7icon,
                    'linkicon' => $hasnav7icon,
                    'link' => $nav7buttonurl,
                    'linktext' => $nav7buttontext,
                    'linktarget' => $nav7target
                ) ,
                array(
                    'hasicon' => $hasnav8icon,
                    'linkicon' => $hasnav8icon,
                    'link' => $nav8buttonurl,
                    'linktext' => $nav8buttontext,
                    'linktarget' => $nav8target
                ) ,
            ) ,
            'fpslideicon' => array(
                array(
                    'hasicon' => $hasslideicon,
                    'linkicon' => $hasslideicon,
                    'link' => $slideiconbuttonurl,
                    'linktext' => $slideiconbuttontext
                ) ,
            ) , 
        ];

        return $this->render_from_template('theme_learnr/fpicons', $fp_icons);

    }

    public function enrolform() {
        $enrolform = '';
        $plugin = enrol_get_plugin('easy');

        if ($plugin && !isguestuser() && ($this->page->pagelayout == 'mydashboard' || $this->page->pagelayout == 'frontpage' || $this->page->pagelayout == 'mycourses')) {

            $enrolform = '<div class="easyenrolform">';
            $enrolform .= $plugin->get_form();
            $enrolform .= '</div>';
        }
        return $enrolform;
    }



}
