<!DOCTYPE html>
<html lang="en">
<head>
  <title>ABC: Weatherportal</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    margin: 0;
}

/* Column container */
.row {  
    display: flex;
    flex-wrap: wrap;
}

/* Create two unequal columns that sits next to each other */
/* Sidebar/left column */
.side {
    flex: 20%;
    background-color: #f1f1f1;
    padding: 20px;
}

/* Main column */
.main {   
    flex: 70%;
    background-color: white;
    padding: 20px;
}

/* Responsive layout - when the screen is less than 700px wide, make the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 700px) {
    .row {   
        flex-direction: column;
    }
}
</style>
</head>
<body>

<div class="row">
  <div class="side">
      <h2>Sidebar</h2>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque in porta est. In ac rutrum mauris, vel tincidunt orci. Sed gravida erat pharetra nulla posuere suscipit. Phasellus non egestas justo, vitae ultricies purus. Quisque luctus viverra augue quis placerat. In posuere quis risus quis ultrices. Quisque nec enim sollicitudin, molestie arcu id, bibendum lacus. Maecenas varius diam at eros posuere, eget semper mi commodo. Nulla semper libero in faucibus convallis. Curabitur hendrerit magna sapien, nec tempor turpis aliquet eget. Aenean ac malesuada odio, eu dapibus libero. Nulla porta purus et elit scelerisque, sit amet sodales eros tincidunt. </p>
    
  </div>
  <div class="main">
      <h2>Content</h2>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque in porta est. In ac rutrum mauris, vel tincidunt orci. Sed gravida erat pharetra nulla posuere suscipit. Phasellus non egestas justo, vitae ultricies purus. Quisque luctus viverra augue quis placerat. In posuere quis risus quis ultrices. Quisque nec enim sollicitudin, molestie arcu id, bibendum lacus. Maecenas varius diam at eros posuere, eget semper mi commodo. Nulla semper libero in faucibus convallis. Curabitur hendrerit magna sapien, nec tempor turpis aliquet eget. Aenean ac malesuada odio, eu dapibus libero. Nulla porta purus et elit scelerisque, sit amet sodales eros tincidunt. </p>
  </div>
</div>
</body>
</html>
