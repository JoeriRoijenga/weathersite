// Basis global variables
var ctxTemperature = document.getElementById('graphTemperature').getContext('2d');
var ctxAirPressureStation = document.getElementById('graphAirPressureStation').getContext('2d');
var ctxRainfall = document.getElementById('graphRainfall').getContext('2d');
var charts = new Array();
var stationLabel = document.getElementById("stationName");
var intervalGraph;
var requests = [];

var temperatureColor = 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', 0.5)';
var rainfallColor = 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ')';
var pressureColor = 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ')';

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

    if (station != 0) {
        Get("api/v1/station/" + station + "?group_by=5_second&limit=24");

        intervalGraph = setInterval(function() {
            Get("api/v1/station/" + station + "?group_by=5_second&limit=24", true);
        }, 5000);
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
        clearInterval(intervalGraph);

        requests.forEach(function(request) {
            request.abort()
        });

    }
}

/**
 * Creating the line graph
 *
 * @param dataset
 * @param labels
 * @param ctx
 * @param update
 * @returns {Chart}
 */
function createLineGraph(dataset, labels, ctx) {
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: dataset
        },
        options: {
            responsiveAnimationDuration: 0
        }
    });
}

/**
 *
 * @param chart
 * @param dataset
 * @param labels
 */
function updateLineGraph(chart, dataset, labels) {
    chart.data = {
        labels: labels,
        datasets: dataset,
        options: {
            responsiveAnimationDuration: 0
        }
    };
    chart.update(0);
}

/**
 * Creating the bar graph
 *
 * @param dataset
 * @param labels
 * @param ctx
 * @returns {Chart}
 */
function createBarGraph(dataset, labels, ctx) {
    return new Chart(ctx, {
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
 * @param update
 * @constructor
 */
function Get(jsonURL, update = false){
    var datasets = new Array();
    var Httpreq = new XMLHttpRequest();
    requests.push(Httpreq);

    if (!update) {
        destroyCharts();
    }

    Httpreq.open("GET", jsonURL);
    Httpreq.send();
    Httpreq.onreadystatechange=function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(Httpreq.responseText)

            // Creating variables for time
            var temp = [];
            var pressureStation = [];
            var rainfall = [];

            // Retrieving weather object
            var date = 0;
            var lastDate;
            for (var dateItem in data.item.weather){
                if (Date.parse(dateItem) > date){
                    date = Date.parse(dateItem);
                    lastDate = dateItem;
                }
            }

            var object = data.item.weather[lastDate];

            // Sorting data
            var lastUpdate = '-';
            for (var item in object){
                if (object.hasOwnProperty(item)) {
                    temp[object[item]["time"].toString().slice(-5)] = object[item]["temperature"];
                    pressureStation[object[item]["time"].toString().slice(-5)] = object[item]["air_pressure_station"];
                    rainfall[object[item]["time"].toString().slice(-5)] = object[item]["rainfall"];
                    lastUpdate = lastDate + " " + object[item]["time"];
                    console.log(object[item]["time"]);
                }
            }
            $('#lastUpdate').text(lastUpdate);

            // Create dataset line graph, temperature
            datasets[0] = [{
                label: 'Temperature (Celcius)',
                backgroundColor: temperatureColor,
                data: listComprehension(temp, false)
            }];

            // Create dataset bar graph
            datasets[1] = [{
                label: 'Air Pressure (millibar)',
                backgroundColor: pressureColor,
                barPercentage: 0.5,
                barThickness: 6,
                maxBarThickness: 8,
                minBarLength: 2,
                data: listComprehension(pressureStation, false)
            }];

            // Create dataset line graph, rainfall
            datasets[2] = [{
                label: 'Rainfall (millimeter)',
                backgroundColor: rainfallColor,
                data: listComprehension(rainfall, false)
            }];

            // Update line graph
            if (update) {
                updateLineGraph(charts[0], datasets[0], listComprehension(rainfall))
                updateLineGraph(charts[1], datasets[1], listComprehension(rainfall))
                updateLineGraph(charts[2], datasets[2], listComprehension(rainfall))
            } else {
                // Creating temperature
                charts[0] = createLineGraph(datasets[0], listComprehension(temp), ctxTemperature);

                // Creating air pressure station
                charts[1] = createBarGraph(datasets[1], listComprehension(pressureStation), ctxAirPressureStation);

                // Creating rainfall
                charts[2] = createLineGraph(datasets[2], listComprehension(rainfall), ctxRainfall);
            }

            // Station name
            stationLabel.innerHTML = data.item.name;
        }
    }

    /**
     *
     * @param data
     * @param key
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