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

if($_GET){
	$userid = $_GET['id'];
	$action = $_GET['action'];
	if ($action == "delete") {
		$sql = "DELETE FROM users WHERE user_id ='$userid'";
		$query = mysqli_query($db, $sql);
		header("Location: listuser.php");
	}
	$sql = "SELECT * FROM users WHERE user_id ='$userid'";
	$query = mysqli_query($db, $sql);
	$result = mysqli_fetch_assoc($query);
	$username = $result['username'];
} 

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	$userid = $_POST['id'];
	$input_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$input_priv = $_POST['priv'];

	$sql = "UPDATE users SET password = '$input_password', priv_level = '$input_priv' WHERE user_id = '$userid'";
	mysqli_query($db, $sql);	
}
?>

		<h1>Edit user:</h1>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<label>ID:</label><br>
			<input type='text' name='id' readonly value='<?php echo $userid;?>'><br>
			<label>Username:</label><br>
			<input type='text' readonly name='username' value='<?php echo $username;?>' autofocus='true'><br>
			<label>Password:</label><br>
			<input type="password" name="password" placeholder="Enter password"><br><br>
			<label>Privilege:</label><br>
			<select name="priv">
				<option value=0>Guest</option>
				<option value=1>User</option>
				<option value=2>Admin</option>
			</select>
			<button type="submit">Edit</button>
			<label style="color:red"><?php if($err == 1) { echo "Invalid login";}?></label>
		</form>
	</body>
</html>