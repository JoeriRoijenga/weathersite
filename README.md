# weathersite

## API Routes
**/api/v1/stations**

Query params:
- stn | *Station number. Optional.*
- lat_start | *Latitude Start. Optional.*
- lat_end | *Latitude End. Optional.*
- long_start | *Longitude Start. Optional.*
- long_end | *Longitude Start. Optional*

**/api/v1/station/{stn}**

URL params:
- stn | *Station number. Required.*

Query params:
- group_by | *Group measurements by "minute" or "hour". Default: "minute".*
- group_type | *Group type, grouped values should show "min", "max" or "avg". Default: "avg".*
- end_date | *End date in Y-m-d format.*
- start_date | *Start date in Y-m-d format.*

**/api/v1/weather/latest**

Query params:
- order_by | *Order by. Order by numeric column. Default: "rainfall".*
- limit | *Limit. Amount of stations to show. Default: "10"*
- lat_start | *Latitude Start. Optional.*
- lat_end | *Latitude End. Optional.*
- long_start | *Longitude Start. Optional.*
- long_end | *Longitude Start. Optional*
    
