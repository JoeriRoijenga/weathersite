<!DOCTYPE html>
<html lang="en">
<head>
<title>Page Title</title>
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

/* Style the header */
.header {
    padding: 80px;
    text-align: center;
    background: #0099ff;
    color: white;
}

/* Increase the font size of the h1 element */
.header h1 {
    font-size: 40px;
}

/* Style the top navigation bar */
.navbar {
    overflow: hidden;
    background-color: #333;
}

/* Style the navigation bar links */
.navbar a {
    float: left;
    display: block;
    color: white;
    text-align: center;
    padding: 14px 20px;
    text-decoration: none;
}

/* Right-aligned link */
.navbar a.right {
    float: right;
}

/* Change color on hover */
.navbar a:hover {
    background-color: #ddd;
    color: black;
}

/* Column container */
.row {  
    display: flex;
    flex-wrap: wrap;
}

/* Create two unequal columns that sits next to each other */
/* Sidebar/left column */
.side {
    flex: 30%;
    background-color: #f1f1f1;
    padding: 20px;
}

/* Main column */
.main {   
    flex: 70%;
    background-color: white;
    padding: 20px;
}

/* Fake image, just for this example */
.fakeimg {
    background-color: #aaa;
    width: 100%;
    padding: 20px;
}

/* Footer */
.footer {
    padding: 20px;
    text-align: center;
    background: #ddd;
}

/* Responsive layout - when the screen is less than 700px wide, make the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 700px) {
    .row {   
        flex-direction: column;
    }
}

/* Responsive layout - when the screen is less than 400px wide, make the navigation links stack on top of each other instead of next to each other */
@media screen and (max-width: 400px) {
    .navbar a {
        float: none;
        width:100%;
    }
}
</style>
</head>
<body>

<div class="header">
  <h1>climat express</h1>
  <p>Delivering your weatherdata like your pizza</p>
</div>

<div class="navbar"> 
  <a href="#">HOME</a>
  <a href="#">Offer</a>
  <a href="#">Order</a>
  <a href="#">Request</a>
  <a href="#" class="right">Account</a>
</div>

<div class="row">
  <div class="side">
      <h2>About us</h2>
      <h5>Our icon:</h5>
      <div class="fakeimg" style="height:200px;">ICON</div>
      <p>Delivering your weatherdata like your pizza</p>
      <h3>Contact Info</h3>
      <p>our Email: something@something.nl</p>
    
  </div>
  <div class="main">
      <h2>TITLE HEADING</h2>
      <h5>Title description, oktober 1, 2018</h5>
      <div class="fakeimg" style="height:200px;">Image</div>
      <p>Some text..</p>
      <p>Some more text.</p>
      <br>
      <h2>TITLE HEADING</h2>
      <h5>Title description, oktober 2, 2018</h5>
      <div class="fakeimg" style="height:200px;">Image</div>
      <p>Some text..</p>
      <p>Some more text.</p>
  </div>
</div>

<div class="footer">
  <h2>Footer</h2>
</div>

</body>
</html>
