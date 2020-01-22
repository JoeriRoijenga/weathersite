<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>ABCWeather: Login</title>
</head>
<body>
  <?php
  include 'database.php';
  if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
  	$input_username = $_POST['username'];
  	$input_password = $_POST['password'];

	$sql = "SELECT user_id, username, password, priv_level FROM users WHERE username = '$input_username'";
  	$result = mysqli_query($db, $sql);
  	$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
  	
  	if (password_verify($input_password, $row["password"])) {
    	echo 'Password is valid!';
    	session_start();
    	$_SESSION['username'] = $row['username'];
    	$_SESSION['priv_level'] = $row['priv_level'];
	} else {
    	echo 'Invalid password.';
	}

  	
  };
  


  ?>

  <h2>Login:</h2>
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" placeholder="Enter username">
    <label for="password">Password:</label>
    <input type="password" name="password" placeholder="Enter password">
    <button type="submit">Log In</button>
  </form>
</body>
</html>

