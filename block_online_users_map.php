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

 /* Online Users Map block - reworking of the standard Moodle online users
 * block, but this displays the users on a Google map - using the location
 * given in the Moodle profile.
 * @author Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package block_online_users_map
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/online_users_map/lib.php');

class block_online_users_map extends block_base {

    public function init() {
        $this->title = get_string('title', 'block_online_users_map');
    }

    public function instance_allow_config() {
        return false;
    }

    public function has_config() {
        return true;
    }

    public function get_content() {
        global $CFG, $COURSE, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }
        if ($COURSE->id == SITEID) {
            if ($this->instance->visible) {
                $PAGE->requires->js(new moodle_url('https://www.google.com/jsapi'), true);
            }
            $this->content->text = gethtmlforblock();
        } else {
            $this->content->text = get_html_googlemap();
        }
        return $this->content;
    }

    public function cron() {
        update_users_locations();
        return true;
    }
}