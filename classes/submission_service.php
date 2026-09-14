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
 * Class submission_service.
 */
class submission_service {
    /** @var float|int */
    public const MAX_FILE_SIZE = 50 * 1024 * 1024;

    /** @var int */
    private const DURATION_TOLERANCE_MS = 2000;

    /**
     * Method get_for_user.
     *
     * @param int $videoanswerid Parameter videoanswerid.
     * @param int $userid Parameter userid.
     * @return ?object Return value.
     */
    public static function get_for_user(int $videoanswerid, int $userid): ?object {
        global $DB;
        $record = $DB->get_record('videoanswer_submissions', [
            'videoanswerid' => $videoanswerid,
            'userid' => $userid,
        ]);
        return $record ?: null;
    }

    /**
     * Method save.
     *
     * @param object $activity Parameter activity.
     * @param \context_module $context Parameter context.
     * @param int $userid Parameter userid.
     * @param array $upload Parameter upload.
     * @param int $duration Parameter duration.
     * @return object Return value.
     */
    public static function save(object $activity, \context_module $context, int $userid, array $upload, int $duration): object {
        global $DB;

        self::validate_upload($activity, $upload, $duration);

        $existing = self::get_for_user((int)$activity->id, $userid);
        if ($existing && empty($activity->allowretake)) {
            throw new \moodle_exception('alreadyanswered', 'videoanswer');
        }

        $now = time();
        $mimetype = self::normalise_mimetype((string)($upload['type'] ?? ''));

        if ($existing) {
            $submission = $existing;
            $submission->duration = $duration;
            $submission->mimetype = $mimetype;
            $submission->filesize = (int)$upload['size'];
            $submission->timemodified = $now;
            $DB->update_record('videoanswer_submissions', $submission);
        } else {
            $submission = (object)[
                'videoanswerid' => (int)$activity->id,
                'userid' => $userid,
                'duration' => $duration,
                'mimetype' => $mimetype,
                'filesize' => (int)$upload['size'],
                'timecreated' => $now,
                'timemodified' => $now,
            ];
            $submission->id = $DB->insert_record('videoanswer_submissions', $submission);
        }

        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_videoanswer', 'submission', (int)$submission->id);

        $extension = self::extension_for_mimetype($mimetype);
        $record = [
            'contextid' => $context->id,
            'component' => 'mod_videoanswer',
            'filearea' => 'submission',
            'itemid' => (int)$submission->id,
            'filepath' => '/',
            'filename' => 'answer.' . $extension,
            'userid' => $userid,
        ];
        $fs->create_file_from_pathname($record, $upload['tmp_name']);

        return $submission;
    }

    /**
     * Method get_file.
     *
     * @param \context_module $context Parameter context.
     * @param int $submissionid Parameter submissionid.
     * @return ?\stored_file Return value.
     */
    public static function get_file(\context_module $context, int $submissionid): ?\stored_file {
        $files = get_file_storage()->get_area_files(
            $context->id,
            'mod_videoanswer',
            'submission',
            $submissionid,
            'id',
            false
        );
        if (!$files) {
            return null;
        }
        return reset($files) ?: null;
    }

    /**
     * Method get_file_url.
     *
     * @param \context_module $context Parameter context.
     * @param object $submission Parameter submission.
     * @return ?\moodle_url Return value.
     */
    public static function get_file_url(\context_module $context, object $submission): ?\moodle_url {
        $file = self::get_file($context, (int)$submission->id);
        if (!$file) {
            return null;
        }
        return \moodle_url::make_pluginfile_url(
            $context->id,
            'mod_videoanswer',
            'submission',
            (int)$submission->id,
            '/',
            $file->get_filename(),
            false
        );
    }

    /**
     * Method validate_upload.
     *
     * @param object $activity Parameter activity.
     * @param array $upload Parameter upload.
     * @param int $duration Parameter duration.
     * @return void Return value.
     */
    private static function validate_upload(object $activity, array $upload, int $duration): void {
        if (empty($upload['tmp_name']) || !is_uploaded_file($upload['tmp_name'])) {
            throw new \moodle_exception('uploaderror', 'videoanswer');
        }

        if (!empty($upload['error'])) {
            throw new \moodle_exception('uploaderror', 'videoanswer');
        }

        $size = (int)($upload['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_FILE_SIZE) {
            throw new \moodle_exception('toobig', 'videoanswer');
        }

        $maxduration = ((int)$activity->timelimit * 1000) + self::DURATION_TOLERANCE_MS;
        if ($duration <= 0 || $duration > $maxduration) {
            throw new \moodle_exception('invalidduration', 'videoanswer');
        }

        $mimetype = self::normalise_mimetype((string)($upload['type'] ?? ''));
        if (!in_array($mimetype, ['video/webm', 'video/mp4', 'video/quicktime'], true)) {
            throw new \moodle_exception('invalidmimetype', 'videoanswer');
        }
    }

    /**
     * Method normalise_mimetype.
     *
     * @param string $mimetype Parameter mimetype.
     * @return string Return value.
     */
    private static function normalise_mimetype(string $mimetype): string {
        $mimetype = strtolower(trim(explode(';', $mimetype)[0]));
        if ($mimetype === 'video/x-m4v') {
            return 'video/mp4';
        }
        return $mimetype;
    }

    /**
     * Method extension_for_mimetype.
     *
     * @param string $mimetype Parameter mimetype.
     * @return string Return value.
     */
    private static function extension_for_mimetype(string $mimetype): string {
        return match ($mimetype) {
            'video/mp4' => 'mp4',
            'video/quicktime' => 'mov',
            default => 'webm',
        };
    }
}
