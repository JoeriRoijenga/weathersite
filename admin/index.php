<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>ABCWeather: Admin</title>
</head>
<body>
<?php
session_start();
if ($_SESSION['priv_level'] != 2) {
  header("Location: ../login.php");
}
echo "Hi, admin!";

?>
</body>
</html>

