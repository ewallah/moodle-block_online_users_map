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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_online_users_map_timetosee',
        get_string('timetosee', 'block_online_users_map'),
        get_string('configtimetosee', 'block_online_users_map'), 5, PARAM_INT));
    $settings->add(new admin_setting_configselect('block_online_users_map_type',
        get_string('type', 'block_online_users_map'),
        get_string('configtype', 'block_online_users_map'), 0, ['google' => 'Google Maps']));
    $settings->add(new admin_setting_configtext('block_online_users_map_centre_lat',
        get_string('centrelat', 'block_online_users_map'),
        get_string('configcentrelat', 'block_online_users_map'), 0, PARAM_NUMBER));
    $settings->add(new admin_setting_configtext('block_online_users_map_centre_lng',
        get_string('centrelng', 'block_online_users_map'),
        get_string('configcentrelng', 'block_online_users_map'), 0, PARAM_NUMBER));
    $settings->add(new admin_setting_configtext('block_online_users_map_init_zoom',
        get_string('zoomlevel', 'block_online_users_map'),
        get_string('configzoomlevel', 'block_online_users_map'), 0, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('block_online_users_map_debug',
        get_string('debug', 'block_online_users_map'),
        get_string('configdebug', 'block_online_users_map'), 1));
    $settings->add(new admin_setting_configcheckbox('block_online_users_map_show_offline',
        get_string('offline', 'block_online_users_map'),
        get_string('configoffline', 'block_online_users_map'), 1));
    $settings->add(new admin_setting_configcheckbox('block_online_users_map_centre_user',
        get_string('centreuser', 'block_online_users_map'),
        get_string('configcentreuser', 'block_online_users_map'), 1));
    $settings->add(new admin_setting_configcheckbox('block_online_users_map_has_names',
        get_string('namesonmap', 'block_online_users_map'),
        get_string('confignamesonmap', 'block_online_users_map'), 1));
    $settings->add(new admin_setting_configtext('block_online_users_map_update_limit',
        get_string('updatelimit', 'block_online_users_map'),
        get_string('configupdatelimit', 'block_online_users_map'), 100, PARAM_INT));
}
