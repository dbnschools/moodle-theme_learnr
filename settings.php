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
 * @copyright  2022 Dearborn Public Schools, Chris Kenniburg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings = new theme_boost_admin_settingspage_tabs('themesettinglearnr', get_string('configtitle', 'theme_learnr'));
    $page = new admin_settingpage('theme_learnr_general', get_string('generalsettings', 'theme_boost'));

    // Unaddable blocks.
    // Blocks to be excluded when this theme is enabled in the "Add a block" list: Administration, Navigation, Courses and
    // Section links.
    $default = 'navigation,settings,course_list,section_links';
    $setting = new admin_setting_configtext('theme_learnr/unaddableblocks',
            get_string('unaddableblocks', 'theme_boost'), get_string('unaddableblocks_desc', 'theme_boost'), $default, PARAM_TEXT);
    $page->add($setting);

    // Preset.
    $name = 'theme_learnr/preset';
    $title = get_string('preset', 'theme_boost');
    $description = get_string('preset_desc', 'theme_boost');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_learnr', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';

    $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'learnr');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_learnr/presetfiles';
    $title = get_string('presetfiles', 'theme_boost');
    $description = get_string('presetfiles_desc', 'theme_boost');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
            array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

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
    $page->add($setting);

    $name = 'theme_learnr/fullwidthpage';
    $title = get_string('fullwidthpage', 'theme_learnr');
    $description = get_string('fullwidthpage_desc', 'theme_learnr');
    $default = '1';
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Show/hide course index navigation.
    $name = 'theme_learnr/showcourseindexnav';
    $title = get_string('showcourseindexnav', 'theme_learnr');
    $description = get_string('showcourseindexnav_desc', 'theme_learnr');
    $default = '1';
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Show/hide course index navigation.
    $name = 'theme_learnr/showblockdrawer';
    $title = get_string('showblockdrawer', 'theme_learnr');
    $description = get_string('showblockdrawer_desc', 'theme_learnr');
    $default = '1';
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Block Display Options.
    $name = 'theme_learnr/activitynavdisplay';
    $title = get_string('activitynavdisplay' , 'theme_learnr');
    $description = get_string('activitynavdisplay_desc', 'theme_learnr');
    //$option1 = get_string('blockdisplay_on', 'theme_learnr');
    $option1 = get_string('actnav_top_on', 'theme_learnr');
    $option2 = get_string('actnav_bottom_on', 'theme_learnr');
    $option3 = get_string('actnav_all_on', 'theme_learnr');
    $option4 = get_string('actnav_all_off', 'theme_learnr');
    $default = '1';
    $choices = array('1'=>$option1, '2'=>$option2, '3'=>$option3, '4'=>$option4);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    //Activity icon size for course page.
    $name = 'theme_learnr/courseiconsize';
    $title = get_string('courseiconsize', 'theme_learnr');
    $description = get_string('courseiconsize_desc', 'theme_learnr');;
    $default = '50px';
    $choices = array(
            '26px' => '26px',
            '28px' => '28px',
            '30px' => '30px',
            '32px' => '32px',
            '34px' => '34px',
            '36px' => '36px',
            '38px' => '38px',
            '40px' => '40px',
            '42px' => '42px',
            '44px' => '44px',
            '46px' => '46px',
            '48px' => '48px',
            '50px' => '50px',
            '52px' => '52px',
            '54px' => '54px',
            '56px' => '56px',
            '58px' => '58px',
            '60px' => '60px',
        );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Show/hide course index navigation.
    $name = 'theme_learnr/showcoursedashboard';
    $title = get_string('showcoursedashboard', 'theme_learnr');
    $description = get_string('showcoursedashboard_desc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    
    $page->add($setting);

    // Show/hide page image.
    $name = 'theme_learnr/showpageimage';
    $title = get_string('showpageimage', 'theme_learnr');
    $description = get_string('showpageimage_desc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    
    $page->add($setting);

    // Show sitewide image.
    $name = 'theme_learnr/sitewideimage';
    $title = get_string('sitewideimage', 'theme_learnr');
    $description = get_string('sitewideimage_desc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    
    $page->add($setting);

    // Background image setting.
    $name = 'theme_learnr/pagebackgroundimage';
    $title = get_string('backgroundimage', 'theme_learnr');
    $description = get_string('backgroundimage_desc', 'theme_learnr');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'pagebackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login Background image setting.
    $name = 'theme_learnr/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_boost');
    $description = get_string('loginbackgroundimage_desc', 'theme_boost');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_learnr_colors', get_string('colorsettings', 'theme_learnr'));

    // Variable $body-color.
    // We use an empty default value because the default colour should come from the preset .
    $name = 'theme_learnr/navbarbg';
    $title = get_string('navbarbg', 'theme_learnr');
    $description = get_string('navbarbg_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/primarynavbarlink';
    $title = get_string('primarynavbarlink', 'theme_learnr');
    $description = get_string('navbarlink_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/secondarynavbarlink';
    $title = get_string('secondarynavbarlink', 'theme_learnr');
    $description = get_string('navbarlink_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/drawerbg';
    $title = get_string('drawerbg', 'theme_learnr');
    $description = get_string('drawerbg_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/bodybg';
    $title = get_string('bodybg', 'theme_learnr');
    $description = get_string('bodybg_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);


    $name = 'theme_learnr/brandcolor';
    $title = get_string('brandcolor', 'theme_boost');
    $description = get_string('brandcolor_desc', 'theme_boost');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/successcolor';
    $title = get_string('successcolor', 'theme_learnr');
    $description = get_string('rootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/infocolor';
    $title = get_string('infocolor', 'theme_learnr');
    $description = get_string('rootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/warningcolor';
    $title = get_string('warningcolor', 'theme_learnr');
    $description = get_string('rootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/dangercolor';
    $title = get_string('dangercolor', 'theme_learnr');
    $description = get_string('rootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/secondarycolor';
    $title = get_string('secondarycolor', 'theme_learnr');
    $description = get_string('rootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/iconadministrationcolor';
    $title = get_string('iconadministrationcolor', 'theme_learnr');
    $description = get_string('iconrootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/iconassessmentcolor';
    $title = get_string('iconassessmentcolor', 'theme_learnr');
    $description = get_string('iconrootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/iconcollaborationcolor';
    $title = get_string('iconcollaborationcolor', 'theme_learnr');
    $description = get_string('iconrootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/iconcommunicationcolor';
    $title = get_string('iconcommunicationcolor', 'theme_learnr');
    $description = get_string('iconrootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/iconcontentcolor';
    $title = get_string('iconcontentcolor', 'theme_learnr');
    $description = get_string('iconrootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/iconinterfacecolor';
    $title = get_string('iconinterfacecolor', 'theme_learnr');
    $description = get_string('iconrootcolor_desc', 'theme_learnr');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);


    // Advanced settings.
    $page = new admin_settingpage('theme_learnr_advanced', get_string('advancedsettings', 'theme_boost'));
    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_learnr/scsspre',
            get_string('rawscsspre', 'theme_boost'), get_string('rawscsspre_desc', 'theme_boost'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_learnr/scss', get_string('rawscss', 'theme_boost'),
            get_string('rawscss_desc', 'theme_boost'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_learnr_content', get_string('contentsettings', 'theme_learnr'));

    // Alert setting.
    $name = 'theme_learnr/alertbox';
    $title = get_string('alert', 'theme_learnr');
    $description = get_string('alert_desc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    
    $page->add($setting);

    // Frontpage Textbox.
    $name = 'theme_learnr/fptextbox';
    $title = get_string('fptextbox', 'theme_learnr');
    $description = get_string('fptextbox_desc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    
    $page->add($setting);

    $settings->add($page);


    // Advanced settings.
    $page = new admin_settingpage('theme_learnr_marketing', get_string('marketing', 'theme_learnr'));

    // This is the descriptor for Marketing Spot One
    $name = 'theme_learnr/marketing1info';
    $heading = get_string('marketing1', 'theme_learnr');
    $information = get_string('marketinginfodesc', 'theme_learnr');
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);

    // Marketing Spot One
    $name = 'theme_learnr/marketing1';
    $title = get_string('marketingtitle', 'theme_learnr');
    $description = get_string('marketingtitledesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Background image setting.
    $name = 'theme_learnr/marketing1image';
    $title = get_string('marketingimage', 'theme_learnr');
    $description = get_string('marketingimage_desc', 'theme_learnr');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing1image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing1content';
    $title = get_string('marketingcontent', 'theme_learnr');
    $description = get_string('marketingcontentdesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing1buttontext';
    $title = get_string('marketingbuttontext', 'theme_learnr');
    $description = get_string('marketingbuttontextdesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing1buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_learnr');
    $description = get_string('marketingbuttonurldesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing1target';
    $title = get_string('marketingurltarget' , 'theme_learnr');
    $description = get_string('marketingurltargetdesc', 'theme_learnr');
    $target1 = get_string('marketingurltargetself', 'theme_learnr');
    $target2 = get_string('marketingurltargetnew', 'theme_learnr');
    $target3 = get_string('marketingurltargetparent', 'theme_learnr');
    $default = 'target1';
    $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing1icon';
    $title = get_string('marketicon','theme_learnr');
    $description = get_string('marketicon_desc', 'theme_learnr');
    $default = 'folder';
    $choices = array(
        'clone' => 'Clone',
        'bookmark' => 'Bookmark',
        'book' => 'Book',
        'certificate' => 'Certificate',
        'desktop' => 'Desktop',
        'graduation-cap' => 'Graduation Cap',
        'users' => 'Users',
        'bars' => 'Bars',
        'paper-plane' => 'Paper Plane',
        'plus-circle' => 'Plus Circle',
        'Sitemap' => 'Sitemap',
        'puzzle-piece' => 'Puzzle Piece',
        'spinner' => 'Spinner',
        'circle-o-notch' => 'Circle O Notch',
        'check-square-o' => 'Check Square O',
        'plus-square-o' => 'Plus Square O',
        'chevron-circle-right' => 'Chevron Circle Right',
        'arrow-circle-right' => 'Arrow Circle Right',
        'carrot-down' => 'Caret Down',
        'forward' => 'Forward',
        'file-text' => 'File Text',
        'align-right' => 'Align Right',
        'angle-double-right' => 'Angle Double Right',
        'folder-open' => 'Folder Open',
        'folder' => 'Folder',
        'folder-open-o' => 'Folder Open O',
        'chevron-right' => 'Chevron Right',
        'star' => 'Star',
        'user-circle' => 'User Circle',
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // This is the descriptor for Marketing Spot Two
    $name = 'theme_learnr/marketing2info';
    $heading = get_string('marketing2', 'theme_learnr');
    $information = get_string('marketinginfodesc', 'theme_learnr');
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);

    // Marketing Spot Two.
    $name = 'theme_learnr/marketing2';
    $title = get_string('marketingtitle', 'theme_learnr');
    $description = get_string('marketingtitledesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Background image setting.
    $name = 'theme_learnr/marketing2image';
    $title = get_string('marketingimage', 'theme_learnr');
    $description = get_string('marketingimage_desc', 'theme_learnr');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing2image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing2content';
    $title = get_string('marketingcontent', 'theme_learnr');
    $description = get_string('marketingcontentdesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing2buttontext';
    $title = get_string('marketingbuttontext', 'theme_learnr');
    $description = get_string('marketingbuttontextdesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing2buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_learnr');
    $description = get_string('marketingbuttonurldesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing2target';
    $title = get_string('marketingurltarget' , 'theme_learnr');
    $description = get_string('marketingurltargetdesc', 'theme_learnr');
    $target1 = get_string('marketingurltargetself', 'theme_learnr');
    $target2 = get_string('marketingurltargetnew', 'theme_learnr');
    $target3 = get_string('marketingurltargetparent', 'theme_learnr');
    $default = 'target1';
    $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing2icon';
    $title = get_string('marketicon','theme_learnr');
    $description = get_string('marketicon_desc', 'theme_learnr');
    $default = 'folder';
    $choices = array(
        'clone' => 'Clone',
        'bookmark' => 'Bookmark',
        'book' => 'Book',
        'certificate' => 'Certificate',
        'desktop' => 'Desktop',
        'graduation-cap' => 'Graduation Cap',
        'users' => 'Users',
        'bars' => 'Bars',
        'paper-plane' => 'Paper Plane',
        'plus-circle' => 'Plus Circle',
        'Sitemap' => 'Sitemap',
        'puzzle-piece' => 'Puzzle Piece',
        'spinner' => 'Spinner',
        'circle-o-notch' => 'Circle O Notch',
        'check-square-o' => 'Check Square O',
        'plus-square-o' => 'Plus Square O',
        'chevron-circle-right' => 'Chevron Circle Right',
        'arrow-circle-right' => 'Arrow Circle Right',
        'carrot-down' => 'Caret Down',
        'forward' => 'Forward',
        'file-text' => 'File Text',
        'align-right' => 'Align Right',
        'angle-double-right' => 'Angle Double Right',
        'folder-open' => 'Folder Open',
        'folder' => 'Folder',
        'folder-open-o' => 'Folder Open O',
        'chevron-right' => 'Chevron Right',
        'star' => 'Star',
        'user-circle' => 'User Circle',
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // This is the descriptor for Marketing Spot Three
    $name = 'theme_learnr/marketing3info';
    $heading = get_string('marketing3', 'theme_learnr');
    $information = get_string('marketinginfodesc', 'theme_learnr');
    $setting = new admin_setting_heading($name, $heading, $information);
    $page->add($setting);

    // Marketing Spot Three.
    $name = 'theme_learnr/marketing3';
    $title = get_string('marketingtitle', 'theme_learnr');
    $description = get_string('marketingtitledesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Background image setting.
    $name = 'theme_learnr/marketing3image';
    $title = get_string('marketingimage', 'theme_learnr');
    $description = get_string('marketingimage_desc', 'theme_learnr');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'marketing3image');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing3content';
    $title = get_string('marketingcontent', 'theme_learnr');
    $description = get_string('marketingcontentdesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing3buttontext';
    $title = get_string('marketingbuttontext', 'theme_learnr');
    $description = get_string('marketingbuttontextdesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing3buttonurl';
    $title = get_string('marketingbuttonurl', 'theme_learnr');
    $description = get_string('marketingbuttonurldesc', 'theme_learnr');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing3target';
    $title = get_string('marketingurltarget' , 'theme_learnr');
    $description = get_string('marketingurltargetdesc', 'theme_learnr');
    $target1 = get_string('marketingurltargetself', 'theme_learnr');
    $target2 = get_string('marketingurltargetnew', 'theme_learnr');
    $target3 = get_string('marketingurltargetparent', 'theme_learnr');
    $default = 'target1';
    $choices = array('_self'=>$target1, '_blank'=>$target2, '_parent'=>$target3);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $name = 'theme_learnr/marketing3icon';
    $title = get_string('marketicon','theme_learnr');
    $description = get_string('marketicon_desc', 'theme_learnr');
    $default = 'folder';
    $choices = array(
        'clone' => 'Clone',
        'bookmark' => 'Bookmark',
        'book' => 'Book',
        'certificate' => 'Certificate',
        'desktop' => 'Desktop',
        'graduation-cap' => 'Graduation Cap',
        'users' => 'Users',
        'bars' => 'Bars',
        'paper-plane' => 'Paper Plane',
        'plus-circle' => 'Plus Circle',
        'Sitemap' => 'Sitemap',
        'puzzle-piece' => 'Puzzle Piece',
        'spinner' => 'Spinner',
        'circle-o-notch' => 'Circle O Notch',
        'check-square-o' => 'Check Square O',
        'plus-square-o' => 'Plus Square O',
        'chevron-circle-right' => 'Chevron Circle Right',
        'arrow-circle-right' => 'Arrow Circle Right',
        'carrot-down' => 'Caret Down',
        'forward' => 'Forward',
        'file-text' => 'File Text',
        'align-right' => 'Align Right',
        'angle-double-right' => 'Angle Double Right',
        'folder-open' => 'Folder Open',
        'folder' => 'Folder',
        'folder-open-o' => 'Folder Open O',
        'chevron-right' => 'Chevron Right',
        'star' => 'Star',
        'user-circle' => 'User Circle',
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);


}
