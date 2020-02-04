// Basis global variables
var ctxLineGraph = document.getElementById('lineGraph').getContext('2d');
var ctxBarGraph = document.getElementById('barGraph').getContext('2d');
var charts = new Array();
var stationLabel = document.getElementById("stationName");
// var time = {
//     '01:00': 0, '02:00': 0, '03:00': 0, '04:00': 0, '05:00': 0, '06:00': 0, '07:00': 0, '08:00': 0, '09:00': 0, '10:00': 0, '11:00': 0, "12:00": 0,
//     '13:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00': 0, '22:00': 0, "23:00": 0, "00:00": 0
// };

function createTimeArray() {
    var time = {};

    for (i = 0; i <= 23; i++) {
        if (i < 10) {
            i = "0" + i;
        }
        time[i + ":00"] = 0;
    }

    return time;
}

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
        Get("api/v1/station/" + station + "?group_by=hour", randomData);
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
function createLineGraph(dataset, labels) {
    charts[0] = new Chart(ctxLineGraph, {
        type: 'line',
        data: {
            labels: labels,
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
function createBarGraph(dataset, labels) {
    charts[1] = new Chart(ctxBarGraph, {
        type: 'bar',
        data: {
            labels: labels,
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
            // Creating variables for time
            var temp = createTimeArray();
            var pressureStation = createTimeArray();

            // Retrieving weather object
            var object = data.item.weather["2020-01-28"];

            for (item in object){
                temp[object[item]["time"].toString().slice(0, -3)] = object[item]["temperature"];
                pressureStation[object[item]["time"].toString().slice(0, -3)] = object[item]["air_pressure_station"];
            }

            // Creating line graph
            createLineGraph(datasets[0] = [{
                label: 'Temperature',
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ')',
                data: listComprehension(temp, false)
            }], listComprehension(temp));

            // Creating bar graph
            createBarGraph([{
                label: 'Air Pressure Station',
                backgroundColor: 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ')',
                barPercentage: 0.5,
                barThickness: 6,
                maxBarThickness: 8,
                minBarLength: 2,
                data: listComprehension(pressureStation, false)
            }], listComprehension(pressureStation));

            // Station name
            stationLabel.innerHTML = data.item.name;
        }
    }

    /**
     *
     * @param array
     * @returns {any[]}
     */
    function listComprehension(data, key = true) {
        var arrayReturn = new Array();

        if (key) {
            for (item in data) {
                arrayReturn.push(item);
            }
        } else {
            for (item in data) {
                arrayReturn.push(data[item]);
            }
        }

        return arrayReturn;
    }
}