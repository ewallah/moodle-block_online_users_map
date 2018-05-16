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
 * Privacy tests for block_online_users_map.
 *
 * @package    block_online_users_map
 * @category   test
 * @copyright  2018 Renaat Debleu <rdebleu@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use \core_privacy\tests\provider_testcase;

/**
 * Unit tests for block_online_users_map/classes/privacy/policy
 *
 * @package    block_online_users_map
 * @category   test
 * @copyright  2018 Renaat Debleu <rdebleu@eWallah.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_online_users_map_privacy_testcase extends provider_testcase {

    /** @var user1 first user */
    private $user1;
    /** @var user2 second user */
    private $user2;

    /**
     * Basic setup for these tests.
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest(true);
        $user = new stdClass();
        $user->country = 'AU';
        $user->city = 'Perth';
        $this->user1 = self::getDataGenerator()->create_user($user);
        $loc = new stdClass();
        $loc->userid = $this->user1->id;
        $loc->lat = 31.95;
        $loc->lng = 115.86;
        $loc->country = 'AU';
        $loc->city = 'Perth';
        $DB->insert_record('block_online_users_map', $loc);
        $user = new stdClass();
        $user->country = 'BE';
        $user->city = 'Brussel';
        $this->user2 = self::getDataGenerator()->create_user($user);
        $loc = new stdClass();
        $loc->userid = $this->user2->id;
        $loc->lat = 50.85;
        $loc->lng = 4.3(;
        $loc->country = 'AU';
        $loc->city = 'Perth';
        $DB->insert_record('block_online_users_map', $loc);
    }

    /**
     * Test returning metadata.
     */
    public function test_get_metadata() {
        $collection = new \core_privacy\local\metadata\collection('block_online_users_map');
        $collection = \block_online_users_map\privacy\provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        $contextlist = \block_online_users_map\privacy\provider::get_contexts_for_userid($this->user1->id);
        $this->assertNotEmpty($contextlist);
        $contextlist = \block_online_users_map\privacy\provider::get_contexts_for_userid($this->user2->id);
        $this->assertNotEmpty($contextlist);
    }

    /**
     * Check the exporting of locations for a user.
     */
    public function test_export_maps() {
        $context = context_user::instance($this->user1->id);
        $this->export_context_data_for_user($this->user1->id, $context, 'block_online_users_map');
        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $context = context_user::instance($this->user2->id);
        $this->export_context_data_for_user($this->user2->id, $context, 'block_online_users_map');
        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Tests the deletion of all locations.
     */
    public function test_delete_maps_for_all_users_in_context() {
        $context = context_user::instance($this->user1->id);
        \block_online_users_map\privacy\provider::delete_data_for_all_users_in_context($context);
        $list1 = new core_privacy\tests\request\approved_contextlist($this->user1, 'block_online_users_map', []);
        $list2 = new core_privacy\tests\request\approved_contextlist($this->user2, 'block_online_users_map', []);
        $this->assertEmpty($list1);
        $this->assertEmpty($list2);
    }

    /**
     * Tests deletion of locations for a specified user.
     */
    public function test_delete_maps_for_user() {
        $context = context_user::instance($this->user1->id);
        $list = new core_privacy\tests\request\approved_contextlist($this->user1, 'block_online_users_map', []);
        \block_online_users_map\privacy\provider::delete_data_for_user($list);
        $this->export_context_data_for_user($this->user1->id, $context, 'block_online_users_map');
        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $context = context_user::instance($this->user2->id);
        $this->export_context_data_for_user($this->user2->id, $context, 'block_online_users_map');
        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
    }
}