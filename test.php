<a href=login.php>Login</a>
<a href=logout.php>Logout</a>
<a href=admin/index.php>Admin</a>
<br>
<?php

session_start();
echo $_SESSION['username'];
if (!empty($_POST)) {
	echo "<br>" . password_hash($_POST['password'], PASSWORD_DEFAULT);
}
?>

<h2>make password hash</h2>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<label for="password">Password:</label>
<input type="password" name="password" placeholder="Enter password">
<button type="submit">Log In</button>
</form>