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
            // Retrieving data
            var data = JSON.parse(Httpreq.responseText);
            var name, country, rainfall = null
            var current = false;

            // Looping thorough data
            for (var i = 0; i < data.items.length; i++) {
                var newRow;

                // Check if row already exists
                if (tableBody.children.hasOwnProperty(i)){
                    newRow = tableBody.children[i];
                    newRow.innerHTML = "";
                }else{
                    newRow = tableBody.insertRow();
                }

                // Creating cells
                var newName = newRow.insertCell(0);
                var newCountry = newRow.insertCell(1);
                var newRainfall = newRow.insertCell(2);

                // Creating textnodes for the table cell
                name = document.createTextNode(data.items[i]["station"]["name"]);
                country = document.createTextNode(data.items[i]["station"]["country"]);
                rainfall = document.createTextNode(data.items[i]["rainfall"] + " mm");

                // Adding text nodes to the table cell
                newName.appendChild(name);
                newCountry.appendChild(country);
                newRainfall.appendChild(rainfall);

                current = "<strong>Top 10</strong> measured as of: " + data.items[i]['date'] + " " + data.items[i]['time'];
            }

            if (current !== false){
                document.getElementById("current").innerHTML = current;
            }
        }
    }
}

// Start
var tableBody = document.getElementById("top-ten");
Get("api/v1/weather/latest?lat_start=-5&lat_end=30", tableBody);
setInterval(function () {
    Get("api/v1/weather/latest?lat_start=-5&lat_end=30", tableBody);
}, 10000);