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
 * Theme Boost Union Child - Settings file
 *
 * @package    theme_learnr
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig || has_capability('theme/boost_union:configure', context_system::instance())) {

    // How this file works:
    // Boost Union's settings are divided into multiple settings pages which resides in its own settings category.
    // You will understand it as soon as you look at /theme/boost_union/settings.php.
    // This settings file here is built in a way that it adds another settings page to this existing settings
    // category. You can add all child-theme-specific settings to this settings page here.

    // Avoid that the theme settings page is auto-created.
    $settings = null;

    // Create empty settings page structure to make the site administration work on non-admin pages.
    if (!$ADMIN->fulltree) {
        // Create Boost Union Child settings page
        // (and allow users with the theme/boost_union:configure capability to access it).
        $tab = new admin_settingpage('theme_learnr',
            get_string('configtitle', 'theme_learnr', null, true),
            'theme/boost_union:configure');
        $ADMIN->add('theme_boost_union', $tab);
    }

    // Create full settings page structure.
    // @codingStandardsIgnoreLine
    else if ($ADMIN->fulltree) {

        // Require the necessary libraries.
        require_once($CFG->dirroot . '/theme/boost_union/lib.php');
        require_once($CFG->dirroot . '/theme/boost_union/locallib.php');
        require_once($CFG->dirroot . '/theme/learnr/lib.php');
        require_once($CFG->dirroot . '/theme/learnr/locallib.php');

        // Prepare options array for select settings.
        // Due to MDL-58376, we will use binary select settings instead of checkbox settings throughout this theme.
        $yesnooption = [THEME_BOOST_UNION_SETTING_SELECT_YES => get_string('yes'),
            THEME_BOOST_UNION_SETTING_SELECT_NO => get_string('no'), ];


        // Create Boost Union Child settings page with tabs
        // (and allow users with the theme/boost_union:configure capability to access it).
        $page = new theme_boost_admin_settingspage_tabs('theme_learnr',
            get_string('configtitle', 'theme_learnr', null, true),
            'theme/boost_union:configure');


        // Create general settings tab.
        $tab = new admin_settingpage('theme_learnr_general',
            get_string('generalinfo', 'theme_learnr', null, true));

        // This is the descriptor for the page.
        $name = 'theme_learnr/learnrinformation';
        $heading = get_string('learnrinfo', 'theme_learnr');
        $information = get_string('learnrinfo_desc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);

        // This is the descriptor for the page.
        $name = 'theme_learnr/learnrsetup';
        $heading = get_string('learnrsetup', 'theme_learnr');
        $information = get_string('learnrsetup_desc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);

        

        // Add tab to settings page.
        $page->add($tab);


        /******************************************************
         * YOUR SETTINGS START HERE.
         *****************************************************/

        // Create example tab.
        $tab = new admin_settingpage('theme_learnr_features',
                get_string('featurestab', 'theme_learnr', null, true));

        // Create example heading.
        $name = 'theme_learnr/featuresheading';
        $title = get_string('featuresheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
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

        // Create static pages tab.
        $tab = new admin_settingpage('theme_learnr_content_stylingtab',
        get_string('stylingtab', 'theme_learnr', null, true));

        // This is the descriptor for the page.
        $name = 'theme_learnr/stylinginfo';
        $heading = get_string('stylinginfo', 'theme_learnr');
        $information = get_string('stylinginfo_desc', 'theme_learnr');
        $setting = new admin_setting_heading($name, $heading, $information);
        $tab->add($setting);

        //Begin DBN Update
        // Sections Display Options.
        $name = 'theme_learnr/layoutstyle';
        $title = get_string('layoutstyle' , 'theme_learnr');
        $description = get_string('sectionstyle_desc', 'theme_learnr');
        $option1 = get_string('layoutstyle-learnr', 'theme_learnr');
        //$option2 = get_string('layoutstyle-boxed', 'theme_learnr');
        $option3 = get_string('layoutstyle-boost', 'theme_learnr');
        //$option4 = get_string('layoutstyle-bars', 'theme_learnr');
        $default = '1';
        $choices = array('1'=>$option1, '3'=>$option3);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);


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

        // secondary Navigation Display Options.
        $name = 'theme_learnr/secondarymenuposition';
        $title = get_string('secondarymenuposition' , 'theme_learnr');
        $description = get_string('secondarymenuposition_desc', 'theme_learnr');
        $option1 = get_string('secondarymenuposition_below', 'theme_learnr');
        $option2 = get_string('secondarymenuposition_above', 'theme_learnr');
        $default = '1';
        $choices = array('1'=>$option1, '2'=>$option2);
        $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

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

        // Create inheritance heading.
        $name = 'theme_learnr/inheritanceheading';
        $title = get_string('inheritanceheading', 'theme_learnr', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Prepare inheritance options.
        $inheritanceoptions = [
                THEME_LEARNR_SETTING_INHERITANCE_INHERIT =>
                        get_string('inheritanceinherit', 'theme_learnr'),
                THEME_LEARNR_SETTING_INHERITANCE_DUPLICATE =>
                        get_string('inheritanceduplicate', 'theme_learnr'),
        ];

        // Setting: Pre SCSS inheritance setting.
        $name = 'theme_learnr/prescssinheritance';
        $title = get_string('prescssinheritancesetting', 'theme_learnr', null, true);
        $description = get_string('prescssinheritancesetting_desc', 'theme_learnr', null, true).'<br />'.
                get_string('inheritanceoptionsexplanation', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_LEARNR_SETTING_INHERITANCE_INHERIT, $inheritanceoptions);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Extra SCSS inheritance setting.
        $name = 'theme_learnr/extrascssinheritance';
        $title = get_string('extrascssinheritancesetting', 'theme_learnr', null, true);
        $description = get_string('extrascssinheritancesetting_desc', 'theme_learnr', null, true).'<br />'.
                get_string('inheritanceoptionsexplanation', 'theme_learnr', null, true);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_LEARNR_SETTING_INHERITANCE_INHERIT, $inheritanceoptions);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);



        $page->add($tab);
        // End DBN Update.





        /******************************************************
         * YOUR SETTINGS END HERE.
         *****************************************************/


        // Add settings page to the admin settings category.
        $ADMIN->add('theme_boost_union', $page);
    }
}
