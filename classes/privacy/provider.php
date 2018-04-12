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
 * Privacy Subsystem implementation for block_online_users_map.
 *
 * @author  Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package block_online_users_map
 */

namespace block_online_users_map\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\helper;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem implementation for block_online_users_map.
 *
 * @author  Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package block_online_users_map
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {

    /**
     * Returns information about how block_online_users_map stores its data.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) : collection {
        $arr = [
            'userid'  => 'privacy:metadata:block_online_users_map:userid',
            'lat'     => 'privacy:metadata:block_online_users_map:lat',
            'lng'     => 'privacy:metadata:block_online_users_map:lng',
            'city'    => 'privacy:metadata:block_online_users_map:city',
            'country' => 'privacy:metadata:block_online_users_map:country'];
        $collection->add_database_table('block_online_users_map', $arr, 'privacy:metadata:block_online_users_map');
        return $collection;
    }

    /**
     * Get empty list of contexts.
     *
     * @param   int           $userid       The user to search.
     * @return  contextlist   $contextlist  The list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        return new \core_privacy\local\request\contextlist();
    }
    
    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;
        $user = $contextlist->get_user();
        if ($data = $DB->get_record('block_online_users_map', ['userid' => $user->id])) {
            unset($data->id);
            $context = \context_system::instance();
            writer::with_context($context)->export_data([], $data);
        }
    }
    
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
    }
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $user = $contextlist->get_user();
        return $DB->delete_records('block_online_users_map', ['userid' => $user->id]);
    }  
}
