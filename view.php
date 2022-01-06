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

// No login check
// @codingStandardsIgnoreLine
require('../../config.php');
require_once($CFG->dirroot . '/theme/iplus/lib.php');

$map = optional_param('map', 3, PARAM_INT);
$cou = optional_param('country', '', PARAM_TEXT);

$course = get_course(1);
$context = context_course::instance(1);
$syscontext = context_system::instance(0);
// TODO log.
$PAGE->set_url(new moodle_url('/blocks/online_users_map/view.php', array('map' => $map)));
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->requires->js(new moodle_url('https://www.google.com/jsapi', array('key' => $CFG->googlemapkey3)), true);

$PAGE->set_title($course->shortname);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->box_start('mod_introbox', 'pageintro');
echo html_writer::tag('h2', get_string('participants'));
echo $OUTPUT->box_end();

$rows = array();
$char = 'GeoChart';
$sty = 'min-width:100%;';
$opt = "'datalessRegionColor': '#fff', 'colorAxis': {'colors': ['#B5C202', '#106B52']}, 'backgroundColor':'transparent'";

if ($cou != '') {
    $cols = "{type: 'string'}, {type: 'number'}, { type: 'string', 'role': 'tooltip'}";
    if (strlen($cou) > 2) {
        $sql = "SELECT country, count(1) AS cnt FROM {user}
                WHERE country > '' AND suspended = 0 AND deleted = 0 GROUP BY country";
        if ($countries = $DB->get_records_sql($sql)) {
            foreach ($countries as $country) {
                $countrystr = addslashes_js(get_string($country->country, 'countries'));
                $rows[] = "{c:[{v:'$country->country'}, {v:$country->cnt}, {v:'$countrystr $country->cnt'}]}";
            }
            $rows = implode(',', $rows);
        }
        $opt .= ", 'region': '$cou'";
    } else {
        $cou = strtoupper($cou);
        $sql = "SELECT city, count(1) AS cnt FROM {user}
                WHERE city > '' AND suspended = 0 AND deleted = 0 AND country = ? GROUP BY city";
        if ($cities = $DB->get_records_sql($sql, array($cou))) {
            $countrystr = addslashes_js(get_string($cou, 'countries'));
            foreach ($cities as $city) {
                $citystr = addslashes_js(trim(preg_replace('/[0-9]+/', '', $city->city)));
                $rows[] = "{c:[{v:'$citystr'}, {v:$city->cnt}, {v:'$countrystr'}]}";
            }
            $rows = implode(',', $rows);
        }
        $opt .= ", 'region': '$cou', 'displayMode': 'markers'";
    }
} else if ($map === 1) {
    $cols = "{type: 'string'}, {type: 'number'}, { type: 'string', 'role': 'tooltip'}";
    $cou = strtoupper($cou);
    $sql = "SELECT city, count(1) AS cnt, country FROM {user} WHERE city > '' AND suspended = 0
                AND deleted = 0 GROUP BY city";
    if ($cities = $DB->get_records_sql($sql)) {
        foreach ($cities as $city) {
            $countryst = addslashes_js(get_string($city->country, 'countries'));
            $cityst = addslashes_js(trim(preg_replace('/[0-9]+/', '', $city->city)));
            $rows[] = "{c:[{v:'$cityst'}, {v:$city->cnt}, {v:'$countryst'}]}";
        }
        $rows = implode(',', $rows);
    }
    $opt .= ", 'region': 'world', 'displayMode': 'markers', 'sizeAxis': {'minValue': 1, 'minSize': 10, 'maxSize': 10}";
} else if ($map === 2) {
    $cols = "{type: 'string'}, {type: 'number'}, { type: 'string', 'role': 'tooltip'}";
    $sql = "SELECT country, count(1) AS cnt FROM {user}
            WHERE country > '' AND suspended = 0 AND deleted = 0 GROUP BY country";
    if ($countries = $DB->get_records_sql($sql)) {
        foreach ($countries as $country) {
            $countryst = addslashes_js(get_string($country->country, 'countries'));
            $rows[] = "{c:[{v:'$country->country'}, {v:$country->cnt}, {v:'$countryst $country->cnt'}]}";
        }
        $rows = implode(',', $rows);
    }
    $opt = "'datalessRegionColor': '#c2c3c3', 'colorAxis': {'colors': ['#B5C202', '#106B52']},";
    $opt .= "'backgroundColor':'transparent', 'region': 'world'";
} else if ($map === 3) {
    $char = 'Map';
    $cols = "{type: 'number'}, {type: 'number'}, { type: 'string', 'role': 'tooltip'}";
    $sql = "SELECT u.city, boumc.lat, boumc.lng FROM {user} u,  {block_online_users_map} boumc
            WHERE boumc.userid = u.id AND u.suspended = 0 AND u.deleted = 0 GROUP BY city";
    if ($cities = $DB->get_records_sql($sql, array(), 0, 400)) {
        foreach ($cities as $city) {
            $citystr = addslashes_js(trim(preg_replace('/[0-9]+/', '', $city->city)));
            $rows[] = "{c:[{v:$city->lat}, {v:$city->lng}, {v:'$citystr'}]}";
        }
        $rows = implode(',', $rows);
    }
    $opt = "'showTip': true, 'zoomLevel': 2, 'colorAxis': {'colors': ['#B5C202', '#106B52']}";
} else if ($map === 4) {
    $char = 'Map';
    $cols = "{type: 'number'}, {type: 'number'}, { type: 'string', 'role': 'tooltip'}";
    $sql = "SELECT u.city, boumc.lat, boumc.lng FROM {user} u,  {block_online_users_map} boumc
            WHERE boumc.userid = u.id AND u.suspended = 0 AND u.deleted = 0 GROUP BY city";
    if ($cities = $DB->get_records_sql($sql, array(), 0, 400)) {
        foreach ($cities as $city) {
            $citystr = addslashes_js(trim(preg_replace('/[0-9]+/', '', $city->city)));
            $rows[] = "{c:[{v:$city->lat}, {v:$city->lng}, {v:'$citystr'}]}";
        }
        $rows = implode(',', $rows);
    }
    $opt = "'showTip': true, 'mapType' : 'terrain', 'zoomLevel': 2, 'colorAxis': {'colors': ['#B5C202', '#106B52']}";
}
echo iplus_gchart($char, $sty, $cols, $rows, $opt);
echo $OUTPUT->footer();
