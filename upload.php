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
define('AJAX_SCRIPT', true);
require_once('../../config.php');

$cmid = required_param('cmid', PARAM_INT);
$duration = required_param('duration', PARAM_INT);
require_sesskey();

$cm = get_coursemodule_from_id('videoanswer', $cmid, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('videoanswer', ['id' => $cm->instance], '*', MUST_EXIST);
require_login($course, false, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/videoanswer:view', $context);
require_capability('mod/videoanswer:submit', $context);

header('Content-Type: application/json; charset=utf-8');

try {
    if (empty($_FILES['video'])) {
        throw new moodle_exception('uploaderror', 'videoanswer');
    }

    $submission = \mod_videoanswer\submission_service::save(
        $activity,
        $context,
        (int)$USER->id,
        $_FILES['video'],
        $duration
    );
    $url = \mod_videoanswer\submission_service::get_file_url($context, $submission);

    echo json_encode([
        'success' => true,
        'message' => get_string('submitted', 'videoanswer'),
        'url' => $url ? $url->out(false) : '',
        'submittedat' => userdate($submission->timemodified),
    ]);
} catch (Throwable $e) {
    $message = $e instanceof moodle_exception ? $e->getMessage() : get_string('uploaderror', 'videoanswer');
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $message,
    ]);
}
