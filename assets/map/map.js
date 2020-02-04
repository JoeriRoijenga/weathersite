/**
 * Asynchronous function for retrieving JSON data
 *
 * @param jsonURL
 * @constructor
 */
function Get(jsonURL){
    var markers = new Array();
    var Httpreq = new XMLHttpRequest();
    Httpreq.open("GET", jsonURL);
    Httpreq.send();
    Httpreq.onreadystatechange=function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(Httpreq.responseText);
            for (i = 0; i < data.items.length; i++) {
                var iconFeature = new ol.Feature({
                    geometry: new ol.geom.Point(ol.proj.fromLonLat([data.items[i]["longitude"], data.items[i]["latitude"]])),
                    name: data.items[i]["name"],
                    id: data.items[i]["id"]
                });

                iconFeature.setStyle(iconStyle);
                markers[markers.length] = iconFeature
            }
            // Creating markers
            setup(markers);
        }
    }
}

// Creating the style for the markers
var iconStyle = new ol.style.Style({
    image: new ol.style.Icon({
        anchor: [0.5, 1],
        anchorXUnits: 'fraction',
        anchorYUnits: 'fraction',
        src: './assets/pointer-transparent-red-1.png',
        scale: 0.08
    })
});

// Setup for the map
function setup(markers) {
    // Creating markers
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
            if (prevPopup != feature.get("name") && prevPopup != null) {
                $(element).popover("dispose");
            }
        }

        if (feature) {
            // Specific coordinates for this map
            var coordinates = feature.getGeometry().getCoordinates();
            popup.setPosition(coordinates);

            // Normal coordinates
            var coor = ol.proj.transform(coordinates, 'EPSG:3857', 'EPSG:4326');
            coor[1] = Math.round(coor[1] * 100) / 100;
            coor[0] = Math.round(coor[0] * 100) / 100;

            // Data in popup
            $(element).popover({
                placement: 'top',
                html: true,
                title: feature.get('name'),
                content: "<b>ID:</b> " + feature.get('id') + "<br /> " +
                            "<b>LAT:</b> " + coor[1] + "<br /><b>LON:</b> " + coor[0]
            });
            prevPopup = feature.get('name')
            currentStation.value = feature.get('id');
            eventChange();

            $(element).popover('show');
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