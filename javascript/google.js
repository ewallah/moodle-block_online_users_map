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
    for (i = 0; i < users.length; i++){
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
    var marker = new google.maps.Marker({position: point, map: map, shadow: shadow, icon: image, title: user + " (online)"});
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
    var marker = new google.maps.Marker({position: point, map: map, shadow: shadow, icon: image, title: user});
}


var map = null;

function JSONscriptRequest(fullUrl) {
    this.fullUrl = fullUrl;
    this.noCacheIE = '&noCacheIE=' + (new Date()).getTime();
    this.headLoc = document.getElementsByTagName("head").item(0);
    this.scriptId = 'YJscriptId' + JSONscriptRequest.scriptCounter++;
}

// Static script ID counter.
JSONscriptRequest.scriptCounter = 1;

// BuildScriptTag method.
JSONscriptRequest.prototype.buildScriptTag = function () {
    this.scriptObj = document.createElement("script");
    this.scriptObj.setAttribute("type", "text/javascript");
    this.scriptObj.setAttribute("src", this.fullUrl + this.noCacheIE);
    this.scriptObj.setAttribute("id", this.scriptId);
}

// RemoveScriptTag method.
JSONscriptRequest.prototype.removeScriptTag = function () {
    this.headLoc.removeChild(this.scriptObj);
}

// AddScriptTag method.
JSONscriptRequest.prototype.addScriptTag = function () {
    this.headLoc.appendChild(this.scriptObj);
}
