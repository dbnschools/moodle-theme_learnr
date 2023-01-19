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
 * Theme LearnR - Theme config
 *
 * @package    theme_learnr
 * @copyright  2022 Dearborn Public Schools, Chris Kenniburg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'learnr';
$THEME->parents = ['boost'];
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->usefallback = true;
$THEME->scss = function($theme) {
    return theme_learnr_get_main_scss_content($theme);
};

$THEME->layouts = [
    // Most backwards compatible layout without the blocks.
    'base' => [
        'file' => 'drawers.php',
        'regions' => [],
    ],
    // Standard layout with blocks.
    'standard' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Main course page.
    'course' => [
        'file' => 'course.php',
        'regions' => ['side-pre','columna', 'columnb', 'columnc', 'footera', 'footerb', 'footerc'],
        'defaultregion' => 'columna',
        'options' => ['langmenu' => true],
    ],
    'coursecategory' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    'incourse' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre','columna', 'columnb', 'columnc', 'footera', 'footerb', 'footerc'],
        'defaultregion' => 'side-pre',
    ],
    // The site home page.
    'frontpage' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
    // Server administration scripts.
    'admin' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // My courses page.
    'mycourses' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
    // My dashboard page.
    'mydashboard' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true, 'langmenu' => true],
    ],
    // My public page.
    'mypublic' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre','columna', 'columnb', 'columnc', 'footera', 'footerb', 'footerc'],
        'defaultregion' => 'side-pre',
    ],
    'login' => [
        'file' => 'login.php',
        'regions' => [],
        'options' => ['langmenu' => true],
    ],

    // Pages that appear in pop-up windows - no navigation, no blocks, no header and bare activity header.
    'popup' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => [
            'nofooter' => true,
            'nonavbar' => true,
            'activityheader' => [
                'notitle' => true,
                'nocompletion' => true,
                'nodescription' => true
            ]
        ]
    ],
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => [
            'nofooter' => true,
            'nocoursefooter' => true,
            'activityheader' => [
                'nocompletion' => true
            ]
        ],
    ],
    // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible.
    'embedded' => [
        'file' => 'embedded.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, links, or API calls that would lead to database or cache interaction.
    // Please be extremely careful if you are modifying this layout.
    'maintenance' => [
        'file' => 'maintenance.php',
        'regions' => [],
    ],
    // Should display the content and basic headers only.
    'print' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => false, 'noactivityheader' => true],
    ],
    // The pagelayout used when a redirection is occuring.
    'redirect' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    // The pagelayout used for reports.
    'report' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // The pagelayout used for safebrowser and securewindow.
    'secure' => [
        'file' => 'secure.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre'
    ]
];

if ($THEME->settings->showheaderblockpanel == 1 && $THEME->settings->showblockdrawer == 1) {
    $THEME->layouts['course'] = [
        'file' => 'course.php',
        'regions' => ['side-pre', 'columna', 'columnb', 'columnc', 'footera', 'footerb', 'footerc'],
        'defaultregion' => 'side-pre',
        'options' => ['langmenu' => true],
    ];
}
if ($THEME->settings->showheaderblockpanel == 0 && $THEME->settings->showblockdrawer == 1){
    $THEME->layouts['course'] = [
        'file' => 'course.php',
        'regions' => ['side-pre','footera', 'footerb', 'footerc'],
        'defaultregion' => 'side-pre',
        'options' => ['langmenu' => true],
    ];
}
if ($THEME->settings->showheaderblockpanel == 1 && $THEME->settings->showblockdrawer == 0){
     $THEME->layouts['mydashboard'] = [
        'file' => 'drawers.php',
        'regions' => [ ],
        'options' => ['nonavbar' => true, 'langmenu' => true],
    ];
    $THEME->layouts['course'] = [
        'file' => 'course.php',
        'regions' => ['columna', 'columnb', 'columnc', 'footera', 'footerb', 'footerc'],
        'defaultregion' => 'columna',
        'options' => ['langmenu' => true],
    ];
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    $THEME->layouts['incourse'] = [
        'file' => 'drawers.php',
        'regions' => ['side-pre','columna', 'columnb', 'columnc', 'footera', 'footerb', 'footerc'],
        'defaultregion' => 'side-pre',
    ];
}

$THEME->prescsscallback = 'theme_learnr_get_pre_scss';
$THEME->extrascsscallback = 'theme_learnr_get_extra_scss';
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->precompiledcsscallback = 'theme_learnr_get_precompiled_css';
$THEME->yuicssmodules = array();

$THEME->haseditswitch = true;
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;
$THEME->activityheaderconfig = [
    'notitle' => true
];
$THEME->requiredblocks = '';
$THEME->enable_dock = false;

if ($THEME->settings->showcourseindexnav == 1) {
    $THEME->usescourseindex = true;
} else {
    $THEME->usescourseindex = false;
}
// ADDED tinjohn 20221206.
$THEME->removedprimarynavitems = explode(',', $THEME->settings->removedprimarynavitems);
// ADDED tinjohn 20231701.
$THEME->javascripts_footer = array('dynprogressbar');
