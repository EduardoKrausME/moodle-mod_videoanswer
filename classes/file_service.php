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
 * Class file_service.
 */
class file_service {
    /**
     * Method serve.
     *
     * @param mixed $course Parameter course.
     * @param mixed $cm Parameter cm.
     * @param mixed $context Parameter context.
     * @param string $filearea Parameter filearea.
     * @param array $args Parameter args.
     * @param bool $forcedownload Parameter forcedownload.
     * @param array $options Parameter options.
     * @return bool Return value.
     */
    public static function serve($course, $cm, $context, string $filearea, array $args,
                                 bool $forcedownload, array $options = []): bool {
        global $DB, $USER;

        if ($context->contextlevel !== CONTEXT_MODULE || $filearea !== 'submission') {
            return false;
        }

        require_login($course, true, $cm);

        $submissionid = (int)array_shift($args);
        $submission = $DB->get_record('videoanswer_submissions', ['id' => $submissionid]);
        if (!$submission || (int)$submission->videoanswerid !== (int)$cm->instance) {
            return false;
        }

        if ((int)$submission->userid !== (int)$USER->id && !has_capability('mod/videoanswer:viewreports', $context)) {
            return false;
        }

        $filename = array_pop($args);
        $filepath = '/' . ($args ? implode('/', $args) . '/' : '');

        $file = get_file_storage()->get_file(
            $context->id,
            'mod_videoanswer',
            'submission',
            $submissionid,
            $filepath,
            $filename
        );
        if (!$file || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 0, 0, $forcedownload, $options);
        return true;
    }
}
