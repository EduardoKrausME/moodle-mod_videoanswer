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

defined('MOODLE_INTERNAL') || die;

$string['allowretake'] = 'Allow a new recording after submission';
$string['alreadyanswered'] = 'You have already submitted a video and this activity does not allow another submission.';
$string['duration'] = 'Duration';
$string['invalidduration'] = 'The reported recording duration is invalid.';
$string['invalidmimetype'] = 'Unsupported video format.';
$string['modulename'] = 'Short video answer';
$string['modulenameplural'] = 'Short video answers';
$string['nosubmissions'] = 'No video responses have been submitted yet.';
$string['permissiondenied'] = 'Camera and microphone access was denied.';
$string['pluginadministration'] = 'Short video answer administration';
$string['pluginname'] = 'Short video answer';
$string['preview'] = 'Preview';
$string['privacy:metadata:files'] = 'The activity stores each submitted video in the Moodle file API.';
$string['privacy:metadata:videoanswer_submissions'] = 'Information about video responses submitted to the activity.';
$string['privacy:metadata:videoanswer_submissions:duration'] = 'The recording duration.';
$string['privacy:metadata:videoanswer_submissions:filesize'] = 'The uploaded video file size.';
$string['privacy:metadata:videoanswer_submissions:mimetype'] = 'The uploaded video MIME type.';
$string['privacy:metadata:videoanswer_submissions:timecreated'] = 'When the first submission was created.';
$string['privacy:metadata:videoanswer_submissions:timemodified'] = 'When the submission was last replaced.';
$string['privacy:metadata:videoanswer_submissions:userid'] = 'The user who submitted the video.';
$string['question'] = 'Question / prompt';
$string['ready'] = 'Ready to record';
$string['record'] = 'Start recording';
$string['recording'] = 'Recording';
$string['recordingerror'] = 'Could not start the camera or microphone.';
$string['remaining'] = 'Remaining: {$a}s';
$string['replacewarning'] = 'Submitting this recording will replace your previous video.';
$string['reports'] = 'Responses';
$string['retake'] = 'Record again';
$string['seconds'] = '{$a} seconds';
$string['stop'] = 'Stop';
$string['student'] = 'Student';
$string['submission'] = 'Your submission';
$string['submitted'] = 'Video submitted.';
$string['submittedat'] = 'Submitted';
$string['submitvideo'] = 'Submit video';
$string['timelimit'] = 'Maximum recording time';
$string['toobig'] = 'The recorded file is too large.';
$string['unsupportedbrowser'] = 'This browser does not support video recording with MediaRecorder.';
$string['uploaderror'] = 'The video could not be submitted.';
$string['uploading'] = 'Submitting video…';
$string['video'] = 'Video';
$string['videoanswer:addinstance'] = 'Add a short video answer activity';
$string['videoanswer:submit'] = 'Record and submit a video answer';
$string['videoanswer:view'] = 'View a short video answer activity';
$string['videoanswer:viewreports'] = 'View video answer reports';
