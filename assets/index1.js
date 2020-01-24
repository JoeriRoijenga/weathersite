var iconFeature = new ol.Feature({
    geometry: new ol.geom.Point(ol.proj.fromLonLat([0, 0])),
    name: 'Evenaar',
    id: 1
});

var iconFeature1 = new ol.Feature({
    geometry: new ol.geom.Point(ol.proj.fromLonLat([6.534853, 53.240570])),
    name: 'Hanze Hogeschool',
    id: 2
});

var iconStyle = new ol.style.Style({
    image: new ol.style.Icon({
        anchor: [0.5, 1],
        anchorXUnits: 'fraction',
        anchorYUnits: 'fraction',
        src: 'assets/pointer-transparent-red-1.png',
        scale: 0.08
    })
});

iconFeature.setStyle(iconStyle);
iconFeature1.setStyle(iconStyle);

var vectorSource = new ol.source.Vector({
    features: [iconFeature, iconFeature1]
});

var vectorLayer = new ol.layer.Vector({
    source: vectorSource
});

var rasterLayer = new ol.layer.Tile({
    source: new ol.source.TileJSON({
        url: 'https://a.tiles.mapbox.com/v3/aj.1x1-degrees.json',
        crossOrigin: ''
    })
});

var map = new ol.Map({
    layers: [rasterLayer, vectorLayer],
    target: document.getElementById('map'),
    view: new ol.View({
        center: [0, 0],
        zoom: 3
    })
});

var element = document.getElementById('popup');

var popup = new ol.Overlay({
    element: element,
    positioning: 'bottom-centesr',
    stopEvent: false,
    offset: [0, -45]
});
map.addOverlay(popup);

// Variable for checking the content values, in case of refreshing
var prevPopup = null;

// display popup on click
map.on('click', function(evt) {
    var currentStation = document.getElementById('currentStation');

    var feature = map.forEachFeatureAtPixel(evt.pixel,
        function(feature) {
            return feature;
        });

    if (feature != null) {
        if (prevPopup != feature.get("name") && prevPopup != null) {
            $(element).popover("dispose");
        }
    }

    if (feature) {
        var coordinates = feature.getGeometry().getCoordinates();
        popup.setPosition(coordinates);
        $(element).popover({
            placement: 'top',
            html: true,
            content: feature.get('name')
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

// change mouse cursor when over marker
map.on('pointermove', function(e) {
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

function eventChange() {
    currentStation.dispatchEvent(new Event('change'));
}