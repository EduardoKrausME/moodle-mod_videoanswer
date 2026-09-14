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
 * Class backup_videoanswer_activity_structure_step.
 */
class backup_videoanswer_activity_structure_step extends backup_activity_structure_step {
    /**
     * Method define_structure.
     *
     * @return mixed Return value.
     */
    protected function define_structure() {
        $videoanswer = new backup_nested_element('videoanswer', ['id'], [
            'name', 'intro', 'introformat', 'timelimit', 'allowretake', 'timecreated', 'timemodified',
        ]);

        $submissions = new backup_nested_element('submissions');
        $submission = new backup_nested_element('submission', ['id'], [
            'userid', 'duration', 'mimetype', 'filesize', 'timecreated', 'timemodified',
        ]);

        $videoanswer->add_child($submissions);
        $submissions->add_child($submission);

        $videoanswer->set_source_table('videoanswer', ['id' => backup::VAR_ACTIVITYID]);
        if ($this->get_setting_value('userinfo')) {
            $submission->set_source_table('videoanswer_submissions', ['videoanswerid' => backup::VAR_PARENTID]);
            $submission->annotate_ids('user', 'userid');
        }

        $videoanswer->annotate_files('mod_videoanswer', 'intro', null);
        $submission->annotate_files('mod_videoanswer', 'submission', 'id');

        return $this->prepare_activity_structure($videoanswer);
    }
}
