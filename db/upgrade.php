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

defined('MOODLE_INTERNAL') || die();

function xmldb_block_online_users_map_upgrade($oldversion) {

    global $DB;

    if ($oldversion < 2007110101) {
        // Add new config entries.
        $setting = new object();
        $setting->name = "block_online_users_map_centre_lat";
        $setting->value = 17.383;
        $DB->insert_record("config", $setting);

        $setting = new object();
        $setting->name = "block_online_users_map_centre_lng";
        $setting->value = 11.183;
        $DB->insert_record("config", $setting);

        $setting = new object();
        $setting->name = "block_online_users_map_init_zoom";
        $setting->value = 0;
        $DB->insert_record("config", $setting);
    }

    if ($oldversion < 2008011400) {
        // Add new config entries.
        $setting = new object();
        $setting->name = "block_online_users_map_debug";
        $setting->value = 0;
        $DB->insert_record("config", $setting);
    }

    if ($oldversion < 2008030600) {
        // Add new config entries.
        $setting = new object();
        $setting->name = "block_online_users_map_show_offline";
        $setting->value = 0;
        $DB->insert_record("config", $setting);

        $setting = new object();
        $setting->name = "block_online_users_map_show_offline_role";
        $setting->value = 0;
        $DB->insert_record("config", $setting);
    }

    if ($oldversion < 2008052700) {
        // Add new config entries.
        $setting = new object();
        $setting->name = "block_online_users_map_centre_user";
        $setting->value = 0;
        $DB->insert_record("config", $setting);
    }

    if ($oldversion < 2008080700) {
        // Add new config entries.
        $setting = new object();
        $setting->name = "block_online_users_map_update_limit";
        $setting->value = 100;
        $DB->insert_record("config", $setting);
    }

    if ($oldversion < 2010051900) {
        // Add new config entries.
        $setting = new object();
        $setting->name = "block_online_users_map_has_names";
        $setting->value = 1;
        $DB->insert_record("config", $setting);
    }

    if ($oldversion < 2010122700) {
        $setting = new object();
        $setting->name = "block_online_users_map_type";
        $setting->value = 'osm';
        $DB->insert_record("config", $setting);

        // Block savepoint reached.
        upgrade_block_savepoint(true, 2010122700, 'online_users_map');
    }

    if ($oldversion < 2016112000) {
        // Block savepoint reached.
        upgrade_block_savepoint(true, 2016112000, 'online_users_map');
    }
    return true;
}
