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
 * Other tests for block_online_users_map.
 *
 * @package    block_online_users_map
 * @category   test
 * @copyright iplusacademy.org (www.iplusacademy.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_online_users_map;

/**
 * Unit tests for block_online_users_map/classes/privacy/policy
 *
 * @package    block_online_users_map
 * @category   test
 * @copyright iplusacademy.org (www.iplusacademy.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class other_test extends \advanced_testcase {
    /** @var user1 first user */
    private $user1;
    /** @var user2 second user */
    private $user2;
    /** @var block online users map */
    private $block;
    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        global $CFG;
        require_once($CFG->dirroot . '/blocks/online_users_map/lib.php');
        parent::setUp();
        $this->resetAfterTest(true);
        $user = new \stdClass();
        $user->country = 'AU';
        $user->city = 'Perth';
        $user->lastip = '8.8.8.8';
        $this->user1 = self::getDataGenerator()->create_user($user);
        $user = new \stdClass();
        $user->country = 'BE';
        $user->city = 'BruSsel';
        $this->user2 = self::getDataGenerator()->create_user($user);
        update_users_locations();
        update_users_locations();
        $regions = ['region-a'];
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $page->set_pagetype('page-type');
        $page->set_subpage('');
        $page->set_url(new \moodle_url('/'));
        $blockmanager = new \block_manager($page);
        $blockmanager->add_regions($regions, false);
        $blockmanager->set_default_region($regions[0]);
        $blockmanager->add_block('online_users_map', 'region-a', 0, false);
        $blockmanager->load_blocks();
        $blocks = $blockmanager->get_blocks_for_region('region-a');
        $this->block = $blocks[0];
    }

    /**
     * Test basic block.
     * #[CoversClass(block_online_users_map)]
     */
    public function test_block_basic(): void {
        $this->assertFalse($this->block->instance_allow_multiple());
        $this->assertTrue($this->block->has_config());
        $this->assertFalse($this->block->instance_allow_config());
        $this->assertNotEmpty($this->block->title);
        $this->assertCount(2, $this->block->applicable_formats());
        $this->assertNotEquals('', $this->block->get_content()->text);
        $this->assertEquals('', $this->block->get_content()->footer);

        $this->setAdminUser();
        $this->assertFalse($this->block->instance_allow_multiple());
        $this->assertTrue($this->block->has_config());
        $this->assertFalse($this->block->instance_allow_config());
        $this->assertNotEmpty($this->block->title);
        $this->assertNotEmpty($this->block->applicable_formats());
        $this->assertNotEquals('', $this->block->get_content()->text);
        $this->assertEquals('', $this->block->get_content()->footer);
    }

    /**
     * Test other course.
     * #[CoversClass(block_online_users_map)]
     */
    public function test_other_course(): void {
        $course = self::getDataGenerator()->create_course();
        $regions = ['region-a'];
        $page = new \moodle_page();
        $page->set_context(\context_course::instance($course->id));
        $page->set_pagetype('page-type');
        $page->set_subpage('');
        $page->set_url(new \moodle_url('/'));
        $blockmanager = new \block_manager($page);
        $blockmanager->add_regions($regions, false);
        $blockmanager->set_default_region($regions[0]);
        $blockmanager->add_block('online_users_map', 'region-a', 0, false);
        $blockmanager->load_blocks();
        $blocks = $blockmanager->get_blocks_for_region('region-a');
        $block = $blocks[0];
        $this->assertNotEmpty($block->title);
        $this->assertNotEquals('', $block->get_content()->text);
        $this->assertEquals('', $block->get_content()->footer);
    }

    /**
     * Test cron.
     * #[CoversClass(block_online_users_map\task\cron_task)]
     */
    public function test_cron_task(): void {
        self::getDataGenerator()->create_user([
            'firstname' => 'fIrStName',
            'lastname' => 'LASTNAME',
            'city' => 'city',
            'email' => 'tst@iplussolutions.org',
        ]);
        $tsk = new \block_online_users_map\task\cron_task();
        $this->assertEquals('block online users task', $tsk->get_name());
        $this->assertEquals(true, $tsk->execute());
    }
}
