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
require_capability('mod/videoanswer:viewreports', $context);

$url = new moodle_url('/mod/videoanswer/report.php', ['id' => $cm->id]);
$PAGE->set_url($url);
$PAGE->set_title(get_string('reports', 'videoanswer'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('reports', 'videoanswer'));
echo $OUTPUT->heading(format_string($activity->name), 3);

$table = new \mod_videoanswer\table\submission_table(
    'mod-videoanswer-submissions-' . $cm->id,
    $context,
    (int)$course->id,
    (int)$activity->id
);
$table->define_baseurl($url);
$table->out(25, true);

echo $OUTPUT->footer();
