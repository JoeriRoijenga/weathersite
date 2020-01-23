<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>ABCWeather: Admin</title>
</head>
<body>
<?php
include '../database.php';
include 'adminsession.php';
$err = 0;
$sql = "SELECT * FROM users";
$result = mysqli_query($db, $sql);

	
	while($row = mysqli_fetch_assoc($result)) {
        echo $row['username'] . "(" . $row['user_id'] . ") " . $row['priv_level'] . "<br>";
    }

?> 

</body>
</html>

