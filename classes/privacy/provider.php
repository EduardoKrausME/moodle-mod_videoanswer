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
namespace mod_videoanswer\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

/**
 * Class provider.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Method get_metadata.
     *
     * @param collection $collection Parameter collection.
     * @return collection Return value.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('videoanswer_submissions', [
            'userid' => 'privacy:metadata:videoanswer_submissions:userid',
            'duration' => 'privacy:metadata:videoanswer_submissions:duration',
            'mimetype' => 'privacy:metadata:videoanswer_submissions:mimetype',
            'filesize' => 'privacy:metadata:videoanswer_submissions:filesize',
            'timecreated' => 'privacy:metadata:videoanswer_submissions:timecreated',
            'timemodified' => 'privacy:metadata:videoanswer_submissions:timemodified',
        ], 'privacy:metadata:videoanswer_submissions');
        $collection->add_subsystem_link('core_files', [], 'privacy:metadata:files');
        return $collection;
    }

    /**
     * Method get_contexts_for_userid.
     *
     * @param int $userid Parameter userid.
     * @return contextlist Return value.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {videoanswer_submissions} s ON s.videoanswerid = cm.instance
                 WHERE s.userid = :userid";
        $params = [
            'contextlevel' => CONTEXT_MODULE,
            'modname' => 'videoanswer',
            'userid' => $userid,
        ];
        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Method export_user_data.
     *
     * @param approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        if (!$contextlist->count()) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            $cm = get_coursemodule_from_id('videoanswer', $context->instanceid, 0, false, IGNORE_MISSING);
            if (!$cm) {
                continue;
            }
            $submission = $DB->get_record('videoanswer_submissions', [
                'videoanswerid' => $cm->instance,
                'userid' => $userid,
            ]);
            if (!$submission) {
                continue;
            }

            $data = (object)[
                'duration_ms' => $submission->duration,
                'mimetype' => $submission->mimetype,
                'filesize' => $submission->filesize,
                'timecreated' => transform::datetime($submission->timecreated),
                'timemodified' => transform::datetime($submission->timemodified),
            ];
            writer::with_context($context)->export_data([], $data);
            writer::with_context($context)->export_area_files([], 'mod_videoanswer', 'submission', $submission->id);
        }
    }

    /**
     * Method delete_data_for_all_users_in_context.
     *
     * @param \context $context Parameter context.
     * @return void Return value.
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if ($context->contextlevel !== CONTEXT_MODULE) {
            return;
        }
        $cm = get_coursemodule_from_id('videoanswer', $context->instanceid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return;
        }
        $DB->delete_records('videoanswer_submissions', ['videoanswerid' => $cm->instance]);
        get_file_storage()->delete_area_files($context->id, 'mod_videoanswer', 'submission');
    }

    /**
     * Method delete_data_for_user.
     *
     * @param approved_contextlist $contextlist Parameter contextlist.
     * @return void Return value.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_MODULE) {
                continue;
            }
            $cm = get_coursemodule_from_id('videoanswer', $context->instanceid, 0, false, IGNORE_MISSING);
            if (!$cm) {
                continue;
            }
            $submission = $DB->get_record('videoanswer_submissions', [
                'videoanswerid' => $cm->instance,
                'userid' => $userid,
            ]);
            if (!$submission) {
                continue;
            }
            get_file_storage()->delete_area_files($context->id, 'mod_videoanswer', 'submission', $submission->id);
            $DB->delete_records('videoanswer_submissions', ['id' => $submission->id]);
        }
    }
}
