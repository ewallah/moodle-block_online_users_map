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
 * @package block_online_users_map
 * @copyright  2018 Renaat Debleu <rdebleu@eWallah.net>
 * @author  Renaat Debleu
 * @author  Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace block_online_users_map\privacy;

use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\metadata\collection;

/**
 * Privacy provider class.
 *
 * @package block_online_users_map
 * @copyright  2018 Renaat Debleu <rdebleu@eWallah.net>
 * @author  Renaat Debleu
 * @author  Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class provider implements \core_privacy\local\metadata\provider, \core_privacy\local\request\plugin\provider {

    /**
     * Returns information about how block_community stores its data.
     *
     * @param   collection     $collection The initialized collection to add items to.
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
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid) : contextlist {
        $contextlist = new \core_privacy\local\request\contextlist();
        // The block_online_users_map data is associated at system context level.
        $sql = "SELECT id FROM {context} WHERE id = 1";
        $contextlist->add_from_sql($sql, []);
        return $contextlist;
    }

    /**
     * Export all user data for the specified user using the system context level.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $contexts = $contextlist->get_contexts();
        if (count($contexts) == 0) {
            return;
        }
        $context = reset($contexts);
        
        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }
        $user = $contextlist->get_user();
        if ($data = $DB->get_record('block_online_users_map', ['userid' => $user->id])) {
            unset($data->id);
            $context = \context_system::instance();
            writer::with_context($context)->export_data([], $data);
        }
    }

    /**
     * Delete all user data for the specified user.
     *
     * @param   approved_contextlist $contextlist  The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $DB->delete_records('block_online_users_map', ['userid' => $contextlist->get_user()->id]);
    }

    /**
     * Delete all data for all users in global system context.
     *
     * @param   context $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }
        $DB->execute("DELETE FROM {block_online_users_map}");
    }

}