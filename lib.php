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
 * Theme Boost Union Child - Library
 *
 * @package    theme_learnr
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Constants which are use throughout this theme.
define('THEME_LEARNR_SETTING_INHERITANCE_INHERIT', 0);
define('THEME_LEARNR_SETTING_INHERITANCE_DUPLICATE', 1);

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_learnr_get_main_scss_content($theme) {
    global $CFG;

    // Require the necessary libraries.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // As a start, get the compiled main SCSS from Boost Union.
    // This way, Boost Union Child will ship the same SCSS code as Boost Union itself.
    $scss = theme_boost_union_get_main_scss_content(theme_config::load('boost_union'));

    // And add Boost Union Child's main SCSS file to the stack.
    $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/post.scss');

    // Begin DBN Update.
    if ($theme->settings->sectionstyle == 1) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/sections/sections-learnr.scss');
    }

    if ($theme->settings->sectionstyle == 2) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/sections/sections-boxed.scss');
    }

    if ($theme->settings->sectionstyle == 3) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/sections/sections-boost.scss');
    }

    if ($theme->settings->sectionstyle == 4) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/sections/sections-bars.scss');
    }

    if ($theme->settings->layoutstyle == 1) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/layouts/layout-learnr.scss');
    }

    if ($theme->settings->layoutstyle == 2) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/layouts/layout-boxed.scss');
    }

    if ($theme->settings->layoutstyle == 3) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/layouts/layout-boost.scss');
    }

    if ($theme->settings->layoutstyle == 4) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/layouts/layout-bars.scss');
    }
    // End DBN Update.

    return $scss;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_learnr_get_pre_scss($theme) {
    global $CFG;

    // Require the necessary libraries.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // As a start, initialize the Pre SCSS code with an empty string.
    $scss = '';

    // Then, if configured, get the compiled pre SCSS code from Boost Union.
    // This should not be necessary as Moodle core calls the *_get_pre_scss() functions from all parent themes as well.
    // However, as soon as Boost Union would use $theme->settings in this function, $theme would be this theme here and
    // not Boost Union. The Boost Union developers are aware of this topic, but faults can always happen.
    // If such a fault happens, the Boost Union Child administrator can switch the inheritance to 'Duplicate'.
    // This way, we will add the pre SCSS code with the explicit use of the Boost Union configuration to the stack.
    $inheritanceconfig = get_config('theme_learnr', 'prescssinheritance');
    if ($inheritanceconfig == THEME_LEARNR_SETTING_INHERITANCE_DUPLICATE) {
        $scss .= theme_boost_union_get_pre_scss(theme_config::load('boost_union'));
    }

    $configurable = [
        // Config key => [variableName, ...].
        // Begin DBN Update.
        'navbarbg' => ['navbar-bg'],
        'navbarlink' => ['navbar-link'],
        'navbarlinkhover' => ['navbar-link-hover'],
        'navbarsitetitlecolor' => ['navbarsitetitlecolor'],
        'drawerbg' => ['drawer-bg'],
        'bodybg' => ['body-bg'],
        'bgwhite' => ['bg-white'],
        'bgdark' => ['bg-dark'],
        'courseheaderbg' => ['courseheaderbg'],
        'pagenavbuttonsbg' => ['pagenavbuttonsbg'],
        'layoutstyle' => ['layoutstyle'],
        // End DBN Update.
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = get_config('theme_learnr', $configkey);
        if (!($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Finally, you can compose and add additional pre SCSS code here if needed.
    $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/pre.scss');

    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_learnr_get_extra_scss($theme) {
    global $CFG;

    // Require the necessary libraries.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // As a start, initialize the Extra SCSS code with an empty string.
    $scss = '';

    // Then, if configured, get the compiled extra SCSS code from Boost Union.
    // This should not be necessary as Moodle core calls the *_get_extra_scss() functions from all parent themes as well.
    // However, as soon as Boost Union would use $theme->settings in this function, $theme would be this theme here and
    // not Boost Union. The Boost Union developers are aware of this topic, but faults can always happen.
    // If such a fault happens, the Boost Union Child administrator can switch the inheritance to 'Duplicate'.
    // This way, we will add the extra SCSS code with the explicit use of the Boost Union configuration to the stack.
    $inheritanceconfig = get_config('theme_learnr', 'extrascssinheritance');
    if ($inheritanceconfig == THEME_LEARNR_SETTING_INHERITANCE_DUPLICATE) {
        $scss .= theme_boost_union_get_extra_scss(theme_config::load('boost_union'));
    }

    // Finally, you can compose and add additional extra SCSS code here if needed.

    return $scss;
}


/* 
//Not needed unless we add an image in future update.

function theme_learnr_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $CFG;

    // Serve the (general) logo files or favicon file from the theme settings.
    // This code is copied and modified from core_admin_pluginfile() in admin/lib.php.
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'courseheaderimagefallback')) {
        $theme = theme_config::load('learnr');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);

    } else {
        send_file_not_found();
    }
}
*/

