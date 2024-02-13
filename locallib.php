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
 * Theme Boost Union Child - Local library
 *
 * @package    theme_learnr
 * @copyright  2023 Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* This file is empty by purpose.
You can add your Boost Union Child local functions here if you need to. */
// Begin DBN Update.
function theme_learnr_get_course_activities() {
    GLOBAL $CFG, $PAGE, $OUTPUT;
    // A copy of block_activity_modules.
    $course = $PAGE->course;
    $content = new stdClass();
    $modinfo = get_fast_modinfo($course);
    $modfullnames = array();

    $archetypes = array();

    foreach ($modinfo->cms as $cm) {
        // Exclude activities which are not visible or have no link (=label).
        if (!$cm->uservisible or !$cm->has_view()) {
            continue;
        }
        if (array_key_exists($cm->modname, $modfullnames)) {
            continue;
        }
        if (!array_key_exists($cm->modname, $archetypes)) {
            $archetypes[$cm->modname] = plugin_supports('mod', $cm->modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
        }
        if ($archetypes[$cm->modname] == MOD_ARCHETYPE_RESOURCE) {
            if (!array_key_exists('resources', $modfullnames)) {
                $modfullnames['resources'] = get_string('resources');
            }
        } else {
            $modfullnames[$cm->modname] = $cm->modplural;
        }
    }
    core_collator::asort($modfullnames);

    return $modfullnames;
}

function theme_learnr_get_course_header_image_url() {
    global $PAGE;

    // If the current course is the frontpage course (which means that we are not within any real course),
    // directly return null.
    if (isset($PAGE->course->id) && $PAGE->course->id == SITEID) {
        return null;
    }

    // Get the course image.
    $courseimage = \core_course\external\course_summary_exporter::get_course_image($PAGE->course);

    // If the course has a course image.
    if ($courseimage) {
        // Then return it directly.
        return $courseimage;

        // Otherwise, if a fallback image is configured.
    } else if (get_config('theme_boost_union', 'courseheaderimagefallback')) {
        // Get the system context.
        $systemcontext = \context_system::instance();

        // Get filearea.
        $fs = get_file_storage();

        // Get all files from filearea.
        $files = $fs->get_area_files($systemcontext->id, 'theme_boost_union', 'courseheaderimagefallback',
            false, 'itemid', false);

        // Just pick the first file - we are sure that there is just one file.
        $file = reset($files);

        // Build and return the image URL.
        return moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
            $file->get_itemid(), $file->get_filepath(), $file->get_filename());
    }

    // As no picture was found, return null.
    return null;
}
// End DBN Update.