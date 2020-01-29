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

?>
<table>
	<tr>
		<th>ID</th>
		<th>Name</th>
		<th>Privilege</th>
	</tr>
	<?php
	while($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>" . $row['user_id'] . "</td><td>" . $row['username'] . "</td><td> " . $row['priv_level'] . "</td><td><a href='edituser.php?id=" . $row['user_id'] ."'>Edit</a></td><td><a href='edituser.php?action=delete&id=" . $row['user_id'] . "'>Delete</a></td></tr>";
    }
    ?>
</table>

</body>
</html>