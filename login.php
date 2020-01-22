<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>ABC: Weatherportal-Login</title>
	<link rel="stylesheet" type="text/css" href="font.css">
	<style>
		* {
			font-family: ABCSans;
		}
		body, html {
  			height: 90%;
  			display: grid;
		}
		#login-box {
			margin: auto;
		}
		#form-box {
			padding: 20px;
  			
  			background-color: #d9d9d9;
  			border-radius: 10px;
   		}

   		#logo-text {
			font-family: ABCSans-Black;
			font-size: 80px;
			display: inline-block;
			padding-left: 7px;
		}
		#logo-img {
			display: inline-block;
		}

		h1 {
			font-family: ABCSans-Bold;
		}
	</style>
</head>
<body>
<?php
session_start();
if (!empty($_SESSION)){
	header("Location: ../index.php");
}
$err = 0;
include 'database.php';
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	// todo: input sanitizen!
	if (ctype_alnum($_POST['username'])) {
		$input_username = $_POST['username'];
	} else {
		$err = 1;
	}
	
	$input_password = $_POST['password'];

	$sql = "SELECT user_id, username, password, priv_level FROM users WHERE username = '$input_username'";
	$data = mysqli_fetch_array(mysqli_query($db, $sql),MYSQLI_ASSOC);
	if (password_verify($input_password, $data["password"])) {
   		$_SESSION['username'] = $data['username'];
   		$_SESSION['priv_level'] = $data['priv_level'];
   		header("Location: ../index.php");
   	} else {
   		$err = 1;
   	}		
};
?>
<div id="login-box">
<div id="logo-img"><img src="res/abclogo.svg" alt="logo" height="60"></div><div id="logo-text">ABC</div>
<div id="form-box">
	<h1>Login</h1>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<label>Username:</label><br>
		<input type="text" name="username" placeholder="Enter username" autofocus="true"><br>
		<label>Password:</label><br>
		<input type="password" name="password" placeholder="Enter password"><br><br>
		<button type="submit">Log In</button>
		<label style="color:red"><?php if($err == 1) { echo "Invalid login";}?></label>
	</form>
</div>
</div>
</body>
</html>