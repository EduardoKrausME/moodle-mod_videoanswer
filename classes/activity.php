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
namespace mod_videoanswer;

/**
 * Class activity.
 */
class activity {
    /**
     * Method create.
     *
     * @param object $data Parameter data.
     * @return int Return value.
     */
    public static function create(object $data): int {
        global $DB;

        $now = time();
        $data->timecreated = $now;
        $data->timemodified = $now;

        return (int)$DB->insert_record('videoanswer', $data);
    }

    /**
     * Method update.
     *
     * @param object $data Parameter data.
     * @return bool Return value.
     */
    public static function update(object $data): bool {
        global $DB;

        $data->id = $data->instance;
        $data->timemodified = time();

        return $DB->update_record('videoanswer', $data);
    }

    /**
     * Method delete.
     *
     * @param int $id Parameter id.
     * @return bool Return value.
     */
    public static function delete(int $id): bool {
        global $DB;

        $activity = $DB->get_record('videoanswer', ['id' => $id]);
        if (!$activity) {
            return false;
        }

        $cm = get_coursemodule_from_instance('videoanswer', $id, $activity->course, false, IGNORE_MISSING);
        if ($cm) {
            $context = \context_module::instance($cm->id);
            get_file_storage()->delete_area_files($context->id, 'mod_videoanswer', 'submission');
        }

        $DB->delete_records('videoanswer_submissions', ['videoanswerid' => $id]);
        $DB->delete_records('videoanswer', ['id' => $id]);
        return true;
    }
}
