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
 * Theme LearnR - Settings file
 *
 * @package    theme_learnr
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \theme_learnr\admin_setting_configdatetime;
use \theme_learnr\admin_setting_configstoredfilealwayscallback;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig || has_capability('theme/learnr:configure', context_system::instance())) {

    // How this file works:
    // This theme's settings are divided into multiple settings pages.
    // This is quite unusual as Boost themes would have a nice tabbed settings interface.
    // However, as we are using many hide_if constraints for our settings, we would run into the
    // stupid "Too much data passed as arguments to js_call_amd..." debugging message if we would
    // pack all settings onto just one settings page.
    // To achieve this goal, we create a custom admin settings category and fill it with several settings pages.
    // However, there is still the $settings variable which is expected by Moodle coreto be filled with the theme
    // settings and which is automatically added to the admin settings tree in one settings page.
    // To avoid that there appears an empty "LearnR" settings page near our own custom settings category,
    // we set $settings to null.

    // Avoid that the theme settings page is auto-created.
    $settings = null;

    // Create custom admin settings category.
    $ADMIN->add('themes', new admin_category('theme_learnr',
            get_string('pluginname', 'theme_learnr', null, true)));

    // Create empty settings page structure to make the site administration work on non-admin pages.
    if (!$ADMIN->fulltree) {
        // Create Look settings page
        // (and allow users with the theme/learnr:configure capability to access it).
        $tab = new admin_settingpage('theme_learnr_look',
                get_string('configtitlelook', 'theme_learnr', null, true),
                'theme/learnr:configure');
        $ADMIN->add('theme_learnr', $tab);

        // Create Feel settings page
        // (and allow users with the theme/learnr:configure capability to access it).
        $tab = new admin_settingpage('theme_learnr_feel',
                get_string('configtitlefeel', 'theme_learnr', null, true),
                'theme/learnr:configure');
        $ADMIN->add('theme_learnr', $tab);

        // Create Content settings page
        // (and allow users with the theme/learnr:configure capability to access it).
        $tab = new admin_settingpage('theme_learnr_content',
                get_string('configtitlecontent', 'theme_learnr', null, true),
                'theme/learnr:configure');
        $ADMIN->add('theme_learnr', $tab);

        // Create Functionality settings page
        // (and allow users with the theme/learnr:configure capability to access it).
        $tab = new admin_settingpage('theme_learnr_functionality',
                get_string('configtitlefunctionality', 'theme_learnr', null, true),
                'theme/learnr:configure');
        $ADMIN->add('theme_learnr', $tab);

        // Create Flavours settings page as external page
        // (and allow users with the theme/learnr:configure capability to access it).
        $flavourspage = new admin_externalpage('theme_learnr_flavours',
                get_string('configtitleflavours', 'theme_learnr', null, true),
                new moodle_url('/theme/learnr/flavours/overview.php'),
                'theme/learnr:configure');
        $ADMIN->add('theme_learnr', $flavourspage);
    }

    // Create full settings page structure.
    // @codingStandardsIgnoreLine
    else if ($ADMIN->fulltree) {

        // Require the necessary libraries.
        require_once($CFG->dirroot . '/theme/learnr/lib.php');
        require_once($CFG->dirroot . '/theme/learnr/locallib.php');

        // Prepare options array for select settings.
        // Due to MDL-58376, we will use binary select settings instead of checkbox settings throughout this theme.
        $yesnooption = array(THEME_LEARNR_SETTING_SELECT_YES => get_string('yes'),
                THEME_LEARNR_SETTING_SELECT_NO => get_string('no'));

        // Prepare regular expression for checking if the value is a percent number (from 0% to 100%) or a pixel number
        // (with 3 or 4 digits) or a viewport width number (from 0 to 100).
        $widthregex = '/^((\d{1,2}|100)%)|((\d{1,2}|100)vw)|(\d{3,4}px)$/';


        // Create Look settings page with tabs
        // (and allow users with the theme/learnr:configure capability to access it).
        $page = new theme_boost_admin_settingspage_tabs('theme_learnr_look',
                get_string('configtitlelook', 'theme_learnr', null, true),
                'theme/learnr:configure');


        // Create general settings tab.
        $tab = new admin_settingpage('theme_learnr_look_general', get_string('generalsettings', 'theme_boost', null, true));

        // Create theme presets heading.
        $name = 'theme_learnr/presetheading';
        $title = get_string('presetheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Replicate the preset setting from theme_boost, but use our own file area.
        $name = 'theme_learnr/preset';
        $title = get_string('preset', 'theme_boost', null, true);
        $description = get_string('preset_desc', 'theme_boost', null, true);
        $default = 'default.scss';

        $context = context_system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'theme_learnr', 'preset', 0, 'itemid, filepath, filename', false);

        $choices = [];
        foreach ($files as $file) {
            $choices[$file->get_filename()] = $file->get_filename();
        }
        $choices['default.scss'] = 'default.scss';
        $choices['plain.scss'] = 'plain.scss';

        $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'learnr');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Replicate the preset files setting from theme_boost.
        $name = 'theme_learnr/presetfiles';
        $title = get_string('presetfiles', 'theme_boost', null, true);
        $description = get_string('presetfiles_desc', 'theme_boost', null, true);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
                array('maxfiles' => 20, 'accepted_types' => array('.scss')));
        $tab->add($setting);

        //Begin DBN Update
        // Sections Display Options.
        $name = 'theme_learnr/sectionstyle';
        $title = get_string('sectionstyle' , 'theme_learnr');
        $description = get_string('sectionstyle_desc', 'theme_learnr');
        $option1 = get_string('sections-learnr', 'theme_learnr');
        $option2 = get_string('sections-boxed', 'theme_learnr');
        $option3 = get_string('sections-boost', 'theme_learnr');
        $option4 = get_string('sections-bars', 'theme_learnr');
        $default = '1';
        $choices = array('1'=>$option1, '2'=>$option2, '3'=>$option3, '4'=>$option4);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Course Tile Display Styles
        $name = 'theme_learnr/coursetilestyle';
        $title = get_string('coursetilestyle' , 'theme_learnr');
        $description = get_string('coursetilestyle_desc', 'theme_learnr');
        $coursestyle1 = get_string('coursestyle1', 'theme_learnr');
        $coursestyle2 = get_string('coursestyle2', 'theme_learnr');
        $coursestyle3 = get_string('coursestyle3', 'theme_learnr');
        $coursestyle4 = get_string('coursestyle4', 'theme_learnr');
        $coursestyle5 = get_string('coursestyle5', 'theme_learnr');
        $coursestyle6 = get_string('coursestyle6', 'theme_learnr');
        $coursestyle7 = get_string('coursestyle7', 'theme_learnr');
        $coursestyle10 = get_string('coursestyle8', 'theme_learnr');
        $default = '10';
        $choices = array('1'=>$coursestyle1, '2'=>$coursestyle2, '3'=>$coursestyle3, '4'=>$coursestyle4, '5'=>$coursestyle5, '6'=>$coursestyle6, '7'=>$coursestyle7,'8'=>$coursestyle10);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // trim title setting.
        $name = 'theme_learnr/trimtitle';
        $title = get_string('trimtitle', 'theme_learnr');
        $description = get_string('trimtitle_desc', 'theme_learnr');
        $default = '256';
        $choices = array(
                '15' => '15',
                '20' => '20',
                '30' => '30',
                '40' => '40',
                '50' => '50',
                '60' => '60',
                '70' => '70',
                '80' => '80',
                '90' => '90',
                '100' => '100',
                '110' => '110',
                '120' => '120',
                '130' => '130',
                '140' => '140',
                '150' => '150',
                '175' => '175',
                '200' => '200',
                '256' => '256',
                );
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // trim title setting.
        $name = 'theme_learnr/trimsummary';
        $title = get_string('trimsummary', 'theme_learnr');
        $description = get_string('trimsummary_desc', 'theme_learnr');
        $default = '300';
        $choices = array(
                '30' => '30',
                '60' => '60',
                '90' => '90',
                '100' => '100',
                '150' => '150',
                '200' => '200',
                '250' => '250',
                '300' => '300',
                '350' => '350',
                '400' => '400',
                '450' => '450',
                '500' => '500',
                '600' => '600',
                '800' => '800',
                );
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Courses height
        $name = 'theme_learnr/courseboxheight';
        $title = get_string('courseboxheight', 'theme_learnr');
        $description = get_string('courseboxheight_desc', 'theme_learnr');;
        $default = '250px';
        $choices = array(
                '200px' => '200px',
                '225px' => '225px',
                '250px' => '250px',
                '275px' => '275px',
                '300px' => '300px',
                '325px' => '325px',
                '350px' => '350px',
                '375px' => '375px',
                '400px' => '400px',
                );
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);
        //End DBN Update

        // Add tab to settings page.
        $page->add($tab);


        // Create SCSS tab.
        $tab = new admin_settingpage('theme_learnr_look_scss', get_string('scsstab', 'theme_learnr', null, true));

        // Create Raw SCSS heading.
        $name = 'theme_learnr/scssheading';
        $title = get_string('scssheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Replicate the Raw initial SCSS setting from theme_boost.
        $name = 'theme_learnr/scsspre';
        $title = get_string('rawscsspre', 'theme_boost', null, true);
        $description = get_string('rawscsspre_desc', 'theme_boost', null, true);
        $default = '';
        $setting = new admin_setting_scsscode($name, $title, $description, $default, PARAM_RAW);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Replicate the Raw SCSS setting from theme_boost.
        $name = 'theme_learnr/scss';
        $title = get_string('rawscss', 'theme_boost', null, true);
        $description = get_string('rawscss_desc', 'theme_boost', null, true);
        $default = '';
        $setting = new admin_setting_scsscode($name, $title, $description, $default, PARAM_RAW);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Create page tab.
        $tab = new admin_settingpage('theme_learnr_look_page', get_string('pagetab', 'theme_learnr', null, true));

        // Create page width heading.
        $name = 'theme_learnr/pagewidthheading';
        $title = get_string('pagewidthheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Course content max width.
        $name = 'theme_learnr/coursecontentmaxwidth';
        $title = get_string('coursecontentmaxwidthsetting', 'theme_learnr', null, true);
        $description = get_string('coursecontentmaxwidthsetting_desc', 'theme_learnr', null, true);
        $default = '95%';
        $setting = new admin_setting_configtext($name, $title, $description, $default, $widthregex, 6);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Medium content max width.
        $name = 'theme_learnr/mediumcontentmaxwidth';
        $title = get_string('mediumcontentmaxwidthsetting', 'theme_learnr', null, true);
        $description = get_string('mediumcontentmaxwidthsetting_desc', 'theme_learnr', null, true);
        $default = '95%';
        $setting = new admin_setting_configtext($name, $title, $description, $default, $widthregex, 6);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Create branding tab.
        $tab = new admin_settingpage('theme_learnr_look_branding', get_string('brandingtab', 'theme_learnr', null, true));

        // Create logos heading.
        $name = 'theme_learnr/logosheading';
        $title = get_string('logosheading', 'theme_learnr', null, true);
        $notificationurl = new moodle_url('/admin/settings.php', array('section' => 'logos'));
        $notification = new \core\output\notification(get_string('logosheading_desc', 'theme_learnr', $notificationurl->out()),
                \core\output\notification::NOTIFY_INFO);
        $notification->set_show_closebutton(false);
        $description = $OUTPUT->render($notification);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        // Replicate the logo setting from core_admin.
        $name = 'theme_learnr/logo';
        $title = get_string('logosetting', 'theme_learnr', null, true);
        $description = get_string('logosetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo', 0,
                array('maxfiles' => 1, 'accepted_types' => 'web_image'));
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Replicate the compact logo setting from core_admin.
        $name = 'theme_learnr/logocompact';
        $title = get_string('logocompactsetting', 'theme_learnr', null, true);
        $description = get_string('logocompactsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'logocompact', 0,
                array('maxfiles' => 1, 'accepted_types' => 'web_image'));
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Create favicon heading.
        $name = 'theme_learnr/faviconheading';
        $title = get_string('faviconheading', 'theme_learnr', null, true);
        $notificationurl = new moodle_url('/admin/settings.php', array('section' => 'logos'));
        $notification = new \core\output\notification(get_string('faviconheading_desc', 'theme_learnr',
                $notificationurl->out()), \core\output\notification::NOTIFY_INFO);
        $notification->set_show_closebutton(false);
        $description = $OUTPUT->render($notification);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        // Replicate the favicon setting from core_admin.
        $name = 'theme_learnr/favicon';
        $title = get_string('faviconsetting', 'theme_learnr', null, true);
        $description = get_string('faviconsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'favicon', 0,
                array('maxfiles' => 1, 'accepted_types' => 'image'));
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Create background images heading.
        $name = 'theme_learnr/backgroundimagesheading';
        $title = get_string('backgroundimagesheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Replicate the Background image setting from theme_boost.
        $name = 'theme_learnr/backgroundimage';
        $title = get_string('backgroundimagesetting', 'theme_learnr', null, true);
        $description = get_string('backgroundimagesetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage', 0,
                array('maxfiles' => 1, 'accepted_types' => 'web_image'));
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Create brand colors heading.
        $name = 'theme_learnr/brandcolorsheading';
        $title = get_string('brandcolorsheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Replicate the Variable $body-color setting from theme_boost.
        $name = 'theme_learnr/brandcolor';
        $title = get_string('brandcolor', 'theme_boost', null, true);
        $description = get_string('brandcolor_desc', 'theme_boost', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        //Begin DBN Update
        // We use an empty default value because the default colour should come from the preset .

        $name = 'theme_learnr/pagenavbuttonsbg';
        $title = get_string('pagenavbuttonsbg', 'theme_learnr');
        $description = get_string('pagenavbuttonsbg_desc', 'theme_learnr');
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/drawerbg';
        $title = get_string('drawerbg', 'theme_learnr');
        $description = get_string('drawerbg_desc', 'theme_learnr');
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/bodybg';
        $title = get_string('bodybg', 'theme_learnr');
        $description = get_string('bodybg_desc', 'theme_learnr');
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/bgwhite';
        $title = get_string('bgwhite', 'theme_learnr');
        $description = get_string('bgwhite_desc', 'theme_learnr');
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/bgdark';
        $title = get_string('bgdark', 'theme_learnr');
        $description = get_string('bgdark_desc', 'theme_learnr');
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // End DBN Update

        // Create Bootstrap colors heading.
        $name = 'theme_learnr/bootstrapcolorsheading';
        $title = get_string('bootstrapcolorsheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Bootstrap color for 'success'.
        $name = 'theme_learnr/bootstrapcolorsuccess';
        $title = get_string('bootstrapcolorsuccesssetting', 'theme_learnr', null, true);
        $description = get_string('bootstrapcolorsuccesssetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Bootstrap color for 'info'.
        $name = 'theme_learnr/bootstrapcolorinfo';
        $title = get_string('bootstrapcolorinfosetting', 'theme_learnr', null, true);
        $description = get_string('bootstrapcolorinfosetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Bootstrap color for 'warning'.
        $name = 'theme_learnr/bootstrapcolorwarning';
        $title = get_string('bootstrapcolorwarningsetting', 'theme_learnr', null, true);
        $description = get_string('bootstrapcolorwarningsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Bootstrap color for 'danger'.
        $name = 'theme_learnr/bootstrapcolordanger';
        $title = get_string('bootstrapcolordangersetting', 'theme_learnr', null, true);
        $description = get_string('bootstrapcolordangersetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Create activity icon colors heading.
        $name = 'theme_learnr/activityiconcolorsheading';
        $title = get_string('activityiconcolorsheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Activity icon color for 'administration'.
        $name = 'theme_learnr/activityiconcoloradministration';
        $title = get_string('activityiconcoloradministrationsetting', 'theme_learnr', null, true);
        $description = get_string('activityiconcoloradministrationsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Activity icon color for 'assessment'.
        $name = 'theme_learnr/activityiconcolorassessment';
        $title = get_string('activityiconcolorassessmentsetting', 'theme_learnr', null, true);
        $description = get_string('activityiconcolorassessmentsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Activity icon color for 'collaboration'.
        $name = 'theme_learnr/activityiconcolorcollaboration';
        $title = get_string('activityiconcolorcollaborationsetting', 'theme_learnr', null, true);
        $description = get_string('activityiconcolorcollaborationsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Activity icon color for 'communication'.
        $name = 'theme_learnr/activityiconcolorcommunication';
        $title = get_string('activityiconcolorcommunicationsetting', 'theme_learnr', null, true);
        $description = get_string('activityiconcolorcommunicationsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Activity icon color for 'content'.
        $name = 'theme_learnr/activityiconcolorcontent';
        $title = get_string('activityiconcolorcontentsetting', 'theme_learnr', null, true);
        $description = get_string('activityiconcolorcontentsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Activity icon color for 'interface'.
        $name = 'theme_learnr/activityiconcolorinterface';
        $title = get_string('activityiconcolorinterfacesetting', 'theme_learnr', null, true);
        $description = get_string('activityiconcolorinterfacesetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Create navbar heading.
        $name = 'theme_learnr/navbarheading';
        $title = get_string('navbarheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Navbar color.
        $name = 'theme_learnr/navbarcolor';
        $title = get_string('navbarcolorsetting', 'theme_learnr', null, true);
        $description = get_string('navbarcolorsetting_desc', 'theme_learnr', null, true);
        $navbarcoloroptions = array(
                THEME_LEARNR_SETTING_NAVBARCOLOR_LIGHT =>
                        get_string('navbarcolorsetting_light', 'theme_learnr'),
                THEME_LEARNR_SETTING_NAVBARCOLOR_DARK =>
                        get_string('navbarcolorsetting_dark', 'theme_learnr'),
                THEME_LEARNR_SETTING_NAVBARCOLOR_PRIMARYLIGHT =>
                        get_string('navbarcolorsetting_primarylight', 'theme_learnr'),
                THEME_LEARNR_SETTING_NAVBARCOLOR_PRIMARYDARK =>
                        get_string('navbarcolorsetting_primarydark', 'theme_learnr'));
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_NAVBARCOLOR_DARK,
                $navbarcoloroptions);
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Create login page tab.
        $tab = new admin_settingpage('theme_learnr_look_loginpage',
                get_string('loginpagetab', 'theme_learnr', null, true));

        // Create login page background images heading.
        $name = 'theme_learnr/loginbackgroundimagesheading';
        $title = get_string('loginbackgroundimagesheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Create login page background image setting.
        $name = 'theme_learnr/loginbackgroundimage';
        $title = get_string('loginbackgroundimage', 'theme_learnr', null, true);
        $description = get_string('loginbackgroundimage_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage', 0,
                array('maxfiles' => 25, 'accepted_types' => 'web_image'));
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Create login page background image text setting.
        $name = 'theme_learnr/loginbackgroundimagetext';
        $title = get_string('loginbackgroundimagetextsetting', 'theme_learnr', null, true);
        $description = get_string('loginbackgroundimagetextsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configtextarea($name, $title, $description, null, PARAM_TEXT);
        $tab->add($setting);

        // Create login form heading.
        $name = 'theme_learnr/loginformheading';
        $title = get_string('loginformheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Begin DBN Update.
        // Show/hide login form.
        $name = 'theme_learnr/hideloginform';
        $title = get_string('hideloginform', 'theme_learnr');
        $description = get_string('hideloginform_desc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);
        // End DBN Update.

        // Create login form position setting.
        $name = 'theme_learnr/loginformposition';
        $title = get_string('loginformpositionsetting', 'theme_learnr', null, true);
        $description = get_string('loginformpositionsetting_desc', 'theme_learnr', null, true);
        $loginformoptions = array(
                THEME_LEARNR_SETTING_LOGINFORMPOS_CENTER => get_string('loginformpositionsetting_center', 'theme_learnr'),
                THEME_LEARNR_SETTING_LOGINFORMPOS_LEFT => get_string('loginformpositionsetting_left', 'theme_learnr'),
                THEME_LEARNR_SETTING_LOGINFORMPOS_RIGHT => get_string('loginformpositionsetting_right', 'theme_learnr'));
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_LOGINFORMPOS_CENTER,
                $loginformoptions);
        $tab->add($setting);

        // Create login form transparency setting.
        $name = 'theme_learnr/loginformtransparency';
        $title = get_string('loginformtransparencysetting', 'theme_learnr', null, true);
        $description = get_string('loginformtransparencysetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_NO, $yesnooption);
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Create course tab.
        $tab = new admin_settingpage('theme_learnr_look_course',
                get_string('coursetab', 'theme_learnr', null, true));

        // Create course header heading.
        $name = 'theme_learnr/courseheaderheading';
        $title = get_string('courseheaderheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Display the course image in the course header.
        $name = 'theme_learnr/courseheaderimageenabled';
        $title = get_string('courseheaderimageenabled', 'theme_learnr', null, true);
        $description = get_string('courseheaderimageenabled_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_NO, $yesnooption);
        $tab->add($setting);

        // Setting: Fallback course header image.
        $name = 'theme_learnr/courseheaderimagefallback';
        $title = get_string('courseheaderimagefallback', 'theme_learnr', null, true);
        $description = get_string('courseheaderimagefallback_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'courseheaderimagefallback', 0,
                array('maxfiles' => 1, 'accepted_types' => 'web_image'));
        $tab->add($setting);
        $page->hide_if('theme_learnr/courseheaderimagefallback', 'theme_learnr/courseheaderimageenabled', 'neq',
                THEME_LEARNR_SETTING_SELECT_YES);

        // Setting: Course header image layout.
        $name = 'theme_learnr/courseheaderimagelayout';
        $title = get_string('courseheaderimagelayout', 'theme_learnr', null, true);
        $description = get_string('courseheaderimagelayout_desc', 'theme_learnr', null, true);
        $courseheaderimagelayoutoptions = array(
                THEME_LEARNR_SETTING_COURSEIMAGELAYOUT_STACKEDDARK =>
                        get_string('courseheaderimagelayoutstackeddark', 'theme_learnr'),
                THEME_LEARNR_SETTING_COURSEIMAGELAYOUT_STACKEDLIGHT =>
                        get_string('courseheaderimagelayoutstackedlight', 'theme_learnr'),
                THEME_LEARNR_SETTING_COURSEIMAGELAYOUT_HEADINGABOVE =>
                        get_string('courseheaderimagelayoutheadingabove', 'theme_learnr'));
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_LEARNR_SETTING_COURSEIMAGELAYOUT_HEADINGABOVE, $courseheaderimagelayoutoptions);
        $tab->add($setting);
        $page->hide_if('theme_learnr/courseheaderimagelayout', 'theme_learnr/courseheaderimageenabled', 'neq',
                THEME_LEARNR_SETTING_SELECT_YES);

        // Setting: Course header image height.
        $name = 'theme_learnr/courseheaderimageheight';
        $title = get_string('courseheaderimageheight', 'theme_learnr', null, true);
        $description = get_string('courseheaderimageheight_desc', 'theme_learnr', null, true);
        $courseheaderimageheightoptions = array(
                THEME_LEARNR_SETTING_HEIGHT_100PX => THEME_LEARNR_SETTING_HEIGHT_100PX,
                THEME_LEARNR_SETTING_HEIGHT_150PX => THEME_LEARNR_SETTING_HEIGHT_150PX,
                THEME_LEARNR_SETTING_HEIGHT_200PX => THEME_LEARNR_SETTING_HEIGHT_200PX,
                THEME_LEARNR_SETTING_HEIGHT_250PX => THEME_LEARNR_SETTING_HEIGHT_250PX);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_HEIGHT_200PX,
                $courseheaderimageheightoptions);
        $tab->add($setting);
        $page->hide_if('theme_learnr/courseheaderimageheight', 'theme_learnr/courseheaderimageenabled', 'neq',
                THEME_LEARNR_SETTING_SELECT_YES);

        // Setting: Course header image position.
        $name = 'theme_learnr/courseheaderimageposition';
        $title = get_string('courseheaderimageposition', 'theme_learnr', null, true);
        $description = get_string('courseheaderimageposition_desc', 'theme_learnr', null, true);
        $courseheaderimagepositionoptions = array(
                THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_CENTER =>
                        THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_CENTER,
                THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_TOP =>
                        THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_TOP,
                THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_BOTTOM =>
                        THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_BOTTOM,
                THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_TOP =>
                        THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_TOP,
                THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_CENTER =>
                        THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_CENTER,
                THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_BOTTOM =>
                        THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_BOTTOM,
                THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_TOP =>
                        THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_TOP,
                THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_CENTER =>
                        THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_CENTER,
                THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_BOTTOM =>
                        THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_BOTTOM);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_CENTER, $courseheaderimagepositionoptions);
        $tab->add($setting);
        $page->hide_if('theme_learnr/courseheaderimageposition', 'theme_learnr/courseheaderimageenabled', 'neq',
                THEME_LEARNR_SETTING_SELECT_YES);

         // Begin DBN Update.
        // Show/hide course index navigation.
        $name = 'theme_learnr/showcourseindexnav';
        $title = get_string('showcourseindexnav', 'theme_learnr');
        $description = get_string('showcourseindexnav_desc', 'theme_learnr');
        $default = '1';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Show hide course management panel.
        $name = 'theme_learnr/showcoursemanagement';
        $title = get_string('showcoursemanagement', 'theme_learnr');
        $description = get_string('showcoursemanagement_desc', 'theme_learnr');
        $default = '1';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Show/hide course progressbar learnr.
        $name = 'theme_learnr/showprogressbar';
        $title = get_string('showprogressbar', 'theme_learnr');
        $description = get_string('showprogressbar_desc', 'theme_learnr');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);
        
        // Show hide Latest Courses learnr.
        $name = 'theme_learnr/showlatestcourses';
        $title = get_string('showlatestcourses', 'theme_learnr');
        $description = get_string('showlatestcourses_desc', 'theme_learnr');
        $default = '1';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Show hide easy enrollment btn.
        $name = 'theme_learnr/showeasyenrolbtn';
        $title = get_string('showeasyenrolbtn', 'theme_learnr');
        $description = get_string('showeasyenrolbtn_desc', 'theme_learnr');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Show Course Activities Grouping Menu
        $name = 'theme_learnr/showcourseactivities';
        $title = get_string('showcourseactivities', 'theme_learnr');
        $description = get_string('showcourseactivities_desc', 'theme_learnr');
        $default = '0';
        $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        //End DBN Update.

        // Add tab to settings page.
        $page->add($tab);

        // Begin DBN Update.
        // Create static pages tab.
        $tab = new admin_settingpage('theme_learnr_content_iconnavbar',
                get_string('iconnavbartab', 'theme_learnr', null, true));

        // This is the descriptor for the page.
        $name = 'theme_learnr/iconnavinfo';
        $heading = get_string('iconnavinfo', 'theme_learnr');
        $information = get_string('iconnavinfo_desc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);
        
        // This is the descriptor for teacher create a course
        $name = 'theme_learnr/createinfo';
        $heading = get_string('createinfo', 'theme_learnr');
        $information = get_string('createinfodesc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);

        // Creator Icon
        $name = 'theme_learnr/createicon';
        $title = get_string('navicon', 'theme_learnr');
        $description = get_string('navicondesc', 'theme_learnr');
        $default = 'edit';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/createbuttontext';
        $title = get_string('naviconbuttontext', 'theme_learnr');
        $description = get_string('naviconbuttontextdesc', 'theme_learnr');
        $default = get_string('naviconbuttoncreatetextdefault', 'theme_learnr');
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/createbuttonurl';
        $title = get_string('naviconbuttonurl', 'theme_learnr');
        $description = get_string('naviconbuttonurldesc', 'theme_learnr');
        $default =  $CFG->wwwroot.'/course/edit.php?category=1';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);


        // This is the descriptor for teacher create a course
        $name = 'theme_learnr/sliderinfo';
        $heading = get_string('sliderinfo', 'theme_learnr');
        $information = get_string('sliderinfodesc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);

        // Creator Icon
        $name = 'theme_learnr/slideicon';
        $title = get_string('navicon', 'theme_learnr');
        $description = get_string('naviconslidedesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/slideiconbuttontext';
        $title = get_string('naviconbuttontext', 'theme_learnr');
        $description = get_string('naviconbuttontextdesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Slide Textbox.
        $name = 'theme_learnr/slidetextbox';
            $title = get_string('slidetextbox', 'theme_learnr');
            $description = get_string('slidetextbox_desc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);



            // This is the descriptor for icon One
            $name = 'theme_learnr/navicon1info';
            $heading = get_string('navicon1', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            // icon One
            $name = 'theme_learnr/nav1icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav1buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav1buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav1target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon One
            $name = 'theme_learnr/navicon2info';
            $heading = get_string('navicon2', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_learnr/nav2icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav2buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav2buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav2target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon three
            $name = 'theme_learnr/navicon3info';
            $heading = get_string('navicon3', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_learnr/nav3icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav3buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav3buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav3target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon four
            $name = 'theme_learnr/navicon4info';
            $heading = get_string('navicon4', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_learnr/nav4icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav4buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav4buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default =  '';
            $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav4target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon four
            $name = 'theme_learnr/navicon5info';
            $heading = get_string('navicon5', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_learnr/nav5icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav5buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav5buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav5target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon six
            $name = 'theme_learnr/navicon6info';
            $heading = get_string('navicon6', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_learnr/nav6icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav6buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav6buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav6target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            // This is the descriptor for icon seven
            $name = 'theme_learnr/navicon7info';
            $heading = get_string('navicon7', 'theme_learnr');
            $information = get_string('navicondesc', 'theme_learnr');
            $setting = new admin_setting_heading($name, $heading, $information);
            $tab->add($setting);

            $name = 'theme_learnr/nav7icon';
            $title = get_string('navicon', 'theme_learnr');
            $description = get_string('navicondesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav7buttontext';
            $title = get_string('naviconbuttontext', 'theme_learnr');
            $description = get_string('naviconbuttontextdesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav7buttonurl';
            $title = get_string('naviconbuttonurl', 'theme_learnr');
            $description = get_string('naviconbuttonurldesc', 'theme_learnr');
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

            $name = 'theme_learnr/nav7target';
            $title = get_string('marketingurltarget' , 'theme_learnr');
            $description = get_string('marketingurltargetdesc', 'theme_learnr');
            $target1 = get_string('marketingurltargetself', 'theme_learnr');
            $target2 = get_string('marketingurltargetnew', 'theme_learnr');
            $target3 = get_string('marketingurltargetparent', 'theme_learnr');
            $default = 'target1';
            $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
            $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);

        // This is the descriptor for icon eight
        $name = 'theme_learnr/navicon8info';
        $heading = get_string('navicon8', 'theme_learnr');
        $information = get_string('navicondesc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);

        $name = 'theme_learnr/nav8icon';
        $title = get_string('navicon', 'theme_learnr');
        $description = get_string('navicondesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/nav8buttontext';
        $title = get_string('naviconbuttontext', 'theme_learnr');
        $description = get_string('naviconbuttontextdesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/nav8buttonurl';
        $title = get_string('naviconbuttonurl', 'theme_learnr');
        $description = get_string('naviconbuttonurldesc', 'theme_learnr');
        $default = '';
        $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_learnr/nav8target';
        $title = get_string('marketingurltarget' , 'theme_learnr');
        $description = get_string('marketingurltargetdesc', 'theme_learnr');
        $target1 = get_string('marketingurltargetself', 'theme_learnr');
        $target2 = get_string('marketingurltargetnew', 'theme_learnr');
        $target3 = get_string('marketingurltargetparent', 'theme_learnr');
        $default = 'target1';
        $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);
        // End DBN Update.


        // Create E_Mail branding tab.
        $tab = new admin_settingpage('theme_learnr_look_emailbranding',
                get_string('emailbrandingtab', 'theme_learnr', null, true));

        // Create E_Mail branding introduction heading.
        $name = 'theme_learnr/emailbrandingintroheading';
        $title = get_string('emailbrandingintroheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Create E-Mail branding introduction note.
        $name = 'theme_learnr/emailbrandingintronote';
        $title = '';
        $description = '<div class="alert alert-info" role="alert">'.
                get_string('emailbrandingintronote', 'theme_learnr', null, true).'</div>';
        $setting = new admin_setting_description($name, $title, $description);
        $tab->add($setting);

        // Create E-Mail branding instruction.
        $name = 'theme_learnr/emailbrandinginstruction';
        $title = '';
        $description = '<h4>'.get_string('emailbrandinginstruction', 'theme_learnr', null, true).'</h4>';
        $description .= '<p>'.get_string('emailbrandinginstruction0', 'theme_learnr', null, true).'</p>';
        $emailbrandinginstructionli1url = new moodle_url('/admin/tool/customlang/index.php', array('lng' => $CFG->lang));
        $description .= '<ul><li>'.get_string('emailbrandinginstructionli1', 'theme_learnr',
                array('url' => $emailbrandinginstructionli1url->out(), 'lang' => $CFG->lang), true).'</li>';
        $description .= '<li>'.get_string('emailbrandinginstructionli2', 'theme_learnr', null, true).'</li>';
        $description .= '<ul><li>'.get_string('emailbrandinginstructionli2li1', 'theme_learnr', null, true).'</li>';
        $description .= '<li>'.get_string('emailbrandinginstructionli2li2', 'theme_learnr', null, true).'</li>';
        $description .= '<li>'.get_string('emailbrandinginstructionli2li3', 'theme_learnr', null, true).'</li>';
        $description .= '<li>'.get_string('emailbrandinginstructionli2li4', 'theme_learnr', null, true).'</li></ul>';
        $description .= '<li>'.get_string('emailbrandinginstructionli3', 'theme_learnr', null, true).'</li>';
        $description .= '<li>'.get_string('emailbrandinginstructionli4', 'theme_learnr', null, true).'</li></ul>';
        $description .= '<h4>'.get_string('emailbrandingpitfalls', 'theme_learnr', null, true).'</h4>';
        $description .= '<p>'.get_string('emailbrandingpitfalls0', 'theme_learnr', null, true).'</p>';
        $description .= '<ul><li>'.get_string('emailbrandingpitfallsli1', 'theme_learnr', null, true).'</li>';
        $description .= '<li>'.get_string('emailbrandingpitfallsli2', 'theme_learnr', null, true).'</li>';
        $description .= '<li>'.get_string('emailbrandingpitfallsli3', 'theme_learnr', null, true).'</li>';
        $description .= '<li>'.get_string('emailbrandingpitfallsli4', 'theme_learnr', null, true).'</li>';
        $description .= '<li>'.get_string('emailbrandingpitfallsli5', 'theme_learnr', null, true).'</li>';
        $description .= '<li>'.get_string('emailbrandingpitfallsli6', 'theme_learnr', null, true).'</li></ul>';
        $setting = new admin_setting_description($name, $title, $description);
        $tab->add($setting);

        // Create HTML E-Mails heading.
        $name = 'theme_learnr/emailbrandinghtmlheading';
        $title = get_string('emailbrandinghtmlheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Get HTML E-Mail preview.
        $htmlpreview = theme_learnr_get_emailbrandinghtmlpreview();

        // If the HTML E-Mails are customized.
        if ($htmlpreview != null) {
            // Create HTML E-Mail intro.
            $name = 'theme_learnr/emailbrandinghtmlintro';
            $title = '';
            $description = '<div class="alert alert-info" role="alert">'.
                    get_string('emailbrandinghtmlintro', 'theme_learnr', null, true).'</div>';
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);

            // Create HTML E-Mail preview.
            $name = 'theme_learnr/emailbrandinghtmlpreview';
            $title = '';
            $description = $htmlpreview;
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);

            // Otherwise.
        } else {
            // Create HTML E-Mail intro.
            $name = 'theme_learnr/emailbrandinghtmlnopreview';
            $title = '';
            $description = '<div class="alert alert-info" role="alert">'.
                    get_string('emailbrandinghtmlnopreview', 'theme_learnr', null, true).'</div>';
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);
        }

        // Create Plaintext E-Mails heading.
        $name = 'theme_learnr/emailbrandingtextheading';
        $title = get_string('emailbrandingtextheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Get Plaintext E-Mail preview.
        $textpreview = theme_learnr_get_emailbrandingtextpreview();

        // If the Plaintext E-Mails are customized.
        if ($textpreview != null) {
            // Create Plaintext E-Mail intro.
            $name = 'theme_learnr/emailbrandingtextintro';
            $title = '';
            $description = '<div class="alert alert-info" role="alert">'.
                    get_string('emailbrandingtextintro', 'theme_learnr', null, true).'</div>';
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);

            // Create Plaintext E-Mail preview.
            $name = 'theme_learnr/emailbrandingtextpreview';
            $title = '';
            $description = $textpreview;
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);

            // Otherwise.
        } else {
            // Create Plaintext E-Mail intro.
            $name = 'theme_learnr/emailbrandingtextnopreview';
            $title = '';
            $description = '<div class="alert alert-info" role="alert">'.
                    get_string('emailbrandingtextnopreview', 'theme_learnr', null, true).'</div>';
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);
        }

        // Add tab to settings page.
        $page->add($tab);


        // Create resources tab.
        $tab = new admin_settingpage('theme_learnr_look_resources',
                get_string('resourcestab', 'theme_learnr', null, true));

        // Create additional resources heading.
        $name = 'theme_learnr/additionalresourcesheading';
        $title = get_string('additionalresourcesheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Additional resources.
        $name = 'theme_learnr/additionalresources';
        $title = get_string('additionalresourcessetting', 'theme_learnr', null, true);
        $description = get_string('additionalresourcessetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'additionalresources', 0,
                array('maxfiles' => -1));
        $tab->add($setting);

        // Information: Additional resources list.
        // If there is at least one file uploaded.
        if (!empty(get_config('theme_learnr', 'additionalresources'))) {
            // Prepare the widget.
            $name = 'theme_learnr/additionalresourceslist';
            $title = get_string('additionalresourceslistsetting', 'theme_learnr', null, true);
            $description = get_string('additionalresourceslistsetting_desc', 'theme_learnr', null, true).'<br /><br />'.
                    get_string('resourcescachecontrolnote', 'theme_learnr', null, true);

            // Append the file list to the description.
            $templatecontext = array('files' => theme_learnr_get_additionalresources_templatecontext());
            $description .= $OUTPUT->render_from_template('theme_learnr/settings-additionalresources-filelist',
                    $templatecontext);

            // Finish the widget.
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);

        }

        // Create custom fonts heading.
        $name = 'theme_learnr/customfontsheading';
        $title = get_string('customfontsheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Register the webfonts file types for filtering the uploads in the subsequent admin settings.
        // This function call may return false. In this case, the filetypes were not registered and we
        // can't restrict the filetypes in the subsequent admin settings unfortunately.
        $registerfontsresult = theme_learnr_register_webfonts_filetypes();

        // Setting: Custom fonts.
        $name = 'theme_learnr/customfonts';
        $title = get_string('customfontssetting', 'theme_learnr', null, true);
        $description = get_string('customfontssetting_desc', 'theme_learnr', null, true);
        if ($registerfontsresult == true) {
            $setting = new admin_setting_configstoredfile($name, $title, $description, 'customfonts', 0,
                    array('maxfiles' => -1, 'accepted_types' => theme_learnr_get_webfonts_extensions()));
        } else {
            $setting = new admin_setting_configstoredfile($name, $title, $description, 'customfonts', 0,
                    array('maxfiles' => -1));
        }
        $tab->add($setting);

        // Information: Custom fonts list.
        // If there is at least one file uploaded.
        if (!empty(get_config('theme_learnr', 'customfonts'))) {
            // Prepare the widget.
            $name = 'theme_learnr/customfontslist';
            $title = get_string('customfontslistsetting', 'theme_learnr', null, true);
            $description = get_string('customfontslistsetting_desc', 'theme_learnr', null, true);

            // Append the file list to the description.
            $templatecontext = array('files' => theme_learnr_get_customfonts_templatecontext());
            $description .= $OUTPUT->render_from_template('theme_learnr/settings-customfonts-filelist', $templatecontext);

            // Finish the widget.
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);

        }

        // Create FontAwesome heading.
        $name = 'theme_learnr/fontawesomeheading';
        $title = get_string('fontawesomeheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: FontAwesome version.
        $faversionoption =
                // Don't use string lazy loading (= false) because the string will be directly used and would produce a
                // PHP warning otherwise.
                array(THEME_LEARNR_SETTING_FAVERSION_NONE =>
                        get_string('fontawesomeversionnone', 'theme_learnr', null, false),
                        THEME_LEARNR_SETTING_FAVERSION_FA6FREE =>
                                get_string('fontawesomeversionfa6free', 'theme_learnr', null, false));
        $name = 'theme_learnr/fontawesomeversion';
        $title = get_string('fontawesomeversionsetting', 'theme_learnr', null, true);
        $description = get_string('fontawesomeversionsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_FAVERSION_NONE,
                $faversionoption);
        $setting->set_updatedcallback('theme_learnr_fontawesome_checkin');
        $tab->add($setting);

        // Setting: FontAwesome files.
        $name = 'theme_learnr/fontawesomefiles';
        $title = get_string('fontawesomefilessetting', 'theme_learnr', null, true);
        $description = get_string('fontawesomefilessetting_desc', 'theme_learnr', null, true).'<br /><br />'.
                get_string('fontawesomefilesstructurenote', 'theme_learnr', null, true);
        if ($registerfontsresult == true) {
            // Use our enhanced implementation of admin_setting_configstoredfile to circumvent MDL-59082.
            // This can be changed back to admin_setting_configstoredfile as soon as MDL-59082 is fixed.
            $setting = new admin_setting_configstoredfilealwayscallback($name, $title, $description, 'fontawesome', 0,
                    array('maxfiles' => -1, 'subdirs' => 1, 'accepted_types' => theme_learnr_get_fontawesome_extensions()));
        } else {
            // Use our enhanced implementation of admin_setting_configstoredfile to circumvent MDL-59082.
            // This can be changed back to admin_setting_configstoredfile as soon as MDL-59082 is fixed.
            $setting = new admin_setting_configstoredfilealwayscallback($name, $title, $description, 'fontawesome', 0,
                    array('maxfiles' => -1));
        }
        $setting->set_updatedcallback('theme_learnr_fontawesome_checkin');
        $tab->add($setting);
        $page->hide_if('theme_learnr/fontawesomefiles', 'theme_learnr/fontawesomeversion', 'eq',
                THEME_LEARNR_SETTING_FAVERSION_NONE);

        // Information: FontAwesome list.
        $faconfig = get_config('theme_learnr', 'fontawesomeversion');
        // If there is at least one file uploaded and if a FontAwesome version is enabled (unfortunately, hide_if does not
        // work for admin_setting_description up to now, that's why we have to use this workaround).
        if ($faconfig != THEME_LEARNR_SETTING_FAVERSION_NONE && $faconfig != null &&
                !empty(get_config('theme_learnr', 'fontawesomefiles'))) {
            // Prepare the widget.
            $name = 'theme_learnr/fontawesomelist';
            $title = get_string('fontawesomelistsetting', 'theme_learnr', null, true);
            $description = get_string('fontawesomelistsetting_desc', 'theme_learnr', null, true).'<br /><br />'.
                    get_string('fontawesomelistnote', 'theme_learnr', null, true);

            // Append the file list to the description.
            $templatecontext = array('files' => theme_learnr_get_fontawesome_templatecontext());
            $description .= $OUTPUT->render_from_template('theme_learnr/settings-fontawesome-filelist', $templatecontext);

            // Finish the widget.
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);
        }

        // Information: FontAwesome checks.
        // If there is at least one file uploaded and if a FontAwesome version is enabled (unfortunately, hide_if does not
        // work for admin_setting_description up to now, that's why we have to use this workaround).
        if ($faconfig != THEME_LEARNR_SETTING_FAVERSION_NONE && $faconfig != null &&
                !empty(get_config('theme_learnr', 'fontawesomefiles'))) {
            // Prepare the widget.
            $name = 'theme_learnr/fontawesomechecks';
            $title = get_string('fontawesomecheckssetting', 'theme_learnr', null, true);
            $description = get_string('fontawesomecheckssetting_desc', 'theme_learnr', null, true);

            // Append the checks to the description.
            $templatecontext = array('checks' => theme_learnr_get_fontawesome_checks_templatecontext());
            $description .= $OUTPUT->render_from_template('theme_learnr/settings-fontawesome-checks', $templatecontext);

            // Finish the widget.
            $setting = new admin_setting_description($name, $title, $description);
            $tab->add($setting);
        }

        // Add tab to settings page.
        $page->add($tab);


        // Create H5P tab.
        $tab = new admin_settingpage('theme_learnr_look_h5p',
                get_string('h5ptab', 'theme_learnr', null, true));

        // Create Raw CSS for H5P heading.
        $name = 'theme_learnr/cssh5pheading';
        $title = get_string('cssh5pheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Raw CSS for H5P.
        $name = 'theme_learnr/cssh5p';
        $title = get_string('cssh5psetting', 'theme_learnr', null, true);
        $description = get_string('cssh5psetting_desc', 'theme_learnr', null, true);
        $default = '';
        $setting = new admin_setting_scsscode($name, $title, $description, $default, PARAM_RAW);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Create content bank width heading.
        $name = 'theme_learnr/contentwidthheading';
        $title = get_string('contentwidthheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: H5P content bank max width.
        $name = 'theme_learnr/h5pcontentmaxwidth';
        $title = get_string('h5pcontentmaxwidthsetting', 'theme_learnr', null, true);
        $description = get_string('h5pcontentmaxwidthsetting_desc', 'theme_learnr', null, true);
        $default = '95%';
        $setting = new admin_setting_configtext($name, $title, $description, $default, $widthregex, 6);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Create mobile app tab.
        $tab = new admin_settingpage('theme_learnr_look_mobile',
                get_string('mobiletab', 'theme_learnr', null, true));

        // Create Mobile appearance heading.
        $name = 'theme_learnr/mobileappearanceheading';
        $title = get_string('mobileappearanceheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Additional CSS for Mobile app.
        $name = 'theme_learnr/mobilescss';
        $title = get_string('mobilecss', 'theme_learnr', null, true);
        $description = get_string('mobilecss_desc', 'theme_learnr', null, true);
        $mobilecssurl = new moodle_url('/admin/settings.php', array('section' => 'mobileappearance'));
        // If another Mobile App CSS URL is set already (in the $CFG->mobilecssurl setting), we add a warning to the description.
        if (isset($CFG->mobilecssurl) && !empty($CFG->mobilecssurl) &&
                strpos($CFG->mobilecssurl, '/learnr/mobile/styles.php') == false) {
            $mobilescssnotification = new \core\output\notification(
                    get_string('mobilecss_overwrite', 'theme_learnr',
                            array('url' => $mobilecssurl->out(), 'value' => $CFG->mobilecssurl)).' '.
                    get_string('mobilecss_donotchange', 'theme_learnr'),
                    \core\output\notification::NOTIFY_WARNING);
            $mobilescssnotification->set_show_closebutton(false);
            $description .= $OUTPUT->render($mobilescssnotification);

            // Otherwise, we just add a note to the description.
        } else {
            $mobilescssnotification = new \core\output\notification(
                    get_string('mobilecss_set', 'theme_learnr',
                            array('url' => $mobilecssurl->out())).' '.
                    get_string('mobilecss_donotchange', 'theme_learnr'),
                    \core\output\notification::NOTIFY_INFO);
            $mobilescssnotification->set_show_closebutton(false);
            $description .= $OUTPUT->render($mobilescssnotification);
        }
        // Using admin_setting_scsscode is not 100% right here as this setting does not support SCSS.
        // However, is shouldn't harm if the CSS code is parsed by the setting.
        $setting = new admin_setting_scsscode($name, $title, $description, '', PARAM_RAW);
        $setting->set_updatedcallback('theme_learnr_set_mobilecss_url');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Add settings page to the admin settings category.
        $ADMIN->add('theme_learnr', $page);

        // Create Feel settings page with tabs
        // (and allow users with the theme/learnr:configure capability to access it).
        $page = new theme_boost_admin_settingspage_tabs('theme_learnr_feel',
                get_string('configtitlefeel', 'theme_learnr', null, true),
                'theme/learnr:configure');


        // Create navigation tab.
        $tab = new admin_settingpage('theme_learnr_feel_navigation',
                get_string('navigationtab', 'theme_learnr', null, true));

        // Create primary navigation heading.
        $name = 'theme_learnr/primarynavigationheading';
        $title = get_string('primarynavigationheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Prepare hide nodes options.
        $hidenodesoptions = array(
                THEME_LEARNR_SETTING_HIDENODESPRIMARYNAVIGATION_HOME => get_string('home'),
                THEME_LEARNR_SETTING_HIDENODESPRIMARYNAVIGATION_MYHOME => get_string('myhome'),
                THEME_LEARNR_SETTING_HIDENODESPRIMARYNAVIGATION_MYCOURSES => get_string('mycourses'),
                THEME_LEARNR_SETTING_HIDENODESPRIMARYNAVIGATION_SITEADMIN => get_string('administrationsite')
        );

        // Setting: Hide nodes in primary navigation.
        $name = 'theme_learnr/hidenodesprimarynavigation';
        $title = get_string('hidenodesprimarynavigationsetting', 'theme_learnr', null, true);
        $description = get_string('hidenodesprimarynavigationsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configmulticheckbox($name, $title, $description, array(), $hidenodesoptions);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Create navigation heading.
        $name = 'theme_learnr/navigationheading';
        $title = get_string('navigationheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: back to top button.
        $name = 'theme_learnr/backtotopbutton';
        $title = get_string('backtotopbuttonsetting', 'theme_learnr', null, true);
        $description = get_string('backtotopbuttonsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_YES, $yesnooption);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: scroll-spy.
        $name = 'theme_learnr/scrollspy';
        $title = get_string('scrollspysetting', 'theme_learnr', null, true);
        $description = get_string('scrollspysetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_YES, $yesnooption);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Activity navigation.
        $name = 'theme_learnr/activitynavigation';
        $title = get_string('activitynavigationsetting', 'theme_learnr', null, true);
        $description = get_string('activitynavigationsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_YES, $yesnooption);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Create blocks tab.
        $tab = new admin_settingpage('theme_learnr_feel_blocks', get_string('blockstab', 'theme_learnr', null, true));

        // Create blocks general heading.
        $name = 'theme_learnr/blocksgeneralheading';
        $title = get_string('blocksgeneralheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Replicate the Unaddable blocks setting from theme_boost.
        $name = 'theme_learnr/unaddableblocks';
        $title = get_string('unaddableblocks', 'theme_boost', null, true);
        $description = get_string('unaddableblocks_desc', 'theme_boost', null, true);
        $default = 'navigation,settings,course_list,section_links';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $tab->add($setting);

        // Create block regions heading.
        $name = 'theme_learnr/blockregionsheading';
        $title = get_string('blockregionsheading', 'theme_learnr', null, true);
        $description = get_string('blockregionsheading_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        // Add experimental warning.
        $name = 'theme_learnr/blockregionsheadingexperimental';
        $notification = new \core\output\notification(get_string('blockregionsheading_experimental', 'theme_learnr'),
                \core\output\notification::NOTIFY_WARNING);
        $notification->set_show_closebutton(false);
        $description = $OUTPUT->render($notification);
        $setting = new admin_setting_heading($name, '', $description);
        $tab->add($setting);

        // Settings: Additional block regions for 'x' layout.
        // List of region strings.
        $regionstr = (array) get_strings([
            'region-outside-top',
            'region-outside-left',
            'region-outside-right',
            'region-outside-bottom',
            'region-content-upper',
            'region-content-lower',
            'region-header',
            'region-footer-left',
            'region-footer-right',
            'region-footer-center',
            'region-offcanvas-left',
            'region-offcanvas-right',
            'region-offcanvas-center'
        ], 'theme_learnr');
        // List of all available regions.
        $allavailableregions = array(
            'outside-top' => $regionstr['region-outside-top'],
            'outside-left' => $regionstr['region-outside-left'],
            'outside-right' => $regionstr['region-outside-right'],
            'outside-bottom' => $regionstr['region-outside-bottom'],
            'footer-left' => $regionstr['region-footer-left'],
            'footer-right' => $regionstr['region-footer-right'],
            'footer-center' => $regionstr['region-footer-center'],
            'offcanvas-left' => $regionstr['region-offcanvas-left'],
            'offcanvas-right' => $regionstr['region-offcanvas-right'],
            'offcanvas-center' => $regionstr['region-offcanvas-center'],
            'content-upper' => $regionstr['region-content-upper'],
            'content-lower' => $regionstr['region-content-lower'],
            'header' => $regionstr['region-header']
        );
        // Partial list of regions (used on some layouts).
        $partialregions = [
            'outside-top' => $regionstr['region-outside-top'],
            'outside-bottom' => $regionstr['region-outside-bottom'],
            'footer-left' => $regionstr['region-footer-left'],
            'footer-right' => $regionstr['region-footer-right'],
            'footer-center' => $regionstr['region-footer-center'],
            'offcanvas-left' => $regionstr['region-offcanvas-left'],
            'offcanvas-right' => $regionstr['region-offcanvas-right'],
            'offcanvas-center' => $regionstr['region-offcanvas-center']
        ];
        // Build list of page layouts and map the regions to each page layout.
        $pagelayouts = [
            'standard' => $partialregions,
            'admin' => $partialregions,
            'coursecategory' => $partialregions,
            'incourse' => $partialregions,
            'mypublic' => $partialregions,
            'report' => $partialregions,
            'course' => $allavailableregions,
            'frontpage' => $allavailableregions
        ];
        // For the mydashboard layout, remove the content-* layouts as there are already block regions.
        $pagelayouts['mydashboard'] = array_filter($allavailableregions, function($key) {
            return ($key != 'content-upper' && $key != 'content-lower') ? true : false;
        }, ARRAY_FILTER_USE_KEY);
        // Create admin setting for each page layout.
        foreach ($pagelayouts as $layout => $regions) {
            $name = 'theme_learnr/blockregionsfor'.$layout;
            $title = get_string('blockregionsforlayout', 'theme_learnr', $layout, true);
            $description = get_string('blockregionsforlayout_desc', 'theme_learnr', $layout, true);
            $setting = new admin_setting_configmulticheckbox($name, $title, $description, array(), $regions);
            $tab->add($setting);
        }

        // Create outside regions heading.
        $name = 'theme_learnr/outsideregionsheading';
        $title = get_string('outsideregionsheading', 'theme_learnr', null, true);
        $description = get_string('outsideregionsheading_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        // Setting: Block region width for Outside (left) region.
        $name = 'theme_learnr/blockregionoutsideleftwidth';
        $title = get_string('blockregionoutsideleftwidth', 'theme_learnr', null, true);
        $description = get_string('blockregionoutsideleftwidth_desc', 'theme_learnr', null, true);
        $default = '300px';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Block region width for Outside (right) region.
        $name = 'theme_learnr/blockregionoutsiderightwidth';
        $title = get_string('blockregionoutsiderightwidth', 'theme_learnr', null, true);
        $description = get_string('blockregionoutsiderightwidth_desc', 'theme_learnr', null, true);
        $default = '300px';
        $setting = new admin_setting_configtext($name, $title, $description, $default);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Block region width for Outside (top) region.
        $outsideregionswidthoptions = array(
            // Don't use string lazy loading (= false) because the string will be directly used and would produce a
            // PHP warning otherwise.
                THEME_LEARNR_SETTING_OUTSIDEREGIONSWITH_FULLWIDTH =>
                        get_string('outsideregionswidthfullwidth', 'theme_learnr', null, false),
                THEME_LEARNR_SETTING_OUTSIDEREGIONSWITH_COURSECONTENTWIDTH =>
                        get_string('outsideregionswidthcoursecontentwidth', 'theme_learnr', null, false),
                THEME_LEARNR_SETTING_OUTSIDEREGIONSWITH_HEROWIDTH =>
                        get_string('outsideregionswidthherowidth', 'theme_learnr', null, false));
        $name = 'theme_learnr/blockregionoutsidetopwidth';
        $title = get_string('blockregionoutsidetopwidth', 'theme_learnr', null, true);
        $description = get_string('blockregionoutsidetopwidth_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_LEARNR_SETTING_OUTSIDEREGIONSWITH_FULLWIDTH, $outsideregionswidthoptions);
        $tab->add($setting);

        // Setting: Block region width for Outside (bottom) region.
        $name = 'theme_learnr/blockregionoutsidebottomwidth';
        $title = get_string('blockregionoutsidebottomwidth', 'theme_learnr', null, true);
        $description = get_string('blockregionoutsidebottomwidth_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_LEARNR_SETTING_OUTSIDEREGIONSWITH_FULLWIDTH, $outsideregionswidthoptions);
        $tab->add($setting);

        // Setting: Outside regions horizontal placement.
        $outsideregionsplacementoptions = array(
            // Don't use string lazy loading (= false) because the string will be directly used and would produce a
            // PHP warning otherwise.
                THEME_LEARNR_SETTING_OUTSIDEREGIONSPLACEMENT_NEXTMAINCONTENT =>
                        get_string('outsideregionsplacementnextmaincontent', 'theme_learnr', null, false),
                THEME_LEARNR_SETTING_OUTSIDEREGIONSPLACEMENT_NEARWINDOW =>
                        get_string('outsideregionsplacementnearwindowedges', 'theme_learnr', null, false));
        $name = 'theme_learnr/outsideregionsplacement';
        $title = get_string('outsideregionsplacement', 'theme_learnr', null, true);
        $description = get_string('outsideregionsplacement_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_LEARNR_SETTING_OUTSIDEREGIONSPLACEMENT_NEXTMAINCONTENT, $outsideregionsplacementoptions);
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Create misc tab.
        $tab = new admin_settingpage('theme_learnr_feel_misc', get_string('misctab', 'theme_learnr', null, true));

        // Create JavaScript heading.
        $name = 'theme_learnr/javascriptheading';
        $title = get_string('javascriptheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: JavaScript disabled hint.
        $name = 'theme_learnr/javascriptdisabledhint';
        $title = get_string('javascriptdisabledhint', 'theme_learnr', null, true);
        $description = get_string('javascriptdisabledhint_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_NO, $yesnooption);
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Add settings page to the admin settings category.
        $ADMIN->add('theme_learnr', $page);

        // Create Content settings page with tabs
        // (and allow users with the theme/learnr:configure capability to access it).
        $page = new theme_boost_admin_settingspage_tabs('theme_learnr_content',
                get_string('configtitlecontent', 'theme_learnr', null, true),
                'theme/learnr:configure');

        // Create footer tab.
        $tab = new admin_settingpage('theme_learnr_content_footer', get_string('footertab', 'theme_learnr', null, true));

        // Create footnote heading.
        $name = 'theme_learnr/footnoteheading';
        $title = get_string('footnoteheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Footnote.
        $name = 'theme_learnr/footnote';
        $title = get_string('footnotesetting', 'theme_learnr', null, true);
        $description = get_string('footnotesetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_confightmleditor($name, $title, $description, '');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Create static pages tab.
        $tab = new admin_settingpage('theme_learnr_content_staticpages',
                get_string('staticpagestab', 'theme_learnr', null, true));

        // The static pages to be supported.
        $staticpages = array('imprint', 'contact', 'help', 'maintenance');

        // Iterate over the pages.
        foreach ($staticpages as $staticpage) {

            // Create page heading.
            $name = 'theme_learnr/'.$staticpage.'heading';
            $title = get_string($staticpage.'heading', 'theme_learnr', null, true);
            $setting = new admin_setting_heading($name, $title, null);
            $tab->add($setting);

            // Setting: Enable page.
            $name = 'theme_learnr/enable'.$staticpage;
            $title = get_string('enable'.$staticpage.'setting', 'theme_learnr', null, true);
            $description = '';
            $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_NO,
                    $yesnooption);
            $tab->add($setting);

            // Setting: Page content.
            $name = 'theme_learnr/'.$staticpage.'content';
            $title = get_string($staticpage.'contentsetting', 'theme_learnr', null, true);
            $description = get_string($staticpage.'contentsetting_desc', 'theme_learnr', null, true);
            $setting = new admin_setting_confightmleditor($name, $title, $description, '');
            $tab->add($setting);
            $page->hide_if('theme_learnr/'.$staticpage.'content', 'theme_learnr/enable'.$staticpage, 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Page title.
            $name = 'theme_learnr/'.$staticpage.'pagetitle';
            $title = get_string($staticpage.'pagetitlesetting', 'theme_learnr', null, true);
            $description = get_string($staticpage.'pagetitlesetting_desc', 'theme_learnr', null, true);
            $default = get_string($staticpage.'pagetitledefault', 'theme_learnr', null, true);
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $tab->add($setting);
            $page->hide_if('theme_learnr/'.$staticpage.'pagetitle', 'theme_learnr/enable'.$staticpage, 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Page link position.
            $name = 'theme_learnr/'.$staticpage.'linkposition';
            $title = get_string($staticpage.'linkpositionsetting', 'theme_learnr', null, true);
            $staticpageurl = theme_learnr_get_staticpage_link($staticpage);
            $description = get_string($staticpage.'linkpositionsetting_desc', 'theme_learnr', array('url' => $staticpageurl),
                    true);
            $linkpositionoption =
                    // Don't use string lazy loading (= false) because the string will be directly used and would produce a
                    // PHP warning otherwise.
                    array(THEME_LEARNR_SETTING_STATICPAGELINKPOSITION_NONE =>
                            get_string($staticpage.'linkpositionnone', 'theme_learnr', null, false),
                            THEME_LEARNR_SETTING_STATICPAGELINKPOSITION_FOOTNOTE =>
                                    get_string($staticpage.'linkpositionfootnote', 'theme_learnr', null, false),
                            THEME_LEARNR_SETTING_STATICPAGELINKPOSITION_FOOTER =>
                                    get_string($staticpage.'linkpositionfooter', 'theme_learnr', null, false),
                            THEME_LEARNR_SETTING_STATICPAGELINKPOSITION_BOTH =>
                                    get_string($staticpage.'linkpositionboth', 'theme_learnr', null, false));
            $default = 'none';
            $setting = new admin_setting_configselect($name, $title, $description, $default, $linkpositionoption);
            $tab->add($setting);
            $page->hide_if('theme_learnr/'.$staticpage.'linkposition', 'theme_learnr/enable'.$staticpage, 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);
        }

        // Add tab to settings page.
        $page->add($tab);


        // Create info banner tab.
        $tab = new admin_settingpage('theme_learnr_infobanners_infobanner',
                get_string('infobannertab', 'theme_learnr', null, true));

        // Prepare options for the pages settings.
        $infobannerpages = array(
            // Don't use string lazy loading (= false) because the string will be directly used and would produce a
            // PHP warning otherwise.
                THEME_LEARNR_SETTING_INFOBANNERPAGES_MY => get_string('myhome', 'core', null, false),
                THEME_LEARNR_SETTING_INFOBANNERPAGES_MYCOURSES => get_string('mycourses', 'core', null, false),
                THEME_LEARNR_SETTING_INFOBANNERPAGES_SITEHOME => get_string('sitehome', 'core', null, false),
                THEME_LEARNR_SETTING_INFOBANNERPAGES_COURSE => get_string('course', 'core', null, false),
                THEME_LEARNR_SETTING_INFOBANNERPAGES_LOGIN =>
                        get_string('infobannerpageloginpage', 'theme_learnr', null, false)
        );

        // Prepare options for the bootstrap class settings.
        $infobannerbsclasses = array(
            // Don't use string lazy loading (= false) because the string will be directly used and would produce a
            // PHP warning otherwise.
                'primary' => get_string('bootstrapprimarycolor', 'theme_learnr', null, false),
                'secondary' => get_string('bootstrapsecondarycolor', 'theme_learnr', null, false),
                'success' => get_string('bootstrapsuccesscolor', 'theme_learnr', null, false),
                'danger' => get_string('bootstrapdangercolor', 'theme_learnr', null, false),
                'warning' => get_string('bootstrapwarningcolor', 'theme_learnr', null, false),
                'info' => get_string('bootstrapinfocolor', 'theme_learnr', null, false),
                'light' => get_string('bootstraplightcolor', 'theme_learnr', null, false),
                'dark' => get_string('bootstrapdarkcolor', 'theme_learnr', null, false),
                'none' => get_string('bootstrapnone', 'theme_learnr', null, false)
        );

        // Prepare options for the order settings.
        $infobannerorders = array();
        for ($i = 1; $i <= THEME_LEARNR_SETTING_INFOBANNER_COUNT; $i++) {
            $infobannerorders[$i] = $i;
        }

        // Prepare options for the mode settings.
        $infobannermodes = array(
            // Don't use string lazy loading (= false) because the string will be directly used and would produce a
            // PHP warning otherwise.
                THEME_LEARNR_SETTING_INFOBANNERMODE_PERPETUAL =>
                        get_string('infobannermodeperpetual', 'theme_learnr', null, false),
                THEME_LEARNR_SETTING_INFOBANNERMODE_TIMEBASED =>
                        get_string('infobannermodetimebased', 'theme_learnr', null, false)
        );

        // Create the hardcoded amount of information banners without code duplication.
        for ($i = 1; $i <= THEME_LEARNR_SETTING_INFOBANNER_COUNT; $i++) {

            // Create Infobanner heading.
            $name = 'theme_learnr/infobanner'.$i.'heading';
            $title = get_string('infobannerheading', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_heading($name, $title, null);
            $tab->add($setting);

            // Setting: Infobanner enabled.
            $name = 'theme_learnr/infobanner'.$i.'enabled';
            $title = get_string('infobannerenabledsetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('infobannerenabledsetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_NO,
                    $yesnooption);
            $tab->add($setting);

            // Setting: Infobanner content.
            $name = 'theme_learnr/infobanner'.$i.'content';
            $title = get_string('infobannercontentsetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('infobannercontentsetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_confightmleditor($name, $title, $description, '');
            $tab->add($setting);
            $page->hide_if('theme_learnr/infobanner'.$i.'content', 'theme_learnr/infobanner'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Infobanner pages.
            $name = 'theme_learnr/infobanner'.$i.'pages';
            $title = get_string('infobannerpagessetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('infobannerpagessetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configmultiselect($name, $title, $description,
                    array($infobannerpages[THEME_LEARNR_SETTING_INFOBANNERPAGES_MY]), $infobannerpages);
            $tab->add($setting);
            $page->hide_if('theme_learnr/infobanner'.$i.'pages', 'theme_learnr/infobanner'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Infobanner bootstrap class.
            $name = 'theme_learnr/infobanner'.$i.'bsclass';
            $title = get_string('infobannerbsclasssetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('infobannerbsclasssetting_desc',
                    'theme_learnr',
                    array('no' => $i, 'bootstrapnone' => get_string('bootstrapnone', 'theme_learnr')),
                    true);
            $setting = new admin_setting_configselect($name, $title, $description,
                    'primary', $infobannerbsclasses);
            $tab->add($setting);
            $page->hide_if('theme_learnr/infobanner'.$i.'bsclass', 'theme_learnr/infobanner'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Infobanner order.
            $name = 'theme_learnr/infobanner'.$i.'order';
            $title = get_string('infobannerordersetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('infobannerordersetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configselect($name, $title, $description,
                    $i, $infobannerorders);
            $tab->add($setting);
            $page->hide_if('theme_learnr/infobanner'.$i.'order', 'theme_learnr/infobanner'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Infobanner mode.
            $name = 'theme_learnr/infobanner'.$i.'mode';
            $title = get_string('infobannermodesetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('infobannermodesetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configselect($name, $title, $description,
                    THEME_LEARNR_SETTING_INFOBANNERMODE_PERPETUAL, $infobannermodes);
            $tab->add($setting);
            $page->hide_if('theme_learnr/infobanner'.$i.'mode', 'theme_learnr/infobanner'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Infobanner start time.
            $name = 'theme_learnr/infobanner'.$i.'start';
            $title = get_string('infobannerstartsetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('infobannerstartsetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configdatetime($name, $title, $description, '');
            $tab->add($setting);
            $page->hide_if('theme_learnr/infobanner'.$i.'start', 'theme_learnr/infobanner'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);
            $page->hide_if('theme_learnr/infobanner'.$i.'start', 'theme_learnr/infobanner'.$i.'mode', 'neq',
                    THEME_LEARNR_SETTING_INFOBANNERMODE_TIMEBASED);

            // Setting: Infobanner end time.
            $name = 'theme_learnr/infobanner'.$i.'end';
            $title = get_string('infobannerendsetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('infobannerendsetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configdatetime($name, $title, $description, '');
            $tab->add($setting);
            $page->hide_if('theme_learnr/infobanner'.$i.'end', 'theme_learnr/infobanner'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);
            $page->hide_if('theme_learnr/infobanner'.$i.'end', 'theme_learnr/infobanner'.$i.'mode', 'neq',
                    THEME_LEARNR_SETTING_INFOBANNERMODE_TIMEBASED);

            // Setting: Infobanner dismissible.
            $name = 'theme_learnr/infobanner'.$i.'dismissible';
            $title = get_string('infobannerdismissiblesetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('infobannerdismissiblesetting_desc', 'theme_learnr', array('no' => $i), true);
            // Add Reset button if the info banner is already configured to be dismissible.
            if (get_config('theme_learnr', 'infobanner'.$i.'dismissible') == true) {
                $reseturl = new moodle_url('/theme/learnr/settings_infobanner_resetdismissed.php',
                        array('sesskey' => sesskey(), 'no' => $i));
                $description .= html_writer::empty_tag('br');
                $description .= html_writer::link($reseturl,
                        get_string('infobannerdismissresetbutton', 'theme_learnr', array('no' => $i), true),
                        array('class' => 'btn btn-secondary mt-3', 'role' => 'button'));
            }
            $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_NO,
                    $yesnooption);
            $tab->add($setting);
            $page->hide_if('theme_learnr/infobanner'.$i.'dismissible', 'theme_learnr/infobanner'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);
            $page->hide_if('theme_learnr/infobanner'.$i.'dismissible', 'theme_learnr/infobanner'.$i.'mode', 'neq',
                    THEME_LEARNR_SETTING_INFOBANNERMODE_PERPETUAL);
        }

        // Add tab to settings page.
        $page->add($tab);


        // Create advertisement tiles tab.
        $tab = new admin_settingpage('theme_learnr_tiles',
            get_string('tilestab', 'theme_learnr', null, true));

        // Create advertisement tiles general heading.
        $name = 'theme_learnr/tilesgeneralheading';
        $title = get_string('tilesgeneralheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Position of the advertisement tiles on the frontpage.
        $tilefrontpagepositionoptions = array(
                THEME_LEARNR_SETTING_ADVERTISEMENTTILES_FRONTPAGEPOSITION_BEFORE =>
                        get_string('tilefrontpagepositionsetting_before', 'theme_learnr'),
                THEME_LEARNR_SETTING_ADVERTISEMENTTILES_FRONTPAGEPOSITION_AFTER =>
                        get_string('tilefrontpagepositionsetting_after', 'theme_learnr'));
        $name = 'theme_learnr/tilefrontpageposition';
        $title = get_string('tilefrontpagepositionsetting', 'theme_learnr', null, true);
        $url = new moodle_url('/admin/settings.php', array('section' => 'frontpagesettings'));
        $description = get_string('tilefrontpagepositionsetting_desc', 'theme_learnr', array('url' => $url), true);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_LEARNR_SETTING_ADVERTISEMENTTILES_FRONTPAGEPOSITION_BEFORE, $tilefrontpagepositionoptions);
        $tab->add($setting);


        //Begin DBN Update.
        // Setting: Show Advert Tiles on pages.
        $name = 'theme_learnr/showadvertonpages';
        $title = get_string('showadvertonpages', 'theme_learnr');
        $description = get_string('showadvertonpages_desc', 'theme_learnr');

        $showoption1 = get_string('showadvertonpages-home', 'theme_learnr');
        $showoption2 = get_string('showadvertonpages-dash', 'theme_learnr');
        $showoption3 = get_string('showadvertonpages-mycourses', 'theme_learnr');
        $showoption4 = get_string('showadvertonpages-course', 'theme_learnr');

        $showadvertpagesoptions = array('1'=>$showoption1, '2'=>$showoption2, '3'=>$showoption3, '4'=>$showoption4);
        $setting = new admin_setting_configmulticheckbox($name, $title, $description, array(), $showadvertpagesoptions);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);
        // End DBN Update.

        // Setting: Number of advertisement tile columns per row.
        $tilecolumnsoptions = array();
        for ($i = 1; $i <= THEME_LEARNR_SETTING_ADVERTISEMENTTILES_COLUMN_COUNT; $i++) {
            $tilecolumnsoptions[$i] = $i;
        }
        $name = 'theme_learnr/tilecolumns';
        $title = get_string('tilecolumnssetting', 'theme_learnr', null, true);
        $description = get_string('tilecolumnssetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, 2, $tilecolumnsoptions);
        $tab->add($setting);

        // Setting: Advertisement tiles height.
        $name = 'theme_learnr/tileheight';
        $title = get_string('tileheightsetting', 'theme_learnr', null, true);
        $description = get_string('tileheightsetting_desc', 'theme_learnr', null, true);
        $tileheightoptions = array(
                THEME_LEARNR_SETTING_HEIGHT_100PX => THEME_LEARNR_SETTING_HEIGHT_100PX,
                THEME_LEARNR_SETTING_HEIGHT_150PX => THEME_LEARNR_SETTING_HEIGHT_150PX,
                THEME_LEARNR_SETTING_HEIGHT_200PX => THEME_LEARNR_SETTING_HEIGHT_200PX,
                THEME_LEARNR_SETTING_HEIGHT_250PX => THEME_LEARNR_SETTING_HEIGHT_250PX);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_LEARNR_SETTING_HEIGHT_150PX, $tileheightoptions);
        $tab->add($setting);

        // Prepare options for the order settings.
        $tilesorders = array();
        for ($i = 1; $i <= THEME_LEARNR_SETTING_ADVERTISEMENTTILES_COUNT; $i++) {
            $tilesorders[$i] = $i;
        }

        // Create the hardcoded amount of advertisement tiles without code duplication.
        for ($i = 1; $i <= THEME_LEARNR_SETTING_ADVERTISEMENTTILES_COUNT; $i++) {

            // Create advertisement tile heading.
            $name = 'theme_learnr/tile'.$i.'heading';
            $title = get_string('tileheading', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_heading($name, $title, null);
            $tab->add($setting);

            // Setting: Advertisement tile enabled.
            $name = 'theme_learnr/tile'.$i.'enabled';
            $title = get_string('tileenabledsetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('tileenabledsetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_NO,
                    $yesnooption);
            $tab->add($setting);

            // Setting: Advertisement tile title.
            $name = 'theme_learnr/tile'.$i.'title';
            $title = get_string('tiletitlesetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('tiletitlesetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configtext($name, $title, $description, '');
            $tab->add($setting);
            $page->hide_if('theme_learnr/tile'.$i.'title', 'theme_learnr/tile'.$i.'enabled', 'neq',
                THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Advertisement tile content.
            $name = 'theme_learnr/tile'.$i.'content';
            $title = get_string('tilecontentsetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('tilecontentsetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_confightmleditor($name, $title, $description, '');
            $tab->add($setting);
            $page->hide_if('theme_learnr/tile'.$i.'content', 'theme_learnr/tile'.$i.'enabled', 'neq',
                THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Advertisement tile background image.
            $name = 'theme_learnr/tile'.$i.'backgroundimage';
            $title = get_string('tilebackgroundimagesetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('tilebackgroundimagesetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configstoredfile($name, $title, $description, 'tilebackgroundimage'.$i, 0,
                array('maxfiles' => 1, 'accepted_types' => 'web_image'));
            $setting->set_updatedcallback('theme_reset_all_caches');
            $tab->add($setting);
            $page->hide_if('theme_learnr/tile'.$i.'backgroundimage', 'theme_learnr/tile'.$i.'enabled', 'neq',
                THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Course header image position.
            $name = 'theme_learnr/tile'.$i.'backgroundimageposition';
            $title = get_string('tilebackgroundimagepositionsetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('tilebackgroundimagepositionsetting_desc', 'theme_learnr', array('no' => $i), true);
            $tilebackgroundimagepositionoptions = array(
                    THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_CENTER =>
                            THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_CENTER,
                    THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_TOP =>
                            THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_TOP,
                    THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_BOTTOM =>
                            THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_BOTTOM,
                    THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_TOP =>
                            THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_TOP,
                    THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_CENTER =>
                            THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_CENTER,
                    THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_BOTTOM =>
                            THEME_LEARNR_SETTING_IMAGEPOSITION_LEFT_BOTTOM,
                    THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_TOP =>
                            THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_TOP,
                    THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_CENTER =>
                            THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_CENTER,
                    THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_BOTTOM =>
                            THEME_LEARNR_SETTING_IMAGEPOSITION_RIGHT_BOTTOM);
            $setting = new admin_setting_configselect($name, $title, $description,
                    THEME_LEARNR_SETTING_IMAGEPOSITION_CENTER_CENTER, $tilebackgroundimagepositionoptions);
            $tab->add($setting);
            $page->hide_if('theme_learnr/tile'.$i.'backgroundimageposition', 'theme_learnr/tile'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Advertisement tile link URL.
            $name = 'theme_learnr/tile'.$i.'link';
            $title = get_string('tilelinksetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('tilelinksetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
            $tab->add($setting);
            $page->hide_if('theme_learnr/tile'.$i.'link', 'theme_learnr/tile'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Advertisement tile link title.
            $name = 'theme_learnr/tile'.$i.'linktitle';
            $title = get_string('tilelinktitlesetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('tilelinktitlesetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configtext($name, $title, $description, '');
            $tab->add($setting);
            $page->hide_if('theme_learnr/tile'.$i.'linktitle', 'theme_learnr/tile'.$i.'enabled', 'neq',
                THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Advertisement tile link target.
            $name = 'theme_learnr/tile'.$i.'linktarget';
            $title = get_string('tilelinktargetsetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('tilelinktargetsetting_desc', 'theme_learnr', array('no' => $i), true);
            $tilelinktargetnoptions = array(
                    THEME_LEARNR_SETTING_LINKTARGET_SAMEWINDOW =>
                            get_string('tilelinktargetsetting_samewindow', 'theme_learnr'),
                    THEME_LEARNR_SETTING_LINKTARGET_NEWTAB =>
                            get_string('tilelinktargetsetting_newtab', 'theme_learnr'));
            $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_LINKTARGET_SAMEWINDOW,
                    $tilelinktargetnoptions);
            $tab->add($setting);
            $page->hide_if('theme_learnr/tile'.$i.'linktarget', 'theme_learnr/tile'.$i.'enabled', 'neq',
                    THEME_LEARNR_SETTING_SELECT_YES);

            // Setting: Advertisement tile order position.
            $name = 'theme_learnr/tile'.$i.'order';
            $title = get_string('tileordersetting', 'theme_learnr', array('no' => $i), true);
            $description = get_string('tileordersetting_desc', 'theme_learnr', array('no' => $i), true);
            $setting = new admin_setting_configselect($name, $title, $description, $i, $tilesorders);
            $tab->add($setting);
            $page->hide_if('theme_learnr/tile'.$i.'order', 'theme_learnr/tile'.$i.'enabled', 'neq',
                THEME_LEARNR_SETTING_SELECT_YES);
        }

        // Add tab to settings page.
        $page->add($tab);


        // Add settings page to the admin settings category.
        $ADMIN->add('theme_learnr', $page);

        // Create Functionality settings page with tabs
        // (and allow users with the theme/learnr:configure capability to access it).
        $page = new theme_boost_admin_settingspage_tabs('theme_learnr_functionality',
                get_string('configtitlefunctionality', 'theme_learnr', null, true),
                'theme/learnr:configure');

        // Create courses tab.
        $tab = new admin_settingpage('theme_learnr_functionality_courses',
                get_string('coursestab', 'theme_learnr', null, true));

        // Create course related hints heading.
        $name = 'theme_learnr/courserelatedhintsheading';
        $title = get_string('courserelatedhintsheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Show hint for switched role.
        $name = 'theme_learnr/showswitchedroleincourse';
        $title = get_string('showswitchedroleincoursesetting', 'theme_learnr', null, true);
        $description = get_string('showswitchedroleincoursesetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_YES, $yesnooption);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Show hint in hidden courses.
        $name = 'theme_learnr/showhintcoursehidden';
        $title = get_string('showhintcoursehiddensetting', 'theme_learnr', null, true);
        $description = get_string('showhintcoursehiddensetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_YES, $yesnooption);
        $tab->add($setting);

        // Setting: Show hint guest for access.
        $name = 'theme_learnr/showhintcourseguestaccess';
        $title = get_string('showhintcoursguestaccesssetting', 'theme_learnr', null, true);
        $description = get_string('showhintcourseguestaccesssetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_YES, $yesnooption);
        $tab->add($setting);

        // Setting: Show hint for self enrolment without enrolment key.
        $name = 'theme_learnr/showhintcourseselfenrol';
        $title = get_string('showhintcourseselfenrolsetting', 'theme_learnr', null, true);
        $description = get_string('showhintcourseselfenrolsetting_desc', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description, THEME_LEARNR_SETTING_SELECT_YES, $yesnooption);
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);


        // Add settings page to the admin settings category.
        $ADMIN->add('theme_learnr', $page);


        // Create Flavours settings page as external page
        // (and allow users with the theme/learnr:configure capability to access it).
        $flavourspage = new admin_externalpage('theme_learnr_flavours',
                get_string('configtitleflavours', 'theme_learnr', null, true),
                new moodle_url('/theme/learnr/flavours/overview.php'),
                'theme/learnr:configure');
        $ADMIN->add('theme_learnr', $flavourspage);
    }
}
