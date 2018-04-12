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

require_once($CFG->dirroot . '/blocks/online_users_map/lib.php');
require_once($CFG->dirroot . '/comment/lib.php');

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

    /**
     * Check the exporting of comments for a user id in a context.
     */
    public function test_export_maps() {
        $this->resetAfterTest(true);
        
        $context = context_system::instance();
        $user1 = new stdClass();
        $user1->country = 'AU';
        $user1->city = 'Perth';
        $user1 = self::getDataGenerator()->create_user($user1);
        $user2 = new stdClass();
        $user2->country = 'BE';
        $user2->city = 'Brussel';
        $user2 = self::getDataGenerator()->create_user($user2);
        update_users_locations();
        
        $this->export_context_data_for_user($user1->id, $context, 'block_online_users_map');
        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $this->export_context_data_for_user($user2->id, $context, 'block_online_users_map');
        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Tests the deletion of all comments in a context.
     */
    public function test_delete_maps_for_all_users_in_context() {
        $this->resetAfterTest();

        $context = context_system::instance();
        $user1 = new stdClass();
        $user1->country = 'AU';
        $user1->city = 'Perth';
        $user1 = self::getDataGenerator()->create_user($user1);
        $user2 = new stdClass();
        $user2->country = 'BE';
        $user2->city = 'Brussel';
        $user2 = self::getDataGenerator()->create_user($user2);
        update_users_locations();
        
        
        
        // Delete only for the first context. All records in the comments table for this context should be removed.
        //\core_comment\privacy\provider::delete_comments_for_all_users_in_context($coursecontext1);
        // No records left here.
        //$this->assertCount(0, $comment1->get_comments());
        // All of the records are left intact here.
        //$this->assertCount(2, $comment2->get_comments());

    }

    /**
     * Tests deletion of comments for a specified user and contexts.
     */
    public function test_delete_maps_for_user() {
        $this->resetAfterTest();

        $context = context_system::instance();
        $user1 = new stdClass();
        $user1->country = 'AU';
        $user1->city = 'Perth';
        $user1 = self::getDataGenerator()->create_user($user1);
        $user2 = new stdClass();
        $user2->country = 'BE';
        $user2->city = 'Brussel';
        $user2 = self::getDataGenerator()->create_user($user2);
        update_users_locations();
        
        // Delete the data for user 1.
        $list = new core_privacy\tests\request\approved_contextlist($user1, 'block_online_users_map', []);
        \block_online_users_map\privacy\provider::delete_data_for_user($list);
    }
}
