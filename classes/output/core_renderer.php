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
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_learnr\output;
use context_system;
use moodle_url;
//Begin DBN Update
use html_writer;
use custom_menu;
use stdClass;
use context_course;
//End DBN Update

/**
 * Extending the core_renderer interface.
 *
 * @package    theme_learnr
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Returns the moodle_url for the favicon.
     *
     * This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * It checks if the favicon is overridden in a flavour and, if yes, it serves this favicon.
     * If there isn't a favicon in any flavour set, it serves the general favicon.
     *
     * @since Moodle 2.5.1 2.6
     * @return moodle_url The moodle_url for the favicon
     * @throws \moodle_exception
     */
    public function favicon() {
        global $CFG;

        // Initialize static variable for the flavour favicon as this function might be called (for whatever reason) multiple times
        // during a page output.
        static $hasflavourfavicon, $flavourfaviconurl;

        // If the flavour favicon has already been checked.
        if ($hasflavourfavicon != null) {
            // If there is a flavour favicon.
            if ($hasflavourfavicon == true) {
                // Directly return the flavour favicon.
                return $flavourfaviconurl;
            }
            // Otherwise, if there isn't a flavour favicon, this function will continue to run the logic from Moodle core later.

            // Otherwise.
        } else {
            // Require flavours library.
            require_once($CFG->dirroot . '/theme/learnr/flavours/flavourslib.php');

            // If any flavour applies to this page.
            $flavour = theme_learnr_get_flavour_which_applies();
            if ($flavour != null) {
                // If the flavour has a favicon set.
                if ($flavour->look_favicon != null) {
                    // Remember this fact for subsequent runs of this function.
                    $hasflavourfavicon = true;

                    // Compose the URL to the flavour's favicon.
                    $flavourfaviconurl = moodle_url::make_pluginfile_url(
                            context_system::instance()->id, 'theme_learnr', 'flavours_look_favicon', $flavour->id,
                            '/'.theme_get_revision(), '/'.$flavour->look_favicon);

                    // Return the URL.
                    return $flavourfaviconurl;

                    // Otherwise.
                } else {
                    // Remember this fact for subsequent runs of this function.
                    $hasflavourfavicon = false;
                }
            }
        }

        // Apparently, there isn't any flavour favicon set. Let's continue with the logic to serve the general favicon.
        $logo = null;
        if (!during_initial_install()) {
            $logo = get_config('theme_learnr', 'favicon');
        }
        if (empty($logo)) {
            return $this->image_url('favicon', 'theme');
        }

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return moodle_url::make_pluginfile_url(context_system::instance()->id, 'theme_learnr', 'favicon', '64x64/',
                theme_get_revision(), $logo);
    }

    /**
     * Return the site's logo URL, if any.
     *
     * This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * It checks if the logo is overridden in a flavour and, if yes, it serves this logo.
     * If there isn't a logo in any flavour set, it serves the general logo.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        global $CFG;

        // Initialize static variable for the flavour logo as this function might be called (for whatever reason) multiple times
        // during a page output.
        static $hasflavourlogo, $flavourlogourl;

        // If the flavour logo has already been checked.
        if ($hasflavourlogo != null) {
            // If there is a flavour logo.
            if ($hasflavourlogo == true) {
                // Directly return the flavour logo.
                return $flavourlogourl;
            }
            // Otherwise, if there isn't a flavour logo, this function will continue to run the logic from Moodle core later.

            // Otherwise.
        } else {
            // Require flavours library.
            require_once($CFG->dirroot . '/theme/learnr/flavours/flavourslib.php');

            // If any flavour applies to this page.
            $flavour = theme_learnr_get_flavour_which_applies();
            if ($flavour != null) {
                // If the flavour has a logo set.
                if ($flavour->look_logo != null) {
                    // Remember this fact for subsequent runs of this function.
                    $hasflavourlogo = true;

                    // Compose the URL to the flavour's logo.
                    $flavourlogourl = moodle_url::make_pluginfile_url(
                            context_system::instance()->id, 'theme_learnr', 'flavours_look_logo', $flavour->id,
                            '/'.theme_get_revision(), '/'.$flavour->look_logo);

                    // Return the URL.
                    return $flavourlogourl;

                    // Otherwise.
                } else {
                    // Remember this fact for subsequent runs of this function.
                    $hasflavourlogo = false;
                }
            }
        }

        // Apparently, there isn't any flavour logo set. Let's continue to serve the general logo.
        $logo = get_config('theme_learnr', 'logo');
        if (empty($logo)) {
            return false;
        }

        // If the logo is a SVG image, do not add a size to the path.
        $logoextension = pathinfo($logo, PATHINFO_EXTENSION);
        if (in_array($logoextension, ['svg', 'svgz'])) {
            // The theme_learnr_pluginfile() function will look for a filepath and will try to extract the size from that.
            // Thus, we cannot drop the filepath from the URL completely.
            // But we can add a path without an 'x' in it which will then be interpreted by theme_learnr_pluginfile()
            // as "no resize requested".
            $filepath = '1/';

            // Otherwise, add a size to the path.
        } else {
            // 200px high is the default image size which should be displayed at 100px in the page to account for retina displays.
            // It's not worth the overhead of detecting and serving 2 different images based on the device.

            // Hide the requested size in the file path.
            $filepath = ((int) $maxwidth . 'x' . (int) $maxheight) . '/';
        }

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return moodle_url::make_pluginfile_url(context_system::instance()->id, 'theme_learnr', 'logo', $filepath,
                theme_get_revision(), $logo);
    }

    /**
     * Return the site's compact logo URL, if any.
     *
     * This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * It checks if the logo is overridden in a flavour and, if yes, it serves this logo.
     * If there isn't a logo in any flavour set, it serves the general compact logo.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        global $CFG;

        // Initialize static variable for the flavour logo as this function is called (for whatever reason) multiple times
        // during a page output.
        static $hasflavourlogo, $flavourlogourl;

        // If the flavour logo has already been checked.
        if ($hasflavourlogo != null) {
            // If there is a flavour logo.
            if ($hasflavourlogo == true) {
                // Directly return the flavour logo.
                return $flavourlogourl;
            }
            // Otherwise, if there isn't a flavour logo, this function will continue to run the logic from Moodle core later.

            // Otherwise.
        } else {
            // Require flavours library.
            require_once($CFG->dirroot . '/theme/learnr/flavours/flavourslib.php');

            // If any flavour applies to this page.
            $flavour = theme_learnr_get_flavour_which_applies();
            if ($flavour != null) {
                // If the flavour has a compact logo set.
                if ($flavour->look_logocompact != null) {
                    // Remember this fact for subsequent runs of this function.
                    $hasflavourlogo = true;

                    // Compose the URL to the flavour's compact logo.
                    $flavourlogourl = moodle_url::make_pluginfile_url(
                            context_system::instance()->id, 'theme_learnr', 'flavours_look_logocompact', $flavour->id,
                            '/'.theme_get_revision(), '/'.$flavour->look_logocompact);

                    // Return the URL.
                    return $flavourlogourl;

                    // Otherwise.
                } else {
                    // Remember this fact for subsequent runs of this function.
                    $hasflavourlogo = false;
                }
            }
        }

        // Apparently, there isn't any flavour logo set. Let's continue to service the general compact logo.
        $logo = get_config('theme_learnr', 'logocompact');
        if (empty($logo)) {
            return false;
        }

        // If the logo is a SVG image, do not add a size to the path.
        $logoextension = pathinfo($logo, PATHINFO_EXTENSION);
        if (in_array($logoextension, ['svg', 'svgz'])) {
            // The theme_learnr_pluginfile() function will look for a filepath and will try to extract the size from that.
            // Thus, we cannot drop the filepath from the URL completely.
            // But we can add a path without an 'x' in it which will then be interpreted by theme_learnr_pluginfile()
            // as "no resize requested".
            $filepath = '1/';

            // Otherwise, add a size to the path.
        } else {
            // Hide the requested size in the file path.
            $filepath = ((int)$maxwidth . 'x' . (int)$maxheight) . '/';
        }

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return moodle_url::make_pluginfile_url(context_system::instance()->id, 'theme_learnr', 'logocompact', $filepath,
                theme_get_revision(), $logo);
    }

    /**
     * Returns HTML attributes to use within the body tag. This includes an ID and classes.
     *
     * This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * @since Moodle 2.5.1 2.6
     * @param string|array $additionalclasses Any additional classes to give the body tag,
     * @return string
     */
    public function body_attributes($additionalclasses = array()) {
        global $CFG;

        // Require local libraries.
        require_once($CFG->dirroot . '/theme/learnr/locallib.php');
        require_once($CFG->dirroot . '/theme/learnr/flavours/flavourslib.php');

        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }

        // If this isn't the login page and the page has a background image, add a class to the body attributes.
        if ($this->page->pagelayout != 'login') {
            if (!empty(get_config('theme_learnr', 'backgroundimage'))) {
                $additionalclasses[] = 'backgroundimage';
            }
        }

        // If this is the login page and the page has a login background image, add a class to the body attributes.
        if ($this->page->pagelayout == 'login') {
            // Generate the background image class for displaying a random image for the login page.
            $loginimageclass = theme_learnr_get_random_loginbackgroundimage_class();

            // If the background image class was returned, we can expect that a background image was set.
            // In this case, add both the general loginbackgroundimage class as well as the generated
            // class to the body tag.
            if ($loginimageclass != '') {
                $additionalclasses[] = 'loginbackgroundimage';
                $additionalclasses[] = $loginimageclass;
            }
        }

        // If there is a flavour applied to this page, add the flavour ID as additional body class.
        // LearnR itself does not need this class for applying the flavour to the page (yet).
        // However, theme designers might want to use it.
        $flavour = theme_learnr_get_flavour_which_applies();
        if ($flavour != null) {
            $additionalclasses[] = 'flavour'.'-'.$flavour->id;
        }

        return ' id="'. $this->body_id().'" class="'.$this->body_css_classes($additionalclasses).'"';
    }



    //Begin DBN Update
    public function headerbuttons() {
        global $DB, $OUTPUT, $COURSE;
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $mycourses = get_string('latestcourses', 'theme_learnr');
        $mycoursesurl = new moodle_url('/my/');
        $mycoursesmenu = $this->learnr_mycourses();
        $hasmycourses = isset($COURSE->id) && $COURSE->id > 1 && (isset($this->page->theme->settings->showlatestcourses) && $this->page->theme->settings->showlatestcourses == 1);
        $hascourseactivities = isset($COURSE->id) && $COURSE->id > 1 && (isset($this->page->theme->settings->showcourseactivities) && $this->page->theme->settings->showcourseactivities == 1);
        $courseactivitiesbtntext = get_string('courseactivitiesbtntext', 'theme_learnr');
        $courseenrollmentcode = get_string('courseenrollmentcode', 'theme_learnr');
        $coursemanagementdash = $this->coursemanagementdash();
        $showincourseonlymanagementbtn = isset($COURSE->id) && $COURSE->id > 1 && $this->page->theme->settings->showcoursemanagement == 1 && has_capability('moodle/course:viewhiddenactivities', $context) && isloggedin() && !isguestuser();
        
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
        //$easyenrolbtntext = get_string('easyenrollbtn', 'theme_learnr');
        
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $showenrollinktoteacher = has_capability('moodle/course:viewhiddenactivities', $context) && $this->page->theme->settings->showeasyenrolbtn == 1  && $globalhaseasyenrollment && $coursehaseasyenrollment && $this->page->pagelayout == 'course';

        // Build links.
        $headerlinks = [
            'manageuserstitle' => get_string('manageuserstitle', 'theme_learnr'),
            'hasmycourses' => $hasmycourses,
            'mycourses' => $mycourses,
            'mycoursesmenu' => $mycoursesmenu,
            'showenrollinktoteacher' => $showenrollinktoteacher,
            'easycodetitle' => $easycodetitle,
            'easycodelink' => $easycodelink,
            'courseactivitiesmenu' => $this->courseactivities_menu(),
            'courseactivitiesbtntext' => $courseactivitiesbtntext,
            'courseenrollmentcode' => $courseenrollmentcode,
            'hascourseactivities' => $hascourseactivities,
            'coursemanagementdash' => $coursemanagementdash,
            'showincourseonlymanagementbtn' => $showincourseonlymanagementbtn
        ];
        return $this->render_from_template('theme_learnr/headerbuttons', $headerlinks);
        

    }
    //End DBN Update


    /**
     * Wrapper for header elements.
     *
     * This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        //Begin DBN Update
        global $DB, $OUTPUT, $COURSE;
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $courseenrollmentcode = get_string('courseenrollmentcode', 'theme_learnr');
        $fpicons = $this->fpicons();
        $enrolform = $this->enrolform();
        $courseprogressbar = $this->courseprogressbar();
        $globalhaseasyenrollment = enrol_get_plugin('easy');
        $coursehaseasyenrollment = '';
        $easyenrollinstance = '';
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
        //End DBN Update

        $pagetype = $this->page->pagetype;
        $homepage = get_home_page();
        $homepagetype = null;
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

        $header = new \stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();

        //Begin DBN Update
        $header->fpicons = $fpicons;
        $header->enrolform = $enrolform;
        $header->courseprogressbar = $courseprogressbar;
        $header->courseenrollmentcode = $courseenrollmentcode;
        //End DBN Update

        // Add the course header image for rendering.
        if ($this->page->pagelayout == 'course' && (get_config('theme_learnr', 'courseheaderimageenabled')
                        == THEME_LEARNR_SETTING_SELECT_YES)) {
            // If course header images are activated, we get the course header image url
            // (which might be the fallback image depending on the course settings and theme settings).
            $header->courseheaderimageurl = theme_learnr_get_course_header_image_url();
            // Additionally, get the course header image height.
            $header->courseheaderimageheight = get_config('theme_learnr', 'courseheaderimageheight');
            // Additionally, get the course header image position.
            $header->courseheaderimageposition = get_config('theme_learnr', 'courseheaderimageposition');
            // Additionally, get the template context attributes for the course header image layout.
            $courseheaderimagelayout = get_config('theme_learnr', 'courseheaderimagelayout');
            switch($courseheaderimagelayout) {
                case THEME_LEARNR_SETTING_COURSEIMAGELAYOUT_HEADINGABOVE:
                    $header->courseheaderimagelayoutheadingabove = true;
                    $header->courseheaderimagelayoutstackedclass = '';
                    break;
                case THEME_LEARNR_SETTING_COURSEIMAGELAYOUT_STACKEDDARK:
                    $header->courseheaderimagelayoutheadingabove = false;
                    $header->courseheaderimagelayoutstackedclass = 'dark';
                    break;
                case THEME_LEARNR_SETTING_COURSEIMAGELAYOUT_STACKEDLIGHT:
                    $header->courseheaderimagelayoutheadingabove = false;
                    $header->courseheaderimagelayoutstackedclass = 'light';
                    break;
            }
        }

        if (!empty($pagetype) && !empty($homepagetype) && $pagetype == $homepagetype) {
            $header->welcomemessage = \core_user::welcome_message();
        }
        return $this->render_from_template('core/full_header', $header);
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    public function block(\block_contents $bc, $region) {
        global $CFG;

        // Require own locallib.php.
        require_once($CFG->dirroot . '/theme/learnr/locallib.php');

        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = \block_contents::NOT_HIDEABLE;
        }

        $id = !empty($bc->attributes['id']) ? $bc->attributes['id'] : uniqid('block-');
        $context = new \stdClass();
        $context->skipid = $bc->skipid;
        $context->blockinstanceid = $bc->blockinstanceid ?: uniqid('fakeid-');
        $context->dockable = $bc->dockable;
        $context->id = $id;
        $context->hidden = $bc->collapsible == \block_contents::HIDDEN;
        $context->skiptitle = strip_tags($bc->title);
        $context->showskiplink = !empty($context->skiptitle);
        $context->arialabel = $bc->arialabel;
        $context->ariarole = !empty($bc->attributes['role']) ? $bc->attributes['role'] : 'complementary';
        $context->class = $bc->attributes['class'];
        $context->type = $bc->attributes['data-block'];
        $context->title = $bc->title;
        $context->content = $bc->content;
        $context->annotation = $bc->annotation;
        $context->footer = $bc->footer;
        $context->hascontrols = !empty($bc->controls);

        // Hide edit control options for the regions based on the capabilities.
        $regions = theme_learnr_get_additional_regions();
        $regioncapname = array_search($region, $regions);
        if (!empty($regioncapname) && $context->hascontrols) {
            $context->hascontrols = has_capability('theme/learnr:editregion'.$regioncapname, $this->page->context);
        }

        if ($context->hascontrols) {
            $context->controls = $this->block_controls($bc->controls, $id);
        }

        return $this->render_from_template('core/block', $context);
    }
    //Begin DBN Update Functions
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

    public function courseprogressbar() {
        global $PAGE;
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $hasprogressbar = (empty($this->page->theme->settings->showprogressbar)) ? false : true;
        $iscoursepage = $this->page->pagelayout == 'course';

        // Student Dash
        if (\core_completion\progress::get_course_progress_percentage($PAGE->course)) {
            $comppc = \core_completion\progress::get_course_progress_percentage($PAGE->course);
            $comppercent = number_format($comppc, 0);
        }
        else {
            $comppercent = 0;
        }
        $progresschartcontext = ['progress' => $comppercent, 'hasprogressbar' => $hasprogressbar, 'iscoursepage' => $iscoursepage];
        $progress = $this->render_from_template('theme_learnr/progress-bar', $progresschartcontext);

        return $progress;
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
        $hascreateicon = (empty($this->page->theme->settings->createicon && isloggedin() && has_capability('moodle/course:create', $context))) ? false : $this->page->theme->settings->createicon;
        $createbuttonurl = (empty($this->page->theme->settings->createbuttonurl)) ? false : $this->page->theme->settings->createbuttonurl;
        $createbuttontext = (empty($this->page->theme->settings->createbuttontext)) ? false : format_string($this->page->theme->settings->createbuttontext);
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
            'fpcreateicon' => array(
                array(
                    'hasicon' => $hascreateicon,
                    'linkicon' => $hascreateicon,
                    'link' => $createbuttonurl,
                    'linktext' => $createbuttontext
                ) ,
            ) ,
        ];

        return $this->render_from_template('theme_learnr/fpicons', $fp_icons);

    }

    // Use default image on both dashboard/mycourses and in course pages.
    public function get_generated_image_for_id($id) {
        // See if user uploaded a custom header background to the theme.
        $headerbg = $this->page->theme->setting_file_url('courseheaderimagefallback', 'courseheaderimagefallback');
        $hasheaderbg = $this->page->theme->settings->courseheaderimageenabled == THEME_LEARNR_SETTING_SELECT_YES;
        if (isset($headerbg) && $hasheaderbg)  {
            return $headerbg;
        } elseif ($hasheaderbg) {
            // Usefallback image for mycourse regardless.
            return $this->page->theme->image_url('noimg', 'theme')->out();
        } else {
            $color = $this->get_generated_color_for_id($id);
            $pattern = new \core_geopattern();
            $pattern->setColor($color);
            $pattern->patternbyid($id);
            return $pattern->datauri();
        }
    }

    protected function render_courseactivities_menu(custom_menu $menu) {
        global $CFG;
        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('theme_learnr/activitygroups', $context);
        }
        return $content;
    }

    public function courseactivities_menu() {
        global $PAGE, $COURSE, $OUTPUT, $CFG;
        $menu = new custom_menu();
        $context = $this->page->context;
        if (isset($COURSE->id) && $COURSE->id > 1) {
            $branchtitle = get_string('courseactivitiesbtntext', 'theme_learnr');
            $branchlabel = $branchtitle;
            $branchurl = new moodle_url('#');
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, 10002);
            $data = theme_learnr_get_course_activities();
            foreach ($data as $modname => $modfullname) {
                if ($modname === 'resources') {
                    $branch->add($modfullname, new moodle_url('/course/resources.php', array(
                        'id' => $PAGE->course->id
                    )));
                }
                else {
                    $branch->add($modfullname, new moodle_url('/mod/' . $modname . '/index.php', array(
                        'id' => $PAGE->course->id
                    )));
                }
            }
        }
        return $this->render_courseactivities_menu($menu);
    }

    /**
     * Renders the login form.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        global $CFG, $SITE;

        $context = $form->export_for_template($this);

        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context->hideloginform = $this->page->theme->settings->hideloginform == 1;
        $context->logourl = $url;
        $context->sitename = format_string($SITE->fullname, true,
                ['context' => context_course::instance(SITEID), "escape" => false]);

        return $this->render_from_template('core/loginform', $context);
    }

    public function coursemanagementdash() {
        global $PAGE, $COURSE, $CFG, $DB, $OUTPUT, $USER;
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $hascoursemanagement = has_capability('moodle/course:viewhiddenactivities', $context);
        $togglebutton = get_string('coursemanagementbutton', 'theme_learnr');
        $showincourseonly = isset($COURSE->id) && $COURSE->id > 1 && $this->page->theme->settings->showcoursemanagement == 1 && isloggedin() && !isguestuser();
        $globalhaseasyenrollment = enrol_get_plugin('easy');
        $coursehaseasyenrollment = '';
        $easyenrollinstance = '';
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
        if ($globalhaseasyenrollment && $coursehaseasyenrollment){
        $easycodetitle = get_string('header_coursecodes', 'enrol_easy');
        $easycodelink = new moodle_url('/enrol/editinstance.php', array(
                'courseid' => $this->page->course->id,
                'id' => $easyenrollinstance->id,
                'type' => 'easy'
            ));
        }

        // Link Headers and text.
        $coursemanagementmessage = (empty($PAGE->theme->settings->coursemanagementtextbox)) ? false : format_text($PAGE->theme->settings->coursemanagementtextbox, FORMAT_HTML, array(
            'noclean' => true
        ));

        // Build links.
        $dashlinks = [
            'manageuserstitle' => get_string('manageuserstitle', 'theme_learnr'),
            'gradebooktitle' => get_string('gradebooktitle', 'theme_learnr'),
            'progresstitle' => get_string('progresstitle', 'theme_learnr'),
            'coursemanagetitle' => get_string('coursemanagetitle', 'theme_learnr'),
            'showincourseonly' => $showincourseonly,
            'hascoursemanagement' => $hascoursemanagement,

            'dashlinks' => array(
                // User Links.
                // Bulkenrol.
                array(
                    'hasuserlinks' => get_string_manager()->string_exists('pluginname', 'local_bulkenrol') ? true : false,
                    'title' => get_string('pluginname', 'local_bulkenrol'),
                    'url' => new moodle_url('/local/bulkenrol/index.php', array(
                        'id' => $PAGE->course->id
                    ))
                ) ,
                // Easy Enrollment.
                array(
                    'hasuserlinks' => get_string_manager()->string_exists('header_coursecodes', 'enrol_easy') ? true : false,
                    'title' => $easycodetitle,
                    'url' => $easycodelink,
                ),
                // Participants.
                array(
                    'hasuserlinks' => get_string_manager()->string_exists('participants', 'moodle') ? true : false,
                    'title' => get_string('participants', 'moodle'),
                    'url' => new moodle_url('/user/index.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Groups.
                array(
                    'hasuserlinks' => get_string_manager()->string_exists('groups', 'group') ? true : false,
                    'title' => get_string('groups', 'group'),
                    'url' => new moodle_url('/group/index.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Enrollment Methods.
                array(
                    'hasuserlinks' => get_string_manager()->string_exists('enrolmentmethod', 'enrol') ? true : false,
                    'title' => get_string('enrolmentmethod', 'enrol'),
                    'url' => new moodle_url('/enrol/instances.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),

                // Gradebook Links.
                //Export to MISTAR
                array(
                    'hasgradebooklinks' => get_string_manager()->string_exists('mistar:publish', 'gradeexport_mistar') ? true : false,
                    'title' => get_string('exporttomistar', 'theme_learnr'),
                    'url' => new moodle_url('/grade/export/mistar/index.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Gradebook grader.
                array(
                    'hasgradebooklinks' => get_string_manager()->string_exists('gradebook', 'grades') ? true : false,
                    'title' => get_string('gradebook', 'grades'),
                    'url' => new moodle_url('/grade/report/grader/index.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // User Gradebook Report.
                array(
                    'hasgradebooklinks' => get_string_manager()->string_exists('userreportgradebook', 'theme_learnr') ? true : false,
                    'title' => get_string('userreportgradebook', 'theme_learnr'),
                    'url' => new moodle_url('/grade/report/user/index.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Greadebook Setup.
                array(
                    'hasgradebooklinks' => get_string_manager()->string_exists('gradebooksetup', 'grades') ? true : false,
                    'title' => get_string('gradebooksetup', 'grades'),
                    'url' => new moodle_url('/grade/edit/tree/index.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),

                // Progress links.
                // Badges.
                array(
                    'hasprogresslinks' => get_string_manager()->string_exists('managebadges', 'badges') ? true : false,
                    'title' => get_string('managebadges', 'badges'),
                    'url' => new moodle_url('/badges/view.php?type=2', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Course Completion Settings.
                array(
                    'hasprogresslinks' => get_string_manager()->string_exists('editcoursecompletionsettings', 'completion') ? true : false,
                    'title' => get_string('editcoursecompletionsettings', 'completion'),
                    'url' => new moodle_url('/course/completion.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Activity Completion.
                array(
                    'hasprogresslinks' => get_string_manager()->string_exists('activitycompletion', 'completion') ? true : false,
                    'title' => get_string('activitycompletion', 'completion'),
                    'url' => new moodle_url('/report/progress/index.php', array(
                        'course' => $PAGE->course->id
                    ))
                ),
                // Activity Report.
                array(
                    'hasprogresslinks' => get_string_manager()->string_exists('outline:view', 'report_outline') ? true : false,
                    'title' => get_string('outline:view', 'report_outline'),
                    'url' => new moodle_url('/report/outline/index.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Live Logs.
                array(
                    'hasprogresslinks' => get_string_manager()->string_exists('loglive:view', 'report_loglive') ? true : false,
                    'title' => get_string('loglive:view', 'report_loglive'),
                    'url' => new moodle_url('/report/loglive/index.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),

                // Course Management.
                // Reset course.
                array(
                    'hascoursemanagelinks' => get_string_manager()->string_exists('reset', 'moodle') ? true : false,
                    'title' => get_string('reset', 'moodle'),
                    'url' => new moodle_url('/course/reset.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Copy course.
                array(
                    'hascoursemanagelinks' => get_string_manager()->string_exists('copycourse', 'moodle') ? true : false,
                    'title' => get_string('copycourse', 'moodle'),
                    'url' => new moodle_url('/backup/copy.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Backup course.
                array(
                    'hascoursemanagelinks' => get_string_manager()->string_exists('backup', 'moodle') ? true : false,
                    'title' => get_string('backup', 'moodle'),
                    'url' => new moodle_url('/backup/backup.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                // Restore course.
                array(
                    'hascoursemanagelinks' => get_string_manager()->string_exists('restore', 'moodle') ? true : false,
                    'title' => get_string('restore', 'moodle'),
                    'url' => new moodle_url('/backup/restorefile.php', array(
                        'contextid' => $PAGE->context->id
                    ))
                ),
                // Import course.
                array(
                    'hascoursemanagelinks' => get_string_manager()->string_exists('import', 'moodle') ? true : false,
                    'title' => get_string('import', 'moodle'),
                    'url' => new moodle_url('/backup/import.php', array(
                        'id' => $PAGE->course->id
                    ))
                ),
                
            ),
        ];
        return $this->render_from_template('theme_learnr/coursemanagement', $dashlinks);
    }
//End DBN Update

}
