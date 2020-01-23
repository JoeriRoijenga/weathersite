var ctx = document.getElementById('myChart').getContext('2d');
var chart = null;

function startChart() {
    var station = document.getElementById('currentStation').value;

    var dataset = null;

    if (station == 1) {
        dataset = [{
            label: 'Temperature',
            backgroundColor: 'rgba(0, 0, 0, 0)',
            borderColor: 'rgb(255, 122, 173)',
            data: [0, 45, 30, 20, 2, 5, 10]
        },
        {
            label: 'Rainfall',
            backgroundColor: 'rgba(0, 0, 0, 0)',
            borderColor: 'rgb(255, 0, 173)',
            data: [45, 30, 5, 0, 2, 20, 100]
        }];
        createChart(dataset);
    } else if (station == 2) {
        dataset = [{
            label: 'My First dataset',
            backgroundColor: 'rgba(0, 0, 0, 0)',
            borderColor: 'rgba(0, 0, 0, 0.1)',
            data: [0, 10, 5, 2, 20, 30, 45]
        }];
        createChart(dataset);
    } else {
        destroyChart();
    }
}

function destroyChart() {
    if (chart != null) {
        chart.destroy();
    }
}
function createChart(dataset) {
    destroyChart();
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: dataset
        },
        options: {}
    });
}

