// Basis global variables
var ctxTemperature = document.getElementById('graphTemperature').getContext('2d');
var ctxAirPressureStation = document.getElementById('graphAirPressureStation').getContext('2d');
var ctxRainfall = document.getElementById('graphRainfall').getContext('2d');
var charts = new Array();
var stationLabel = document.getElementById("stationName");
var intervalGraph;
var requests = [];

// Temporary fake value
var a = 0;

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
        Get("api/v1/station/" + station + "?group_by=hour");

        intervalGraph = setInterval(function() {
            Get("api/v1/station/" + station + "?group_by=hour", true);
        }, 10000);
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

        document.getElementById("rainfall").classList.remove('dot-green');
        document.getElementById("rainfall").classList.add('dot-red')
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
        options: {}
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
        datasets: dataset
    };
    chart.update();
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
            var temp = createTimeArray();
            var pressureStation = createTimeArray();
            var rainfall = createTimeArray();

            // Retrieving weather object
            var object = data.item.weather["2020-01-28"];

            // Temporary fake data
            if (update) {
                a += 1 ;
            }

            // Sorting data
            for (item in object){
                temp[object[item]["time"].toString().slice(0, -3)] = object[item]["temperature"];
                pressureStation[object[item]["time"].toString().slice(0, -3)] = object[item]["air_pressure_station"];
                rainfall[object[item]["time"].toString().slice(0, -3)] = object[item]["rainfall"] + a;
            }

            // Create dataset line graph, temperature
            datasets[0] = [{
                label: 'Temperature',
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ')',
                data: listComprehension(temp, false)
            }];

            // Create dataset bar graph
            datasets[1] = [{
                label: 'Air Pressure Station',
                backgroundColor: 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ')',
                barPercentage: 0.5,
                barThickness: 6,
                maxBarThickness: 8,
                minBarLength: 2,
                data: listComprehension(pressureStation, false)
            }];

            // Create dataset line graph, rainfall
            datasets[2] = [{
                label: 'Rainfall',
                backgroundColor: 'rgba(0, 0, 0, 0)',
                borderColor: 'rgb(' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ', ' + Math.floor((Math.random() * 255) + 1) + ')',
                data: listComprehension(rainfall, false)
            }];

            // Update line graph
            if (update) {
                updateLineGraph(charts[2], datasets[2], listComprehension(rainfall))
            } else {
                // Creating temperature
                charts[0] = createLineGraph(datasets[0], listComprehension(temp), ctxTemperature);

                // Creating air pressure station
                charts[1] = createBarGraph(datasets[1], listComprehension(pressureStation), ctxAirPressureStation);

                // Creating rainfall
                charts[2] = createLineGraph(datasets[2], listComprehension(rainfall), ctxRainfall);
                document.getElementById("rainfall").classList.remove('dot-red');
                document.getElementById("rainfall").classList.add('dot-green');
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