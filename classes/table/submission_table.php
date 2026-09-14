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
namespace mod_videoanswer\table;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * Class submission_table.
 */
class submission_table extends \table_sql {
    /**
     * Property context.
     *
     * @var \context_module
     */
    private \context_module $context;
    /**
     * Property courseid.
     *
     * @var int
     */
    private int $courseid;

    /**
     * Method __construct.
     *
     * @param string $uniqueid Parameter uniqueid.
     * @param \context_module $context Parameter context.
     * @param int $courseid Parameter courseid.
     * @param int $videoanswerid Parameter videoanswerid.
     */
    public function __construct(string $uniqueid, \context_module $context, int $courseid, int $videoanswerid) {
        parent::__construct($uniqueid);

        $this->context = $context;
        $this->courseid = $courseid;

        $columns = ['student', 'duration', 'submittedat', 'video'];
        $headers = [
            get_string('student', 'videoanswer'),
            get_string('duration', 'videoanswer'),
            get_string('submittedat', 'videoanswer'),
            get_string('video', 'videoanswer'),
        ];
        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->sortable(true, 'submittedat', SORT_DESC);
        $this->no_sorting('student');
        $this->no_sorting('video');
        $this->collapsible(false);

        $fields = 's.id, s.userid, s.duration, s.mimetype, s.filesize, s.timecreated, s.timemodified AS submittedat,
                   u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename';
        $from = '{videoanswer_submissions} s JOIN {user} u ON u.id = s.userid';
        $where = 's.videoanswerid = :videoanswerid';
        $this->set_sql($fields, $from, $where, ['videoanswerid' => $videoanswerid]);
    }

    /**
     * Method col_student.
     *
     * @param mixed $row Parameter row.
     * @return string Return value.
     */
    public function col_student($row): string {
        $url = new \moodle_url('/user/view.php', [
            'id' => $row->userid,
            'course' => $this->courseid,
        ]);
        return \html_writer::link($url, fullname($row));
    }

    /**
     * Method col_duration.
     *
     * @param mixed $row Parameter row.
     * @return string Return value.
     */
    public function col_duration($row): string {
        return format_float(((int)$row->duration) / 1000, 1) . ' s';
    }

    /**
     * Method col_submittedat.
     *
     * @param mixed $row Parameter row.
     * @return string Return value.
     */
    public function col_submittedat($row): string {
        return userdate((int)$row->submittedat);
    }

    /**
     * Method col_video.
     *
     * @param mixed $row Parameter row.
     * @return string Return value.
     */
    public function col_video($row): string {
        $url = \mod_videoanswer\submission_service::get_file_url($this->context, $row);
        if (!$url) {
            return '-';
        }
        return \html_writer::tag('video', '', [
            'controls' => 'controls',
            'preload' => 'metadata',
            'playsinline' => 'playsinline',
            'src' => $url->out(false),
            'style' => 'width:280px;max-width:100%;border-radius:.5rem;background:#111',
        ]);
    }
}
