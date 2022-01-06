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
 * Online Users Map block - Customised online users map based on work of Alex Little
 *
 * @package   block_online_users_map
 * @copyright 2018 onwards iplusacademy  {@link https://www.iplusacademy.org}
 * @author    Renaat Debleu (www.ewallah.net)
 * @author    Alex Little
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();

// No login check
// @codingStandardsIgnoreLine
require_once($CFG->dirroot . '/blocks/online_users_map/lib.php');

/**
 * Online Users Map block - Customised online users map based on work of Alex Little
 *
 * @package   block_online_users_map
 * @copyright 2018 onwards iplusacademy  {@link https://www.iplusacademy.org}
 * @author    Renaat Debleu (www.ewallah.net)
 * @author    Alex Little
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class block_online_users_map extends block_base {

    /**
     * Core function used to initialize the block.
     */
    public function init() {
        $this->title = get_string('title', 'block_online_users_map');
    }

    /**
     * Has instance configuration
     * @return boolean
     */
    public function instance_allow_config() {
        return false;
    }

    /**
     * Set the applicable formats for this block to all
     * @return array
     */
    public function applicable_formats() {
        return ['site' => true, 'site-index' => true];
    }

    /**
     * Has configuration
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Gets the content for this block
     * return string
     */
    public function get_content() {
        global $COURSE;

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
                $this->page->requires->js(new moodle_url('https://www.google.com/jsapi'), true);
            }
        } else {
            $this->content->text = get_html_googlemap();
        }
        return $this->content;
    }
}
