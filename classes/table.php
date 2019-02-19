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
 * @package     type_plugin
 * @author      Tom Dickman <tomdickman@catalyst-au.net>
 * @copyright   2019 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_apocalypse;

defined('MOODLE_INTERNAL') || die;

use flexible_table;
use moodle_recordset;
use moodle_url;
use html_writer;

class table extends flexible_table {

    protected $hasdata;

    /**
     * table constructor.
     *
     * @param string $uniqueid
     * @param \moodle_recordset $records The recordset data to add to table
     *
     * @throws \coding_exception
     */
    public function __construct(string $uniqueid, moodle_recordset $records) {

        // This is an abitrary date based on the statements from browser developers relating to "mid 2019".
        parent::__construct($uniqueid);
        $this->show_download_buttons_at(array(TABLE_P_BOTTOM));
        $this->init();
        $this->build_rows($records);

    }

    public function init() {
        $this->define_columns(array('category', 'coursefullname', 'component', 'name', 'html5'));
        $this->define_headers(array(
            get_string('category'),
            get_string("course"),
            get_string('activitytype', 'report_apocalypse'),
            get_string("activity"),
            get_string('dualmode', 'report_apocalypse')
        ));
    }

    /**
     * Build the row to be displayed from record.
     *
     * @param $record The record containing data to build row from
     *
     * @return array The data to add to table as a row
     * @throws \moodle_exception
     */
    public function build_row_from_record($record) {

        $courselink = html_writer::link(new moodle_url($record->courseurl), $record->coursefullname);
        $activitylink = html_writer::link(new moodle_url($record->activityurl), $record->activityname);
        $html5status = ($record->html5present) ? 'yes' : 'no';

        return array($record->category, $courselink, $record->type, $activitylink, $html5status);
    }

    /**
     * Build the table rows from moodle_recordset
     *
     * @param moodle_recordset $records The recordset of data to build table from.
     *
     * @throws \coding_exception
     */
    public function build_rows($records) {
        $this->hasdata = false;

        if($records->valid()) {
            foreach ($records as $record) {
                $this->hasdata = true;
                $this->add_data($this->build_row_from_record($record));
            }
        }
        $records->close();
        $this->add_notification_if_no_data();
    }

    /**
     * Check if we have any results and if not add a no records notification.
     *
     * @throws \coding_exception
     */
    protected function add_notification_if_no_data() {
        global $OUTPUT;

        if (!$this->hasdata) {
            $this->add_data(array($OUTPUT->notification(get_string('noflashobjectsfound', 'report_apocalypse'))));
        }
    }

}