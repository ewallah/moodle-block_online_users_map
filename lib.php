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
 * @author Alex Little
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/lib/datalib.php');

/**
 * Get the current user location.
 *
 * @return string location
 */
function currentuserlocation() {
    global $CFG, $USER, $DB;
    $txt = '';
    if ($coords = $DB->get_record('block_online_users_map', ['userid' => $USER->id], 'lat, lng')) {
        $txt .= 'var lat = ' . $coords->lat . ';';
        $txt .= 'var lon = ' . $coords->lng . ';';
    } else {
        $txt .= 'var lat = ' . $CFG->block_online_users_map_centre_lat . ';';
        $txt .= 'var lon = ' . $CFG->block_online_users_map_centre_lng . ';';
    }
    $txt .= 'var zoom = ' . $CFG->block_online_users_map_init_zoom . ';';
    return $txt;
}

/**
 * Get the user cities.
 *
 * @return string json encoded string
 */
function getusercountries() {
    global $DB;
    $arr = [];
    $sql = "SELECT country, count(1) AS cnt FROM {user}
            WHERE country > '' AND suspended = 0 AND deleted = 0
            GROUP BY country";
    if ($countries = $DB->get_records_sql($sql)) {
        foreach ($countries as $country) {
            $arr[] = [$country->country, $country->cnt, get_string($country->country, 'countries')];
        }
    }
    return json_encode($arr, JSON_HEX_APOS | JSON_NUMERIC_CHECK);
}

/**
 * Get the user cities.
 * @param int $limit
 * @return string json encoded string
 */
function getusercities($limit = 1500) {
    global $DB;
    $arr = [];
    if ($cities = $DB->get_records_sql("SELECT city, count(1) AS cnt, country FROM {user} WHERE city > '' AND suspended = 0
                AND deleted = 0 GROUP BY city", [], 0, $limit)) {
        foreach ($cities as $city) {
            $countrystr = get_string($city->country, 'countries');
            $citystr = preg_replace('/[0-9]+/', '', $city->city);
            $arr[] = [trim($citystr), $city->cnt, $countrystr];
        }
    }
    return json_encode($arr, JSON_HEX_APOS | JSON_NUMERIC_CHECK);
}

/**
 * Get the user countries and cities.
 *
 * @param string $country
 * @return string json encoded string
 */
function getcountrycities($country = 'NL') {
    global $DB;
    $arr = [];
    if ($cities = $DB->get_records_sql("SELECT city, count(1) AS cnt FROM {user} WHERE city > '' AND suspended = 0
                AND deleted = 0 AND country = ? GROUP BY city", [$country])) {
        $countrystr = get_string($country, 'countries');
        foreach ($cities as $city) {
            $citystr = preg_replace('/[0-9]+/', '', $city->city);
            $arr[] = [$citystr, $city->cnt, $countrystr];
        }
    }
    return json_encode($arr, JSON_HEX_APOS | JSON_NUMERIC_CHECK);
}

/**
 * Get the user locations.
 *
 * @param int $limit
 * @return string json encoded string
 */
function getuserlocations($limit = 1500) {
    global $DB;
    $arr = [];
    $arr[] = ['lat', 'lon', 'City'];
    if ($users = $DB->get_records_sql("SELECT u.city, boumc.lat, boumc.lng
                                       FROM {user} u,  {block_online_users_map} boumc
                                       WHERE boumc.userid = u.id AND u.suspended = 0
                AND u.deleted = 0 GROUP BY city", [], 0, $limit)) {
        foreach ($users as $user) {
            $arr[] = [$user->lat, $user->lng, $user->city];
        }
    }
    return json_encode($arr, JSON_HEX_APOS | JSON_NUMERIC_CHECK);
}

/**
 * Generate the HTML content for the google map
 *
 * @return string HTML string to display google map
 */
function get_html_googlemap() {
    global $CFG;
    $str = "<script src='https://maps.googleapis.com/maps/api/js?&key=".$CFG->googlemapkey3."' type='text/javascript'></script>";
    $str .= "<div id='block_online_users_googlemap'></div>";
    $str .= "<script type='text/javascript' src='" . $CFG->wwwroot;
    $str .= "/blocks/online_users_map/online_users_map.php' defer='defer'></script>";
    return $str;
}

/**
 * Updates the lat/lng for users
 * @uses $CFG,$DB
 */
function update_users_locations() {
    global $CFG, $DB;
    // Get all the users without a lat/lng.
    $sql = "SELECT u.id, u.city, u.lastip, u.country, u.timezone, boumc.id AS b_id, u.firstname, u.lastname
                FROM {user} u
                LEFT OUTER JOIN {block_online_users_map} boumc
                ON  u.id = boumc.userid
                WHERE (boumc.id IS NULL
                OR u.city != boumc.city
                OR u.country != boumc.country)
                AND u.city != ''
                AND u.suspended = 0
                AND u.deleted = 0";

    $results = $DB->get_records_sql($sql, [], 0, 5);
    if (!$results) {
        return true;
    }
    if ((PHPUNIT_TEST) || (defined('BEHAT_TEST') && BEHAT_TEST) || defined('BEHAT_SITE_RUNNING')) {
       return true;
    }
    // return true;
    $txt = '';
    // Loop through results and get location for each user.
    foreach ($results as $user) {
        // Get the coordinates.
        $city = preg_replace('/[0-9]+/', '', $user->city);
        $response = geturlcontent("http://othello.ws.geonames.org",
                    "/search?maxRows=1&q=" . urlencode($city) . "&country=" . urlencode($user->country));
        if ($xml = simplexml_load_string($response)) {
            $boumc = new stdClass;
            if (isset($xml->geoname->lat)) {
                $boumc->userid = $user->id;
                $boumc->lat = (float)$xml->geoname->lat;
                $boumc->lng = (float)$xml->geoname->lng;
                $boumc->city = $user->city;
                $boumc->country = $user->country;

                // If existing record from block_online_users_map then update.
                if (isset($user->b_id)) {
                    $boumc->id = $user->b_id;
                    $DB->update_record("block_online_users_map", $boumc);
                    $txt .= "\n" . $CFG->wwwroot . '/user/edit.php?id=' . $user->id;
                    $txt .= "\nLocation updated for\n";
                    $country = get_string($user->country, 'countries');
                    $txt .= $user->firstname . " " . $user->lastname . ": " .$user->city . " - " . $country;
                    $txt .= "\n" . $user->lastip;
                } else {
                    $DB->insert_record("block_online_users_map", $boumc);
                    $txt .= "\n" . $CFG->wwwroot . '/user/edit.php?id=' . $user->id;
                    $txt .= "\nLocation added for\n";
                    $country = get_string($user->country, 'countries');
                    $name = $user->firstname . " " . $user->lastname;
                    $txt .= $name . ": " .$user->city . " - " . $country;
                    $txt .= "\n" . $user->lastip;
                    $names = explode(" ", $name);
                    foreach ($names as $name) {
                        $arr = ['firstname' => $name, 'country' => $user->country];
                        if ($candidates = $DB->get_records('user', $arr, 'id, firstname, lastname')) {
                            foreach ($candidates as $candidate) {
                                if ($candidate->id === $user->id) {
                                    continue;
                                }
                                $txt .= "\n" . $CFG->wwwroot . '/user/edit.php?id=' . $candidate->id;
                                $txt .= "\nPossible duplicate: ";
                                $txt .= $candidate->firstname . " " . $candidate->lastname;
                            }
                        }
                        $arr = ['lastname' => $name, 'country' => $user->country];
                        if ($candidates = $DB->get_records('user', $arr, 'id, firstname, lastname')) {
                            foreach ($candidates as $candidate) {
                                if ($candidate->id === $user->id) {
                                    continue;
                                }
                                $txt .= "\n" . $CFG->wwwroot . '/user/edit.php?id=' . $candidate->id;
                                $txt .= "\nPossible duplicate: ";
                                $txt .= $candidate->firstname . " " . $candidate->lastname;
                            }
                        }
                    }
                    if ($user->lastip != '' and $jsonresponse = file_get_contents('http://ip-api.com/json/' . $user->lastip)) {
                        $decodedresponse = json_decode($jsonresponse);
                        if ($decodedresponse->status === 'success') {
                            $txt .= "\n" . $decodedresponse->country;
                            $txt .= "\n" . $decodedresponse->city;
                            $txt .= "\n" . $decodedresponse->zip;
                            $txt .= "\n" . $decodedresponse->timezone;
                            if ($user->timezone === '99') {
                                $DB->set_field('user', 'timezone', $decodedresponse->timezone, ['id' => $user->id]);
                            }
                        }
                    }
                }
            } else {
                if ($user->lastip != '' and $jsonresponse = file_get_contents('http://ip-api.com/json/' . $user->lastip)) {
                    $decodedresponse = json_decode($jsonresponse);
                    if ($decodedresponse->status === 'success') {
                        $txt .= "\n" . $user->id;
                        $txt .= "\n" . $user->b_id;
                        $txt .= "\n" . $decodedresponse->country;
                        $txt .= "\n" . $decodedresponse->countryCode;
                        $txt .= "\n" . $decodedresponse->city;
                        $txt .= "\n" . $decodedresponse->zip;
                        $txt .= "\n" . $decodedresponse->lat;
                        $txt .= "\n" . $decodedresponse->lon;
                        $txt .= "\n" . $decodedresponse->timezone;
                        $boumc->id = $user->b_id;
                        $boumc->userid = $user->id;
                        $boumc->lat = $decodedresponse->lat;
                        $boumc->lng = $decodedresponse->lon;
                        $boumc->city = $decodedresponse->city;
                        $boumc->country = $decodedresponse->countryCode;
                        try {
                            $DB->update_record("block_online_users_map", $boumc);
                        } catch (exception $e) {
                            debugging('Warning: block_online_users_map update error ' . serialize($e), DEBUG_DEVELOPER);
                        }
                        if ($user->timezone === '99') {
                            try {
                                $DB->set_field('user', 'timezone', $decodedresponse->timezone, ['id' => $user->id]);
                            } catch (exception $e) {
                                debugging('Error: block_online_users_map update error ' . serialize($e), DEBUG_DEVELOPER);
                            }
                        }
                    } else {
                        // Failed.
                        $txt .= insertfail($user, $txt);
                    }
                } else {
                    $txt .= "\n" . $CFG->wwwroot . '/user/edit.php?id=' . $user->id;
                    $txt .= "  NOT FOUND\n";
                    $txt .= $user->firstname . " " . $user->lastname . ": " .$user->city . " - " . $user->country;
                    $txt .= "\n" . $user->lastip;
                }
            }
        } else {
            $txt .= "\nLocation not found due to no or invalid response";
            $txt .= insertfail($user, $txt);
        }
        if ($txt != '' ) {
            $user = $DB->get_record('user', ['id' => 2]);
            email_to_user($user, get_admin(), 'Location', $txt);
        }
    }
    return true;
}

/**
 * Insert a failed location
 *
 * @param string $user
 * @param string $txt
 * @return String body of the returned request
 */
function insertfail($user, $txt) {
    global $CFG, $DB;
    $boumc = new stdClass;
    $boumc->userid = $user->id;
    $boumc->lat = 0;
    $boumc->lng = 0;
    $boumc->city = $user->city;
    $boumc->country = $user->country;
    $DB->insert_record("block_online_users_map", $boumc);
    $txt .= "\n" . $CFG->wwwroot . '/user/edit.php?id=' . $user->id;
    $txt .= "\nLocation NOT added for\n";
    $country = get_string($user->country, 'countries');
    $name = $user->firstname . " " . $user->lastname;
    $txt .= $name . ": " .$user->city . " - " . $country;
    $txt .= "\n" . $user->lastip;
    return $txt;
}

/**
 * Gets the content of a url request
 *
 * @param string $domain
 * @param string $path
 * @return String body of the returned request
 */
function geturlcontent($domain, $path) {
    global $CFG;
    $message = "GET $domain$path HTTP/1.0\r\n";
    $msgaddress = str_replace("http://", "", $domain);
    $message .= "Host: $msgaddress\r\n";
    $message .= "Connection: Close\r\n";
    $message .= "\r\n";

    if ($CFG->proxyhost != "" && $CFG->proxyport != 0) {
        $address = $CFG->proxyhost;
        $port = $CFG->proxyport;
    } else {
        $address = str_replace("http://", "", $domain);
        $port = 80;
    }

    /* Attempt to connect to the proxy server to retrieve the remote page */
    if (!$socket = fsockopen($address, $port, $errno, $errstring, 20)) {
        die("Couldn't connect to host $address: $errno: $errstring\n");
    }

    fwrite($socket, $message);
    $content = "";
    while (!feof($socket)) {
        $content .= fgets($socket, 1024);
    }

    fclose($socket);
    return extractbody($content);
}

/**
 * Removes the headers from a url response.
 *
 * @param string $response
 * @return string body of the returned request
 */
function extractbody($response) {
    $crlf = "\r\n";
    // Split header and body.
    $pos = strpos($response, $crlf . $crlf);
    if ($pos === false) {
        return($response);
    }
    $header = substr($response, 0, $pos);
    $body = substr($response, $pos + 2 * strlen($crlf));
    // Parse headers.
    $headers = [];
    $lines = explode($crlf, $header);

    foreach ($lines as $line) {
        if (($pos = strpos($line, ':')) !== false) {
            $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos + 1));
        }
    }
    return $body;
}

/**
 * Gets the timetosee value
 *
 * @return int
 */
function gettimetoshowusers() {
    global $CFG;
    $timetoshowusers = 300;
    if (isset($CFG->block_online_users_map_timetosee)) {
        $timetoshowusers = $CFG->block_online_users_map_timetosee * 60;
    }
    return $timetoshowusers;
}


/**
 * Gets the lat/lng coords of the current user.
 *
 * @return Array of decimal
 */
function getcurrentuserlocations() {
    global $USER, $DB;
    $coords = [];
    $sql = "SELECT boumc.userid, boumc.lat, boumc.lng
            FROM {block_online_users_map} boumc
            WHERE userid = ?";
    $c = $DB->get_record_sql($sql, [$USER->id]);
    if ($c) {
        $coords['lat'] = $c->lat;
        $coords['lng'] = $c->lng;
    }
    return $coords;
}

/**
 * Convert object to json format
 *
 * @param objects $objects objects to turn into JSON
 * @param string $name overall name of the JSON object
 * @param string $callback name of the callback function
 * @return string of the JSON object
 */
function phptojson($objects, $name, $callback='') {
    $str = '';
    if ($callback != '') {
        $str .= $callback . '(';
    }
    if ($objects) {
        $str .= '{"' . $name . '":[';
        $okeys = array_keys($objects);
        for ($i = 0; $i < count($okeys); $i++) {
            $myobj = $objects[$okeys[$i]];
            $attr = get_object_vars($myobj);
            $str .= '{';
            $keys = array_keys($attr);
            for ($j = 0; $j < count($keys); $j++) {
                $str .= '"' . $keys[$j] .'":"' . $attr[$keys[$j]] . '"';
                if ($j != (count($keys) - 1)) {
                    $str .= ',';
                }
            }
            $str .= '}';
            if ($i != (count($objects) - 1)) {
                $str .= ',';
            }
        }
        $str .= ']}';
    }
    if ($callback != '') {
        $str .= ');';
    }
    return $str;
}
