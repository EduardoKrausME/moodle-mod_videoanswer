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

require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('videoanswer', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record('videoanswer', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/videoanswer:view', $context);

$PAGE->set_url('/mod/videoanswer/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($activity->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$submission = null;
$submissionurl = null;
if (has_capability('mod/videoanswer:submit', $context)) {
    $submission = \mod_videoanswer\submission_service::get_for_user((int)$activity->id, (int)$USER->id);
    if ($submission) {
        $submissionurl = \mod_videoanswer\submission_service::get_file_url($context, $submission);
    }
}

$PAGE->requires->strings_for_js([
    'record', 'stop', 'retake', 'submitvideo', 'recording', 'remaining', 'ready',
    'preview', 'submitted', 'replacewarning', 'permissiondenied', 'unsupportedbrowser',
    'recordingerror', 'uploaderror', 'uploading', 'toobig',
], 'videoanswer');

if (has_capability('mod/videoanswer:submit', $context) && (!$submission || !empty($activity->allowretake))) {
    $PAGE->requires->js_call_amd('mod_videoanswer/recorder', 'init', [[
        'cmid' => (int)$cm->id,
        'timelimit' => (int)$activity->timelimit,
        'hasprevious' => !empty($submission),
        'allowretake' => !empty($activity->allowretake),
        'maxbytes' => \mod_videoanswer\submission_service::MAX_FILE_SIZE,
        'uploadurl' => (new moodle_url('/mod/videoanswer/upload.php'))->out(false),
        'sesskey' => sesskey(),
    ]]);
}

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($activity->name));

if ($activity->intro) {
    echo $OUTPUT->box(format_module_intro('videoanswer', $activity, $cm->id), 'generalbox');
}

if (has_capability('mod/videoanswer:viewreports', $context)) {
    $reporturl = new moodle_url('/mod/videoanswer/report.php', ['id' => $cm->id]);
    echo html_writer::div(html_writer::link($reporturl, get_string('reports', 'videoanswer'),
        ['class' => 'btn btn-secondary']), 'mb-3');
}

if (has_capability('mod/videoanswer:submit', $context)) {
    $data = [
        'canrecord' => !$submission || !empty($activity->allowretake),
        'timelimit' => (int)$activity->timelimit,
        'hassubmission' => !empty($submission),
        'submissionurl' => $submissionurl ? $submissionurl->out(false) : '',
        'submissiondate' => $submission ? userdate($submission->timemodified) : '',
        'allowretake' => !empty($activity->allowretake),
    ];
    echo $OUTPUT->render_from_template('mod_videoanswer/recorder', $data);
}

echo $OUTPUT->footer();
