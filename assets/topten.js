/**
 * Asynchronous function for retrieving JSON data
 *
 * @param jsonURL
 * @param tableBody
 * @constructor
 */
function Get(jsonURL, tableBody){
    var Httpreq = new XMLHttpRequest();
    Httpreq.open("GET", jsonURL);
    Httpreq.send();
    Httpreq.onreadystatechange=function() {
        if (this.readyState === 4 && this.status === 200) {
            var data = JSON.parse(Httpreq.responseText);
            var name, rainfall = null
            console.log(data);


            for (var i = 0; i < data.items.length; i++) {
                var newRow = tableBody.insertRow();
                var newName = newRow.insertCell(0);
                var newRainfall = newRow.insertCell(1);

                var nameStr = data.items[i]["station"]["name"];
                nameStr = nameStr.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                    return letter.toUpperCase();
                });

                name = document.createTextNode(nameStr);
                rainfall = document.createTextNode(data.items[i]["rainfall"]);

                newName.appendChild(name);
                newRainfall.appendChild(rainfall);
            }
        }
    }
}

// Start
var tableBody = document.getElementById("top-ten");
Get("api/v1/weather/latest?lat_start=-5&lat_end=30", tableBody);