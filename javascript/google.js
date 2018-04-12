function initialize(id) {
    var mapOptions = {
        zoom: 1.5,
        center: new google.maps.LatLng(50, 0)
    };

    map = new google.maps.Map(document.getElementById('block_online_users_mapfull'), mapOptions);
    loadUsers();
}

function loadUsers(){
    request = M.cfg.wwwroot + "/blocks/online_users_map/getusers1.php?callback=loadUsersCallback";
    aObj = new JSONscriptRequest(request);
    aObj.buildScriptTag();
    aObj.addScriptTag();
}

function loadUsersCallback(jData){
    if(!jData){
        return;
    }
    var users = jData;
    for (i=0; i < users.length; i++){
        createMarker(users[i]);
    }
}

function createMarker(user){
    if (user.lat != "" && user.lng != ""){
        var point = new google.maps.LatLng(user.lat, user.lng);
        if(user.online == "true"){
             createOnlineMarker(point, user.fullname);
        } else {
           createOfflineMarker(point, user.fullname);
        }
    }
}

function createOnlineMarker(point, user){
    var image = new google.maps.MarkerImage(M.cfg.wwwroot + "/blocks/online_users_map/images/online.png",
                new google.maps.Size(22, 15),
                new google.maps.Point(0,0),
                new google.maps.Point(7, 15));
    var shadow = new google.maps.MarkerImage(M.cfg.wwwroot + "/blocks/online_users_map/images/shadow.png",
                 new google.maps.Size(22, 15),
                 new google.maps.Point(0,0),
                 new google.maps.Point(7, 15));

    var marker = new google.maps.Marker({
                 position: point,
                 map: map,
                 shadow: shadow,
                 icon: image,
                 title: user + " (online)"});
}


function createOfflineMarker(point, user){
    var image = new google.maps.MarkerImage(M.cfg.wwwroot + '/blocks/online_users_map/images/offline.png',
                new google.maps.Size(22, 15),
                new google.maps.Point(0,0),
                new google.maps.Point(7, 15));
    var shadow = new google.maps.MarkerImage(M.cfg.wwwroot + 'blocks/online_users_map/images/shadow.png',
                 new google.maps.Size(22, 15),
                 new google.maps.Point(0,0),
                 new google.maps.Point(7, 15));
    var marker = new google.maps.Marker({
                 position: point,
                 map: map,
                 shadow: shadow,
                 icon: image,
                 title: user});
}


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
//google.maps.event.addDomListener(window, 'load', initialize);