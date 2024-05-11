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
 * @copyright iplusacademy.org (www.iplusacademy.org)
 * @author  Renaat Debleu
 * @author  Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace block_online_users_map\privacy;

use core_privacy\local\request\{approved_contextlist, approved_userlist, contextlist, deletion_criteria, userlist, writer};
use core_privacy\local\request\{core_userlist_provider};
use core_privacy\local\request\plugin\provider as pluginprovider;
use core_privacy\local\metadata\collection;

/**
 * Privacy provider class.
 *
 * @package block_online_users_map
 * @copyright iplusacademy.org (www.iplusacademy.org)
 * @author  Renaat Debleu
 * @author  Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class provider implements core_userlist_provider, pluginprovider, \core_privacy\local\metadata\provider {
    /**
     * Returns information about how block_community stores its data.
     *
     * @param   collection     $collection The initialized collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $arr = [
            'userid' => 'privacy:metadata:block_online_users_map:userid',
            'lat' => 'privacy:metadata:block_online_users_map:lat',
            'lng' => 'privacy:metadata:block_online_users_map:lng',
            'city' => 'privacy:metadata:block_online_users_map:city',
            'country' => 'privacy:metadata:block_online_users_map:country', ];
        $collection->add_database_table('block_online_users_map', $arr, 'privacy:metadata:block_online_users_map');
        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int         $userid     The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = "SELECT id
                  FROM {context}
                 WHERE instanceid = :userid
                       AND contextlevel = :contextlevel";

        $contextlist = new \core_privacy\local\request\contextlist();
        $contextlist->set_component('blocks_online_users_map');
        $contextlist->add_from_sql($sql, ['userid' => $userid, 'contextlevel' => CONTEXT_USER]);
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
        if (count($contexts) > 0) {
            $context = reset($contexts);
            if ($context->contextlevel === CONTEXT_USER) {
                $user = $contextlist->get_user();
                if ($data = $DB->get_record('block_online_users_map', ['userid' => $user->id])) {
                    unset($data->id);
                    writer::with_context($context)->export_data([], $data);
                }
            }
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
        if ($context->contextlevel === CONTEXT_USER) {
            $DB->delete_records('block_online_users_map', ['userid' => $context->id]);
        }
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist    $userlist   The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if (is_a($context, \context_user::class)) {
            $userlist->add_from_sql('userid', "SELECT userid FROM {block_online_users_map}", []);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist       $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;
        $context = $userlist->get_context();
        if (is_a($context, \context_user::class)) {
            [$insql, $inparams] = $DB->get_in_or_equal($userlist->get_userids(), SQL_PARAMS_NAMED);
            $DB->delete_records_select('block_online_users_map', "userid $insql", $inparams);
        }
    }
}
