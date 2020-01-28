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

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$input_username = $_POST['username'];
	$input_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$input_priv = $_POST['priv'];

	$sql = "INSERT INTO users(username, password, priv_level) VALUES('$input_username', '$input_password', $input_priv)";
	mysqli_query($db, $sql);	
}
?>

<h1>Add user:</h1>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<label>Username:</label><br>
		<input type='text' name='username' placeholder='Enter username' autofocus='true'><br>	
		<label>Password:</label><br>
		<input type="password" name="password" placeholder="Enter password"><br><br>
		<label>Privilege:</label><br>
		<select name="priv">
			<option value=0>Guest</option>
			<option value=1>User</option>
			<option value=2>Admin</option>
		</select>
		<button type="submit">Add</button>
		<label style="color:red"><?php if($err == 1) { echo "Invalid login";}?></label>
	</form>
</body>
</html>

