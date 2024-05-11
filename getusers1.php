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
 * @copyright iplusacademy.org (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @author Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

// No login check
// @codingStandardsIgnoreLine
require_once("../../config.php");
require_once($CFG->dirroot . '/blocks/online_users_map/lib.php');

$callback = optional_param('callback', '', PARAM_ALPHA);

// Round to nearest 100 seconds for better query cache.
$timefrom = 100 * floor((time() - gettimetoshowusers()) / 100);

// Get context so we can check capabilities.
$context = context_course::instance($COURSE->id);

// Calculate if we are in separate groups.
$isseparategroups = ($COURSE->groupmode == SEPARATEGROUPS
                     && $COURSE->groupmodeforce
                     && !has_capability('moodle/site:accessallgroups', $context));

// Get the user current group.
$currentgroup = $isseparategroups ? get_and_set_current_group($COURSE, groupmode($COURSE)) : null;

$groupmembers = "";
$groupselect = "";
$users = [];

$counter = 0;
// Now if the block setting to show offline users to is get then add the offline users to the returned content.
if (isset($CFG->block_online_users_map_show_offline) && $CFG->block_online_users_map_show_offline == 1) {
    if ($currentgroup !== null) {
        $groupmembers = ",  {groups_members} gm ";
        $groupselect = " AND u.id = gm.userid AND gm.groupid = '$currentgroup'";
    }

    if ($COURSE->id == SITEID) {
        // Site-level.
        $select = "SELECT
                      u.id, u.username, u.firstname, u.lastname, u.city, MAX(u.lastaccess) as lastaccess, boumc.lat, boumc.lng ";
        $from = "FROM {user} u,
                      {block_online_users_map} boumc
                      $groupmembers ";
        $where = "WHERE u.lastaccess <= $timefrom
                    AND boumc.userid = u.id
                  $groupselect ";
        $order = "ORDER BY lastaccess DESC ";
    } else {
        // Course-level.
        $courseselect = "AND ul.courseid = '" . $COURSE->id . "'";
        $select = "SELECT u.id, u.city, MAX(u.lastaccess) as lastaccess, boumc.lat, boumc.lng ";
        $from = "FROM {user_lastaccess} ul,
                      {user} u,
                      {block_online_users_map} boumc
                      $groupmembers ";
        $where = "WHERE ul.timeaccess <= $timefrom
                   AND u.id = ul.userid
                   AND ul.courseid = $COURSE->id
                   AND boumc.userid = u.id
                   $groupselect ";
        $order = "ORDER BY lastaccess DESC ";
    }
    $groupby = "GROUP BY u.id, u.username, u.firstname, u.lastname, u.city, u.picture, boumc.lat, boumc.lng ";
    $sqlwithll = $select . $from . $where . $groupby . $order;

    $pcontext = $context->get_parent_context_ids(true);
    if ($pusers = $DB->get_records_sql($sqlwithll, [], 0, 500)) {
        // We'll just take the most recent 500 maximum.
        foreach ($pusers as $puser) {
            $puser->fullname = $puser->city;
            unset($puser->id);
            unset($puser->username);
            unset($puser->lastname);
            unset($puser->firstname);
            unset($puser->lastaccess);
            $puser->online = "false";
            $users[$counter] = $puser;
            $counter++;
        }
    }
}

// Add this to the SQL to show only group users.
if ($currentgroup !== null) {
    $groupmembers = ",  {groups_members} gm ";
    $groupselect = " AND u.id = gm.userid AND gm.groupid = '$currentgroup'";
}

if ($COURSE->id == SITEID) {
    // Site-level.
    $select = "SELECT u.id, u.city, MAX(u.lastaccess) as lastaccess, boumc.lat, boumc.lng ";
    $from = "FROM {user} u,
            {block_online_users_map} boumc
            $groupmembers ";
    $where = "WHERE u.lastaccess > $timefrom
              AND boumc.userid = u.id
              $groupselect ";
    $order = "ORDER BY lastaccess DESC ";
} else {
    // Course-level.
    $courseselect = "AND ul.courseid = '" . $COURSE->id . "'";
    $select = "SELECT u.id, u.city, MAX(u.lastaccess) as lastaccess, boumc.lat, boumc.lng ";
    $from = "FROM {user_lastaccess} ul,
                  {user} u,
                  {block_online_users_map} boumc
                  $groupmembers ";
    $where = "WHERE ul.timeaccess > $timefrom
               AND u.id = ul.userid
               AND ul.courseid = $COURSE->id
               AND boumc.userid = u.id
               $groupselect ";
    $order = "ORDER BY lastaccess DESC ";
}
$groupby = "GROUP BY u.id, u.city, u.picture, boumc.lat, boumc.lng ";
$sqlwithll = $select . $from . $where . $groupby . $order;
$pcontext = $context->get_parent_context_ids(true);

// We'll just take the most recent 500 maximum.
if ($pusers = $DB->get_records_sql($sqlwithll, [], 0, 500)) {
    foreach ($pusers as $puser) {
        if ($CFG->block_online_users_map_has_names) {
            $puser->fullname = fullname($puser);
        } else {
            $puser->fullname = $puser->city;
        }
        unset($puser->id);
        unset($puser->lastaccess);
        $puser->online = "true";
        $users[$counter] = $puser;
        $counter++;
    }
}

header("Content-type: text/javascript");
echo $callback . "(" . json_encode($users) . ")";
