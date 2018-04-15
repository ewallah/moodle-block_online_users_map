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

defined('MOODLE_INTERNAL') || die();

require_once("../../config.php");
require_once($CFG->dirroot.'/blocks/online_users_map/lib.php');
?>

var map = null;


function JSONscriptRequest(fullUrl) {
    // REST request path
    this.fullUrl = fullUrl; 
    // Keep IE from caching requests
    this.noCacheIE = '&noCacheIE=' + (new Date()).getTime();
    // Get the DOM location to put the script tag
    this.headLoc = document.getElementsByTagName("head").item(0);
    // Generate a unique script tag id
    this.scriptId = 'YJscriptId' + JSONscriptRequest.scriptCounter++;
}

// Static script ID counter
JSONscriptRequest.scriptCounter = 1;

// buildScriptTag method
//
JSONscriptRequest.prototype.buildScriptTag = function () {

    // Create the script tag
    this.scriptObj = document.createElement("script");
    
    // Add script object attributes
    this.scriptObj.setAttribute("type", "text/javascript");
    this.scriptObj.setAttribute("src", this.fullUrl + this.noCacheIE);
    this.scriptObj.setAttribute("id", this.scriptId);
}
 
// removeScriptTag method
// 
JSONscriptRequest.prototype.removeScriptTag = function () {
    // Destroy the script tag
    this.headLoc.removeChild(this.scriptObj);  
}

// addScriptTag method
//
JSONscriptRequest.prototype.addScriptTag = function () {
    // Create the script tag
    this.headLoc.appendChild(this.scriptObj);
}


/**
 * Load the Google map
 */
function loadMap(){
    if(document.getElementById("block_online_users_googlemap") != null){
        <?php
        $latlng = $CFG->block_online_users_map_centre_lat . "," . $CFG->block_online_users_map_centre_lng;
        $coords = getcurrentuserlocations();
        if ($coords) {
            $latlng = $coords['lat'] . "," . $coords['lng'];
        }
        ?>
     var myOptions = {
        center: new google.maps.LatLng(<?php p($latlng); ?>),
        zoom: <?php p($CFG->block_online_users_map_init_zoom); ?>,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        mapTypeControl: false,
        streetViewControl: false
        };
        map = new google.maps.Map(document.getElementById("block_online_users_googlemap"), myOptions);
        loadUsers();     
    }   
    
} 


function loadUsers(){
    request = "<?php p($CFG->wwwroot); ?>/blocks/online_users_map/getusers.php?callback=loadUsersCallback";
    aObj = new JSONscriptRequest(request);
    aObj.buildScriptTag();
    aObj.addScriptTag();
}

function loadUsersCallback(jData){
    if(!jData){
        return;
    }
    var users = jData.online;
    if(!users){
        return;
    }
    for (i = 0; i < users.length; i++){
        createMarker(users[i]);
    }
}

function createMarker(user){
    if (user.lat != "" && user.lng != ""){
        var point = new google.maps.LatLng(user.lat, user.lng);
        if(user.online == "true"){
          createOnlineMarker(point,user);
        } else {
            createOfflineMarker(point,user);
        }
    }
}

function createOnlineMarker(point,user){

    var image = new google.maps.MarkerImage('/blocks/online_users_map/images/online.png',
              new google.maps.Size(22, 15),
              new google.maps.Point(0,0),
              new google.maps.Point(7, 15));
    var shadow = new google.maps.MarkerImage('/blocks/online_users_map/images/shadow.png',
              new google.maps.Size(22, 15),
              new google.maps.Point(0,0),
              new google.maps.Point(7, 15));

    var marker = new google.maps.Marker({
                            position: point, 
                            map: map,
                            shadow: shadow,
                            icon: image,
                            title: user.fullname + " (online)"});
                            
}


function createOfflineMarker(point,user){
    var image = new google.maps.MarkerImage('/blocks/online_users_map/images/offline.png',
              new google.maps.Size(22, 15),
              new google.maps.Point(0,0),
              new google.maps.Point(7, 15));
    var shadow = new google.maps.MarkerImage('/blocks/online_users_map/images/shadow.png',
              new google.maps.Size(22, 15),
              new google.maps.Point(0,0),
              new google.maps.Point(7, 15));

    var marker = new google.maps.Marker({
            position: point, 
            map: map,
            shadow: shadow,
            icon: image,
            title: user.fullname});
}

loadMap();