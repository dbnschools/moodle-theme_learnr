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
 * Theme LearnR - Language pack
 *
 * @package    theme_learnr
 * @copyright  2022 Dearborn Public Schools, Chris Kenniburg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'LearnR';
$string['choosereadme'] = 'Theme LearnR is an enhanced child theme of Boost provided by Moodle an Hochschulen e.V.';
$string['configtitle'] = 'LearnR';
$string['privacy:metadata'] = 'The LearnR theme does not store any personal data about any user.';
$string['colorsettings'] = 'Color Settings';
$string['contentsettings'] = 'Content Areas';
$string['marketing'] = 'Marketing Tiles';
$string['showcourseindexnav'] = 'Show Course Index Navigation';
$string['showcourseindexnav_desc'] = 'Show or hide the Course Index drawer navigation.';
$string['showpageimage'] = 'Show Course Dashboard Images';
$string['showpageimage_desc'] = 'Use custom images as a background for the Course Dashboard panel.  There is a theme default image and each course can use a custom image that the teacher can upload in course settings. You may also upload a site default image in the setting provided on this page.';
$string['sitewideimage'] = 'Sitewide Course Dashboard Image';
$string['sitewideimage_desc'] = 'This setting forces the theme default page image or the page background image to be used for all course dashboards. Images uploaded into course settings are not used.';
$string['courseiconsize'] = 'Course Icon Size';
$string['courseiconsize_desc'] = 'Set the size of Activity icons on the course home page.  Activity pages default to 50px for page layout constraints.';
$string['marketicon'] = 'Category Icon';
$string['marketicon_desc'] = 'Choose an icon to represent course categories.';
$string['latestcourses'] = 'Recent Courses';
$string['viewallcourses'] = 'View All Courses';
$string['nomycourses'] = 'Sorry, you have no enrollments.';
$string['activitynavdisplay'] = 'Activity Navigation Display';
$string['activitynavdisplay_desc'] = 'By default, if you display the Course Index the Activity Navigation menu will be hidden.  This toggle will allow you to control when the Acvitity Navigation menu is displayed and where.';
$string['actnav_top_on'] = 'Turn top location on';
$string['actnav_bottom_on'] = 'Turn bottom location on';
$string['actnav_all_on'] = 'Turn all on';
$string['actnav_all_off'] = 'Turn all off';
$string['region-columna'] = 'Column A';
$string['region-columnb'] = 'Column B';
$string['region-columnc'] = 'Column C';
$string['region-footera'] = 'Footer A';
$string['region-footerb'] = 'Footer B';
$string['region-footerc'] = 'Footer C';
$string['showblockdrawer'] = 'Show Block Drawer';
$string['showblockdrawer_desc'] = 'Show or hide the block drawer on the left side of the page. Hiding this activates the Learn Course Dashboard which is a 3 column ';
$string['coursedashbutton'] = 'Course Dashboard';
$string['closecoursedashboard'] = 'Close';
$string['showcoursedashboard'] = 'Show Course Course Dashboard';
$string['showcoursedashboard_desc'] = 'The Course Dashboard is a three column collapsible panel that displays course blocks at the top of the page.  It is highly recommended that you use this when hiding the block drawer.';
$string['backgroundimage'] = 'Course Dashboard Sitewide Image';
$string['backgroundimage_desc'] = 'This image will be used as the default Course Dashboard image throughout the side.  It can be set as the only image using the checkbox on this page.';
$string['sectionstyle'] = 'Course Section Style Chooser';
$string['sectionstyle_desc'] = 'Choose a style for course sections.';
$string['sections-boost'] = 'Boost Default';
$string['sections-learnr'] = 'LearnR Default Style';
$string['sections-boxed'] = 'Boxed Style';
$string['sections-bars'] = 'Solid Section Bars Style';
$string['fullwidthpage'] = 'Full Width Page Display';
$string['fullwidthpage_desc'] = 'Make the pages full width for consistency throughout the course.  Turning this off reverts back to standard Boost narrow page widths.';


// Color Settings
$string['successcolor'] = 'Success color';
$string['infocolor'] = 'Info Color';
$string['warningcolor'] = 'Warning Color';
$string['dangercolor'] = 'Danger Color';
$string['secondarycolor'] = 'Secondary Color';
$string['rootcolor_desc'] = 'These colors are used by Bootstrap and Moodle for various elements of a page.  These are the defaults.';
$string['iconadministrationcolor'] = 'Icon Set: Administration';
$string['iconassessmentcolor'] = 'Icon Set: Assessment';
$string['iconcollaborationcolor'] = 'Icon Set: Collaboration';
$string['iconcommunicationcolor'] = 'Icon Set: Communication';
$string['iconcontentcolor'] = 'Icon Set: Content';
$string['iconinterfacecolor'] = 'Icon Set: Interface';
$string['iconrootcolor_desc'] = 'These colors are used for activity icon sets on the course page.  These are the defaults.';
$string['drawerbg'] = 'Drawer background color';
$string['drawerbg_desc'] = 'Change the color of the drawer background.';
$string['bodybg'] = 'Body background color';
$string['bodybg_desc'] = 'Change the color of the body background.';
$string['navbarbg'] = 'Navbar background color';
$string['navbarbg_desc'] = 'Change the color of the navbar background.';
$string['primarynavbarlink'] = 'Primary Navbar link color';
$string['secondarynavbarlink'] = 'Secondary Navbar link color';
$string['navbarlink_desc'] = 'Change the color of the navbar links.';

// Content
$string['alert'] = 'Homepage Alert';
$string['alert_desc'] = 'This is a special alert message that will appear on the homepage.';
$string['fptextbox'] = 'Homepage Textbox Authenticated User';
$string['fptextbox_desc'] = 'This textbox appears on the homepage once a user authenticates. It is ideal for putting a welcome message and providing instructions for the learner.';

//Marketing Tiles
$string['marketingheading'] = 'Marketing Tiles';
$string['marketinginfodesc'] = 'Enter the settings for your marketing spot.  You must include a title in order for the Marketing Spot to appear.  The title will activate the individual Marketing Spots.';
$string['marketingheadingsub'] = 'Three locations on the front page to add information and links';
$string['marketboxcolor'] = 'Marketing Box Background Color';
$string['marketboxcolor_desc'] = 'The color of the background for the marketing box.';
$string['marketboxbuttoncolor'] = 'Marketing Box Button Color';
$string['marketboxbuttoncolor_desc'] = 'The color of the button background for the marketing box.';
$string['marketboxcontentcolor'] = 'Marketing Box Content Background Color';
$string['marketboxcontentcolor_desc'] = 'The color of the background for the marketing box content. This is where the text appears in the marketing spot and can be different from the box background color to draw attention to the text.';
$string['marketingheight'] = 'Height of Marketing Images';
$string['marketingheightdesc'] = 'If you want to display images in the Marketing boxes you can specify their hight here.';
$string['marketingdesc'] = 'This theme provides the option of enabling three "marketing" or "ad" spots just under the slideshow.  These allow you to easily identify core information to your users and provide direct links.';
$string['marketing1'] = 'Marketing Spot One';
$string['marketing2'] = 'Marketing Spot Two';
$string['marketing3'] = 'Marketing Spot Three';
$string['marketingtitle'] = 'Title';
$string['marketingtitledesc'] = 'Title to show in this marketing spot.  You must include a title in order for the Marketing Tile to appear.';
$string['marketingicon'] = 'Link Icon';
$string['marketingicondesc'] = 'Name of the icon you wish to use in the marketing URL Button. List is <a href="https://fontawesome.com/v4.7.0/icons/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';
$string['marketingimage'] = 'Image';
$string['marketingimage_desc'] = 'This provides the option of displaying an image in the marketing spot';
$string['marketingcontent'] = 'Content';
$string['marketingcontentdesc'] = 'Content to display in the marketing box. Keep it short and sweet.';
$string['marketingbuttontext'] = 'Link Text';
$string['marketingbuttontextdesc'] = 'Text to appear on the button.';
$string['marketingbuttonurl'] = 'Link URL';
$string['marketingbuttonurldesc'] = 'URL the button will point to.';
$string['marketingurltarget'] = 'Link Target';
$string['marketingurltargetdesc'] = 'Choose how the link should be opened';
$string['marketingurltargetself'] = 'Current Page';
$string['marketingurltargetnew'] = 'New Page';
$string['marketingurltargetparent'] = 'Parent Frame';
$string['togglemarketing'] = 'Marketing Tile Position';
$string['togglemarketing_desc'] = 'Determine where the marketing tiles will be located on the homepage.';
$string['displaytop'] = 'Display at Top of Page';
$string['displaybottom'] = 'Display at Bottom of Page';
$string['markettextbg'] = 'Marketing Tile Text Background';
$string['markettextbg_desc'] = 'Background colour for the text area of the marketing tiles.';

