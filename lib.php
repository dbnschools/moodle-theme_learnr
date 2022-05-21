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
 * Theme LearnR - Library
 *
 * @package    theme_learnr
 * @copyright  2022 Dearborn Public Schools, Chris Kenniburg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_learnr_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/learnr/pre.scss');
    if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_learnr', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/preset/default.scss');
    }
    $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/learnr.scss');

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

    $scss .= file_get_contents($CFG->dirroot . '/theme/learnr/scss/learnr/post.scss');

    return $scss;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_learnr_get_pre_scss($theme) {
    global $CFG;

    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
            'brandcolor' => ['primary'],
            'successcolor' => ['success'],
            'infocolor' => ['info'],
            'warningcolor' => ['warning'],
            'dangercolor' => ['danger'],
            'secondarycolor' => ['secondary'],
            'iconadministrationcolor' => ['administration'],
            'iconassessmentcolor' => ['assessment'],
            'iconcollaborationcolor' => ['collaboration'],
            'iconcommunicationcolor' => ['communication'],
            'iconcontentcolor' => ['content'],
            'iconinterfacecolor' => ['interface'],
            'courseiconsize' => ['courseiconsize'],
            'navbarbg' => ['navbar-bg'],
            'primarynavbarlink' => ['navbar-dark-color'],
            'secondarynavbarlink' => ['navbar-light-color'],
            'drawerbg' => ['drawer-bg-color'],
            'bodybg' => ['body-bg'],
            'fullwidthpage' => ['fullwidthpage'],
            'showpageimage' => ['showpageimage'],
            'iconwidth' => ['fpicon-width'],
            'courseboxheight' => ['courseboxheight'],
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    // Set the image.
    $marketing1image = $theme->setting_file_url('marketing1image', 'marketing1image');
    if (isset($marketing1image)) {
        // Add a fade in transition to avoid the flicker on course headers ***.
        $scss .= '.marketing1image {background-image: url("' . $marketing1image . '"); background-size:cover; background-position:center;}';
    }

    // Set the image.
    $marketing2image = $theme->setting_file_url('marketing2image', 'marketing2image');
    if (isset($marketing2image)) {
        // Add a fade in transition to avoid the flicker on course headers ***.
        $scss .= '.marketing2image {background-image: url("' . $marketing2image . '"); background-size:cover; background-position:center;}';
    }

    // Set the image.
    $marketing3image = $theme->setting_file_url('marketing3image', 'marketing3image');
    if (isset($marketing3image)) {
        // Add a fade in transition to avoid the flicker on course headers ***.
        $scss .= '.marketing3image {background-image: url("' . $marketing3image . '"); background-size:cover; background-position:center;}';
    }

    return $scss;
}
/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_learnr_get_precompiled_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/learnr/style/moodle.css');
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_learnr_get_extra_scss($theme) {
    $content = '';

    // Sets the login background image.
    $loginbackgroundimageurl = $theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
    if (!empty($loginbackgroundimageurl)) {
        $content .= 'body.pagelayout-login #page { ';
        $content .= "background-image: url('$loginbackgroundimageurl'); background-size: cover;";
        $content .= ' }';
    }
    
    return $content;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_learnr_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'pagebackgroundimage' || $filearea === 'loginbackgroundimage' || $filearea === 'marketing1image' || $filearea === 'marketing2image' || $filearea === 'marketing3image')) {
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


function theme_learnr_strip_html_tags( $text ) {
    $text = preg_replace(
        array(
            // Remove invisible content.
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks.
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
            ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
            ),
        $text
        );
return strip_tags( $text );
}

/**
 * Cut the Course content.
 *
 * @param $str
 * @param $n
 * @param $end_char
 * @return string
 */
function theme_learnr_course_trim_char($str, $n = 500, $endchar = '&#8230;') {
    if (strlen($str) < $n) {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));
    if (strlen($str) <= $n) {
        return $str;
    }

    $out = "";
    $small = substr($str, 0, $n);
    $out = $small.$endchar;
    return $out;
}