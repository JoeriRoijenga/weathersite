
var stations;
var markers = new ol.Collection();

/**
 * Generate Iconstyle with SVG inline icon.
 * Inline icon to make it dynamic.
 * @param type 1 = default, 2 = selected
 * @param count Count to show within the marker.
 * @returns {ol.style.Style}
 */
function getIconStyle(type, count) {
    var mainColor = type === 2 ? 'rgb(20, 110, 204)' : 'rgb(238, 56, 64)';

    var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="120pt" height="120pt" viewBox="0 0 120 120" version="1.1">\n' +
        '<g id="surface1">\n' +
        '<path style=" stroke:none;fill-rule:nonzero;fill:' + mainColor + ';fill-opacity:1;" d="M 104.492188 47.207031 C 104.492188 82.835938 64.273438 115.832031 64.273438 115.832031 C 61.921875 117.761719 58.078125 117.761719 55.726562 115.832031 C 55.726562 115.832031 15.507812 82.835938 15.507812 47.207031 C 15.507812 22.636719 35.425781 2.714844 60 2.714844 C 84.574219 2.714844 104.492188 22.636719 104.492188 47.207031 Z M 104.492188 47.207031 "/>\n';

    if (count <= 1){
        svg += '<path style=" stroke:none;fill-rule:nonzero;fill:rgb(225, 225, 214);fill-opacity:1;" d="M 60 69.59375 C 47.65625 69.59375 37.613281 59.550781 37.613281 47.207031 C 37.613281 34.867188 47.65625 24.824219 60 24.824219 C 72.34375 24.824219 82.386719 34.867188 82.386719 47.207031 C 82.386719 59.550781 72.34375 69.59375 60 69.59375 Z M 60 69.59375 "/>';
    }

    svg += '<path style=" stroke:none;fill-rule:nonzero;fill:rgb(17, 17, 63);fill-opacity:1;" d="M 60 0 C 33.96875 0 12.792969 21.179688 12.792969 47.207031 C 12.792969 55.734375 14.992188 64.785156 19.328125 74.105469 C 22.753906 81.46875 27.519531 89.023438 33.488281 96.566406 C 43.609375 109.351562 53.585938 117.585938 54.003906 117.933594 C 55.6875 119.308594 57.84375 120 60 120 C 62.15625 120 64.3125 119.308594 65.996094 117.933594 C 66.414062 117.585938 76.390625 109.351562 86.511719 96.566406 C 92.480469 89.023438 97.246094 81.46875 100.671875 74.105469 C 105.007812 64.785156 107.210938 55.734375 107.210938 47.207031 C 107.207031 21.179688 86.03125 0 60 0 Z M 82.324219 93.109375 C 72.5625 105.460938 62.648438 113.652344 62.550781 113.734375 C 61.191406 114.847656 58.808594 114.847656 57.449219 113.734375 C 57.351562 113.652344 47.4375 105.460938 37.675781 93.109375 C 28.800781 81.878906 18.222656 64.800781 18.222656 47.207031 C 18.222656 24.171875 36.960938 5.429688 60 5.429688 C 83.035156 5.429688 101.777344 24.171875 101.777344 47.207031 C 101.777344 64.796875 91.199219 81.878906 82.324219 93.109375 Z M 82.324219 93.109375 "/>\n</g>\n';

    if (count <= 1){
        svg += '<path style=" stroke:none;fill-rule:nonzero;fill:rgb(17, 17, 63);fill-opacity:1;" d="M 60 22.109375 C 46.160156 22.109375 34.898438 33.367188 34.898438 47.207031 C 34.898438 61.046875 46.160156 72.308594 60 72.308594 C 73.839844 72.308594 85.101562 61.046875 85.101562 47.207031 C 85.097656 33.367188 73.839844 22.109375 60 22.109375 Z M 60 66.878906 C 49.152344 66.878906 40.328125 58.054688 40.328125 47.207031 C 40.328125 36.363281 49.152344 27.539062 60 27.539062 C 70.847656 27.539062 79.671875 36.363281 79.671875 47.207031 C 79.671875 58.054688 70.847656 66.878906 60 66.878906 Z M 60 66.878906 "/>';
    }else{
        svg += '<text x="50%" y="60%" text-anchor="middle" font-size="40pt" fill="rgb(225, 225, 255)" opacity="1">' + count + '</text>';
    }
    svg += '</svg>';

    return new ol.style.Style({
        image: new ol.style.Icon({
            anchor: [0.5, 1],
            anchorXUnits: 'fraction',
            anchorYUnits: 'fraction',
            src: 'data:image/svg+xml;utf8,' + svg,
            scale: 0.3
        })
    });
}

/**
 * Asynchronous function for retrieving JSON data
 *
 * @param jsonURL
 * @constructor
 */
function Get(jsonURL){
    var Httpreq = new XMLHttpRequest();
    Httpreq.open("GET", jsonURL);
    Httpreq.send();
    Httpreq.onreadystatechange=function() {
        if (this.readyState === 4 && this.status === 200) {
            var data = JSON.parse(Httpreq.responseText);
            stations = data.items;
            setup()
        }
    }
}

/**
 * Reload all markers on the map.
 * All current markers are destroyed and new ones are placed.
 * @param map
 * @param prevPopup
 */
function refreshMarkers(map, prevPopup) {
    var boundary = map.getView().calculateExtent(map.getSize());
    var groupBy = Math.trunc((boundary[2] - boundary[0]) / 10);
    var groups = {};
    markers.clear();

    for (var i = 0; i < stations.length; i++) {
        var location = ol.proj.fromLonLat([stations[i]["longitude"], stations[i]["latitude"]]);

        if (location[0] >= boundary[0] && location[0] <= boundary[2] && location[1] >= boundary[1] && location[1] <= boundary[3]){
            var point = new ol.geom.Point(location);
            var A0 = Math.trunc(point.flatCoordinates[0] / groupBy);
            var A1 = Math.trunc(point.flatCoordinates[1] / groupBy);
            var key = A0 + "-" + A1;
            if (map.getView().getZoom() >= 7){
                key += "_" + i;
            }

            if (groups.hasOwnProperty(key)){
                groups[key].pointers[groups[key].pointers.length] = [stations[i]["longitude"], stations[i]["latitude"]];
                groups[key].locations[groups[key].locations.length] = location;
            }else{
                groups[key] = {
                    pointers: [[stations[i]["longitude"], stations[i]["latitude"]]],
                    locations: [location],
                    index: i
                };
            }
        }
    }
    for(key in groups){
        if (groups.hasOwnProperty(key)){
            var iconFeature;
            if (groups[key].pointers.length > 1){
                var long = 0;
                var lat = 0;
                for (var i in groups[key].locations){
                    long += groups[key].locations[i][0];
                    lat += groups[key].locations[i][1];
                }
                long /= groups[key].locations.length;
                lat /= groups[key].locations.length;

                iconFeature = new ol.Feature({
                    id: key,
                    name: 'grouped_pointer',
                    geometry: new ol.geom.Point([long, lat])
                });
                iconFeature.setStyle(getIconStyle(1, groups[key].locations.length));
            }else{
                iconFeature = new ol.Feature({
                    geometry: new ol.geom.Point(ol.proj.fromLonLat(groups[key].pointers[0])),
                    name: stations[groups[key].index]["name"],
                    id: stations[groups[key].index]["id"]
                });
                iconFeature.setStyle(getIconStyle(
                    stations[groups[key].index]["name"] === prevPopup ? 2 : 1
                , 1));
            }
            markers.push(iconFeature);
        }
    }

}

// Setup for the map
function setup() {
    var vectorLayer = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: markers
        })
    });

    // Creating map
    var rasterLayer = new ol.layer.Tile({
        source: new ol.source.TileJSON({
            url: 'https://a.tiles.mapbox.com/v3/aj.1x1-degrees.json',
            crossOrigin: ''
        })
    });

    // Adding map and markers
    var map = new ol.Map({
        layers: [rasterLayer, vectorLayer],
        target: document.getElementById('map'),
        view: new ol.View({
            center: [0, 0],
            zoom: 3
        })
    });

    map.on('moveend', function () {
        refreshMarkers(this, prevPopup);
    });

    // Retrieving popup element
    var element = document.getElementById('popup');

    // Settings popup
    var popup = new ol.Overlay({
        element: element,
        positioning: 'bottom-centesr',
        stopEvent: false,
        offset: [0, -45]
    });

    // Adding popup
    map.addOverlay(popup);

    // Variable for checking the content values, in case of refreshing
    var prevPopup = null;
    var prevFeature = null;

    // display popup on click
    map.on('click', function (evt) {
        var currentStation = document.getElementById('currentStation');

        // Retrieving data of feature
        var feature = map.forEachFeatureAtPixel(evt.pixel,
            function (feature) {
                return feature;
            });

        // Remove popup
        if (feature != null) {
            if (prevPopup !== feature.get("name") && prevPopup != null) {
                $(element).popover("dispose");
                prevFeature.setStyle(getIconStyle(1, 1))
            }
        }

        if (feature) {
            if (feature.get('name') === 'grouped_pointer'){
                map.getView().animate({zoom: map.getView().getZoom() + 2, center: feature.getGeometry().flatCoordinates, duration: 1000});
            }else {
                // Specific coordinates for this map
                var coordinates = feature.getGeometry().getCoordinates();
                popup.setPosition(coordinates);

                // Normal coordinates
                var coor = ol.proj.transform(coordinates, 'EPSG:3857', 'EPSG:4326');
                coor[1] = Math.round(coor[1] * 100) / 100;
                coor[0] = Math.round(coor[0] * 100) / 100;

                if(coor[0] < 0){
                    coor[0] = (0 - coor[0]) + "°W";
                }else{
                    coor[0] = coor[0] + "°E";
                }

                if(coor[1] < 0){
                    coor[1] = (0 - coor[1]) + "°N";
                }else{
                    coor[1] = coor[1] + "°S";
                }


                // Data in popup
                $(element).popover({
                    placement: 'top',
                    html: true,
                    title: feature.get('name'),
                    content: "<b>LAT:</b> " + coor[1] + "<br /><b>LON:</b> " + coor[0]
                });
                prevFeature = feature;
                prevPopup = feature.get('name');
                currentStation.value = feature.get('id');
                eventChange();

                $(element).popover('show');
                feature.setStyle(getIconStyle(2, 1))
            }
        } else {
            $(element).popover('dispose');
            document.getElementById('currentStation').value = 0;
            eventChange();
        }
    });

    // Change mouse cursor when over marker
    map.on('pointermove', function (e) {
        if (e.dragging) {
            $(element).popover('dispose');
            document.getElementById('currentStation').value = 0;
            eventChange();
            return;
        }
        var pixel = map.getEventPixel(e.originalEvent);
        var hit = map.hasFeatureAtPixel(pixel);
        map.getTarget().style.cursor = hit ? 'pointer' : '';
    });

    // Even change for graph.js
    function eventChange() {
        currentStation.dispatchEvent(new Event('change'));
    }
}
// Start
Get("api/v1/stations");