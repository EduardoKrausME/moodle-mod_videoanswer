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

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Class mod_videoanswer_mod_form.
 */
class mod_videoanswer_mod_form extends moodleform_mod {
    /**
     * Method definition.
     *
     * @return void Return value.
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement('text', 'name', get_string('name'), ['size' => 64]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('question', 'videoanswer'));

        $options = [
            30 => get_string('seconds', 'videoanswer', 30),
            60 => get_string('seconds', 'videoanswer', 60),
            120 => get_string('seconds', 'videoanswer', 120),
        ];
        $mform->addElement('select', 'timelimit', get_string('timelimit', 'videoanswer'), $options);
        $mform->setDefault('timelimit', 60);

        $mform->addElement('advcheckbox', 'allowretake', get_string('allowretake', 'videoanswer'));
        $mform->setDefault('allowretake', 1);

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
