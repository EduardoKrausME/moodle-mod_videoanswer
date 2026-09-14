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
 * Short video answer activity.
 *
 * @package    mod_videoanswer
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_videoanswer\file_service;

/**
 * videoanswer_supports
 *
 * @param string $feature
 * @return bool|int|null
 */
function videoanswer_supports(string $feature) {
    return match ($feature) {
        FEATURE_MOD_ARCHETYPE => MOD_ARCHETYPE_OTHER,
        FEATURE_MOD_INTRO => true,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_GRADE_HAS_GRADE => false,
        FEATURE_BACKUP_MOODLE2 => true,
        default => null,
    };
}

/**
 * videoanswer_add_instance
 *
 * @param $data
 * @param $mform
 * @return int
 */
function videoanswer_add_instance($data, $mform = null): int {
    return \mod_videoanswer\activity::create($data);
}

/**
 * videoanswer_update_instance
 *
 * @param $data
 * @param $mform
 * @return bool
 */
function videoanswer_update_instance($data, $mform = null): bool {
    return \mod_videoanswer\activity::update($data);
}

/**
 * videoanswer_delete_instance
 *
 * @param int $id
 * @return bool
 */
function videoanswer_delete_instance(int $id): bool {
    return \mod_videoanswer\activity::delete($id);
}

/**
 * videoanswer_pluginfile
 *
 * @param $course
 * @param $cm
 * @param $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function videoanswer_pluginfile($course, $cm, $context, string $filearea,
                                array $args, bool $forcedownload, array $options = []): bool {
    return file_service::serve($course, $cm, $context, $filearea, $args, $forcedownload, $options);
}
