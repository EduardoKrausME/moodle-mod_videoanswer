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

/**
 * Class restore_videoanswer_activity_structure_step.
 */
class restore_videoanswer_activity_structure_step extends restore_activity_structure_step {
    /**
     * Method define_structure.
     *
     * @return array Return value.
     */
    protected function define_structure(): array {
        $paths = [];
        $paths[] = new restore_path_element('videoanswer', '/activity/videoanswer');
        if ($this->get_setting_value('userinfo')) {
            $paths[] = new restore_path_element('videoanswer_submission', '/activity/videoanswer/submissions/submission');
        }
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Method process_videoanswer.
     *
     * @param mixed $data Parameter data.
     * @return void Return value.
     */
    protected function process_videoanswer($data): void {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('videoanswer', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Method process_videoanswer_submission.
     *
     * @param mixed $data Parameter data.
     * @return void Return value.
     */
    protected function process_videoanswer_submission($data): void {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->videoanswerid = $this->get_new_parentid('videoanswer');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        if (!$data->userid) {
            return;
        }

        $newitemid = $DB->insert_record('videoanswer_submissions', $data);
        $this->set_mapping('videoanswer_submission', $oldid, $newitemid, true);
    }

    /**
     * Method after_execute.
     *
     * @return void Return value.
     */
    protected function after_execute(): void {
        $this->add_related_files('mod_videoanswer', 'intro', null);
        $this->add_related_files('mod_videoanswer', 'submission', 'videoanswer_submission');
    }
}
