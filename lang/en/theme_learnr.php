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
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// General.
$string['pluginname'] = 'LearnR';
$string['choosereadme'] = 'This plugin is just a boilerplate template one can use to develop Boost Union child themes.';
$string['configtitle'] = 'LearnR';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Inheritance';
$string['inheritanceinherit'] = 'Inherit';
$string['inheritanceduplicate'] = 'Duplicate';
$string['inheritanceoptionsexplanation'] = 'Most of the time, inheriting will be perfectly fine. However, it may happen that imperfect code is integrated into Boost Union which prevents simple SCSS inheritance for particular Boost Union features. If you encounter any issues with Boost Union features which seem not to work in LearnR as well, try to switch this setting to \'Dupliate\' and, if this solves the problem, report an issue on Github (see the README.md file for details how to report an issue).';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre SCSS inheritance';
$string['prescssinheritancesetting_desc'] = 'With this setting, you control if the pre SCSS code from Boost Union should be inherited or duplicated.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra SCSS inheritance';
$string['extrascssinheritancesetting_desc'] = 'With this setting, you control if the extra SCSS code from Boost Union should be inherited or duplicated.';


/******************************************************
 * YOUR SETTINGS START HERE.
 *****************************************************/

// Settings: Example tab.
$string['exampletab'] = 'Example tab';
// ... Section: Example.
$string['exampleheading'] = 'Example heading';
// ... ... Setting: Example select setting.
$string['exampleselectsetting'] = 'Example select';
$string['exampleselectsetting_desc'] = 'With this setting, you see an example how to build a yes / no setting in a Boost Union child theme.';

/******************************************************
 * YOUR SETTINGS END HERE.
 *****************************************************/


// Privacy API.
$string['privacy:metadata'] = 'The LearnR theme does not store any personal data about any user.';


// Begin DBN Update.
$string['generalinfo'] = 'LearnR Information';
$string['learnrinfo'] = 'Thank you for using LearnR';
$string['learnrinfo_desc'] = "LearnR started as a fork of the wonderful Boost Union theme.  Recent updates presented us the opportunity to move all of our features and enhancements into a child theme of Boost Union.  So, moving forward we plan to develop enhancements for the Boost Union theme as an add-on/child theme of Boost Union.  We are glad you are giving LearnR a try and we hope you enjoy the features!  This wouldn't be possible without the hard work of the Boost Union development community and team.  
<br> LearnR, Fordson, Pioneer, Rebel, and evolve-D are all themes that we developed due to a need to customize the user experience for our K-12 teachers and students within Moodle.  In our themes we try to implement small UX changes that enhance the overall experience. LearnR continues this journey with the wonderful work of Boost Union.";

$string['learnrsetup'] = 'Initial Setup Configuration';
$string['learnrsetup_desc'] = "LearnR is a child theme of Boost Union and will inherit many of the settings from the parent theme.  In this section I will highlight several parent settings in Boost Union that are recommended.
<br>
<h3>Boost Union Look Section</h3>
<ol>
  <li>Course content max width and Medium content max width set to 95%.</li>
  <li>Navbar color set to Dark navbar with light font color.</li>
  <li>Display the course image in the course header set to Yes.</li>
  <li>Course header image layout set to Course title stacked on course image (white font color for dark background images).</li>
</ol>
<h3>Boost Union Feel Section</h3>
<ol>
  <li>Back to top button set to Yes.</li>
  <li>Scroll-spy set to Yes.</li>
  <li>Activity navigation elements set to Yes.</li>
</ol>
";



// Styling tab.
$string['stylingtab'] = 'LearnR Styling Options';
$string['stylinginfo'] = 'Unique Styling Options';
$string['stylinginfo_desc'] = 'The options on this page help you custom the look of the child theme.';
$string['pagenavbuttonsbg'] = 'Drawer, Help, Back to Top Buttons';
$string['pagenavbuttonsbg_desc'] = 'Change the color of the side drawer buttons, help button, and back to top button.';
$string['drawerbg'] = 'Drawer background color';
$string['drawerbg_desc'] = 'Change the color of the drawer background.';
$string['bodybg'] = 'Body background color';
$string['bodybg_desc'] = 'Change the color of the body background.';
$string['bgwhite'] = 'Navbar Light Color';
$string['bgwhite_desc'] = 'Changes the value of the .bg-white class.';
$string['bgdark'] = 'Navbar Dark Color';
$string['bgdark_desc'] = 'Changes the value of the .bg-dark class.';
$string['sectionstyle'] = 'Course Section Style Chooser';
$string['sectionstyle_desc'] = 'Choose a style for course sections.';
$string['sections-boost'] = 'Boost Default';
$string['sections-learnr'] = 'learnr Default Style';
$string['sections-boxed'] = 'Boxed Style';
$string['sections-bars'] = 'Solid Section Bars Style';
$string['secondarymenuposition'] = 'Secondary Menu Position';
$string['secondarymenuposition_desc'] = 'Determine if the secondary menu appears above or below the header area of a page.';
$string['secondarymenuposition_above'] = 'Above header area';
$string['secondarymenuposition_below'] = 'Below header area';
$string['layoutstyle'] = 'Layout Style Chooser';
$string['layoutstyle_desc'] = 'Choose a style for layouts.  Choose Boost Default if you would like to revert to the more traditional Boost styling.';
$string['layoutstyle-boost'] = 'Boost Default';
$string['layoutstyle-learnr'] = 'learnr Default Style';
$string['layoutstyle-boxed'] = 'Boxed Style';
$string['layoutstyle-bars'] = 'Solid Section Bars Style';

// Features tab.
$string['featurestab'] = 'Features';
// ... Section: Example.
$string['featuresheading'] = 'LearnR Features';
$string['showprogressbar'] = 'Show Course Progress Bar';
$string['showprogressbar_desc'] = 'Display a progress bar for students at the top of each main course page.';
$string['myprogresspercentage'] = '%';
$string['showlatestcourses'] = 'Show Latest Courses Drop Down';
$string['showlatestcourses_desc'] = 'This will display the last 7 courses a user has visited in a drop down menu to the right of the course title.  At this time, it is not part of the main navigation but rather a navigation element in course pages.';
$string['latestcourses'] = 'Recent Courses';
$string['viewallcourses'] = 'View All Courses';
$string['showeasyenrolbtn'] = 'Show Easy Enrollment Button';
$string['showeasyenrolbtn_desc'] = 'Display a quick link in the header for the Easy Enrollment plugin.  This allows teachers to quickly get to their easy enrollment codes while in a course.';
$string['showcoursemanagement'] = 'Show Course Management';
$string['showcoursemanagement_desc'] = 'Course Management is a collection of most used links for teachers.  This is a sliding panel that is toggled from the header of the course.';
$string['showcourseactivities'] = 'Show Course Activities Menu';
$string['showcourseactivities_desc'] = 'Show a Course Activities menu icon in the header.';

//Icon Navigation tab.
$string['iconnavbartab'] = 'Icon Navigation';
$string['iconnavheading'] = 'Icon Navigation';
$string['iconnavinfo'] = 'Dashboard Icon Navigation';
$string['iconnavinfo_desc'] = 'Create buttons with icons for use on the homepage. These appear at the top of the page on the Dashboard.';
$string['navicon1'] = 'Homepage Icon One';
$string['navicon2'] = 'Homepage Icon Two';
$string['navicon3'] = 'Homepage Icon Three';
$string['navicon4'] = 'Homepage Icon Four';
$string['navicon5'] = 'Homepage Icon Five';
$string['navicon6'] = 'Homepage Icon Six';
$string['navicon7'] = 'Homepage Icon Seven';
$string['navicon8'] = 'Homepage Icon Eight';
$string['createinfo'] = 'Special Course Creator Button';
$string['createinfodesc'] = 'This button appears on the homepage when a user can create new courses.  Those with the role of Course Creator at the site level will see this button.';
$string['sliderinfo'] = 'Special Slide Icon Button';
$string['sliderinfodesc'] = 'This button will show/hide a special textbox which slides down from the icon navigation bar.  This is ideal for featuring courses, providing help, or listing required staff training.';
$string['slidetextbox'] = 'Slide Textbox';
$string['slidetextbox_desc'] = 'This textbox content will be displayed when the Slide Button is pressed.';
$string['navicon'] = 'Icon';
$string['navicondesc'] = 'Name of the icon you wish to use. List is <a href="https://fontawesome.com/v4.7.0/icons/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';
$string['naviconslidedesc'] = 'Suggested icon text: arrow-circle-down . Or choose from the list is <a href="https://fontawesome.com/v4.7.0/icons/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';
$string['naviconbuttontext'] = 'Link Text';
$string['naviconbuttontextdesc'] = 'Text to appear below the icon.';
$string['naviconbuttonurl'] = 'Link URL';
$string['naviconbuttonurldesc'] = 'URL the button will point to. You can link to anywhere including outside websites  just enter the proper URL.  If your Moodle site is in a subdirectory the default URL will not work.  Please adjust the URL to reflect the subdirectory. Example if "moodle" was your subdirectory folder then the URL would need to be changed to /moodle/my/ ';
$string['marketingurltarget'] = 'Link Target';
$string['marketingurltargetdesc'] = 'Choose how the link should be opened';
$string['marketingurltargetself'] = 'Current Page';
$string['marketingurltargetnew'] = 'New Page';
$string['marketingurltargetparent'] = 'Parent Frame';
$string['createinfo'] = 'Special Course Creator Button';
$string['createinfodesc'] = 'This button appears on the homepage when a user can create new courses.  Those with the role of Course Creator at the site level will see this button.';

// Misc strings.
$string['nomycourses'] = 'You are not enrolled in any courses';
$string['easyenrollbtn'] = 'Enrollment Codes';
$string['manageuserstitle'] = 'Users';
$string['gradebooktitle'] = 'Gradebook';
$string['progresstitle'] = 'Progress';
$string['coursemanagetitle'] = 'Course';
$string['coursemanagementbutton'] = 'Course Management';
$string['exporttomistar'] = 'Export to MIStar';
$string['userreportgradebook'] = 'User Report';
$string['courseblockpanelbtnclose'] = 'Close';
$string['courseblockpanelbtn'] = 'Course Management';
$string['naviconbuttoncreatetextdefault'] = 'Create a Course';
$string['courseactivitiesbtntext'] = 'Course Activities';
$string['courseenrollmentcode'] = 'Course Enrollment Code';

// Course Styles
$string['coursestyle1'] = 'Tile Style One';
$string['coursestyle2'] = 'Tile Style Two';
$string['coursestyle3'] = 'Tile Style Three';
$string['coursestyle4'] = 'Tile Style Four w/course summary';
$string['coursestyle5'] = 'Horizontal Style One';
$string['coursestyle6'] = 'Horizontal Image Background Full Details';
$string['coursestyle7'] = 'Horizontal Image Background Title & Teacher Only';
$string['coursestyle8'] = 'Default Moodle Course Display';
$string['coursetilestyle'] = 'Course Tile Display';
$string['coursetilestyle_desc'] = 'When viewing course categories you can choose from the following styles to display courses on the home page and course category areas.';
$string['trimtitle'] = 'Trim Course Title';
$string['trimtitle_desc'] = 'Enter a number to trim the title length.  This number represents characters that will be displayed.';
$string['trimsummary'] = 'Trim Course Summary';
$string['trimsummary_desc'] = 'Enter a number to trim the summary length.  This number represents characters that will be displayed.';
$string['courseboxheight'] = 'Course Tile Height';
$string['courseboxheight_desc'] = 'Control the height of the Course tile on the frontpage and course categories.';
$string['enrollcoursecard'] = 'View Course';





// Deprecated. 
$string['showcourseindexnav'] = 'Show Page Drawers';
$string['showcourseindexnav_desc'] = 'Uncheck this box to hide the drawer navigation panels.  Not recommended but might be useful for corporate or organizational training installations of Moodle.  Removal of the drawers simplifies the user interface and might be desired for simple complaince training.';


// End DBN Update.