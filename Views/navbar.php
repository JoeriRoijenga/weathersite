<?php
session_start();
if (!empty($_SESSION)){
    $username = $_SESSION['username'];
    $priv_level = $_SESSION['priv_level'];
}
?>

<style>
    ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
      overflow: hidden;
      background-color: #333;
    }

    li {
      float: left;
    }

    li a {
      display: block;
      color: white;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
    }

    li a:hover {
      background-color: #111;
    }
</style>
<ul id="nav">
	<li><a href="index.php">Home</a></li>
	<li><a href="precipitation">Precipitation</a></li>
 	
 	
  <?php
  if (isset($username)) {
    echo '<li style="float:right"><a href="/logout">Logout</a></li>' ;
    
  } else {
   echo '<li style="float:right"><a href="/login">Login</a></li>' ;
  }
  if (isset($priv_level)) {
      if ($priv_level == 2) {
          echo '<li style="float:right"><a href="/admin/index">Admin</a></li>';
      }
  }
  ?>
  
</ul>