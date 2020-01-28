// Basis global variables
var ctxLineGraph = document.getElementById('lineGraph').getContext('2d');
var ctxBarGraph = document.getElementById('barGraph').getContext('2d');
var charts = new Array();
var stationLabel = document.getElementById("stationName");

/**
 * Start of creating charts
 */
function startChart() {
    var station = document.getElementById('currentStation').value;
    var randomData = new Array();

    for (i = 0; i < 12; i++) {
        randomData[randomData.length] = Math.floor((Math.random() * 40) + 1);
    }

    if (station != 0) {
        Get("api/v1/stations?stn=" + station, randomData);
    } else {
        destroyCharts();
    }
}

/**
 * Destroying all charts
 */
function destroyCharts() {
    if (charts.length != 0) {
        for (i = 0; i < charts.length; i++) {
            charts[i].destroy();
        }
        stationLabel.innerHTML = "No Station Chosen";
    }
}

/**
 * Creating the line graph
 *
 * @param dataset
 */
function createLineGraph(dataset) {
    charts[0] = new Chart(ctxLineGraph, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', "December"],
            datasets: dataset
        },
        options: {}
    });
}

/**
 * Creating the bar graph
 *
 * @param dataset
 */
function createBarGraph(dataset) {
    charts[1] = new Chart(ctxBarGraph, {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', "December"],
            datasets: dataset
        },
        options: {}
    });
}

/**
 * Retrieving data and creating datasets
 *
 * @param jsonURL
 * @param randomData
 * @constructor
 */
function Get(jsonURL, randomData){
    var datasets = new Array();
    var Httpreq = new XMLHttpRequest();
    Httpreq.open("GET", jsonURL);
    Httpreq.send();
    Httpreq.onreadystatechange=function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(Httpreq.responseText);
            destroyCharts();

            createLineGraph(datasets[0] = [{
                label: 'Temperature',
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ')',
                data: randomData
            }]);

            createBarGraph([{
                label: 'Air Pressure',
                backgroundColor: 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ')',
                barPercentage: 0.5,
                barThickness: 6,
                maxBarThickness: 8,
                minBarLength: 2,
                data: randomData
            }]);

            stationLabel.innerHTML = data.items[0]["name"];
        }
    }
}