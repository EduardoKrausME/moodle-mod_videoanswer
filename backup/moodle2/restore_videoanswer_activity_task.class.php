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

require_once($CFG->dirroot . '/mod/videoanswer/backup/moodle2/restore_videoanswer_stepslib.php');

/**
 * Class restore_videoanswer_activity_task.
 */
class restore_videoanswer_activity_task extends restore_activity_task {
    /**
     * Defines activity-specific settings.
     *
     * @return void
     */
    protected function define_my_settings() {
    }

    /**
     * Defines activity-specific restore steps.
     *
     * @return void
     */
    protected function define_my_steps() {
        $this->add_step(new restore_videoanswer_activity_structure_step('videoanswer_structure', 'videoanswer.xml'));
    }

    /**
     * Defines content fields processed by the link decoder.
     *
     * @return restore_decode_content[]
     */
    public static function define_decode_contents() {
        return [
            new restore_decode_content('videoanswer', ['intro'], 'videoanswer'),
        ];
    }

    /**
     * Defines URL decoding rules for this activity.
     *
     * @return restore_decode_rule[]
     */
    public static function define_decode_rules() {
        return [
            new restore_decode_rule('VIDEOANSWERVIEWBYID', '/mod/videoanswer/view.php?id=$1', 'course_module'),
            new restore_decode_rule('VIDEOANSWERINDEX', '/mod/videoanswer/index.php?id=$1', 'course'),
        ];
    }

    /**
     * Defines legacy log restore rules.
     *
     * @return restore_log_rule[]
     */
    public static function define_restore_log_rules() {
        return [];
    }
}
