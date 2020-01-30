<?php
//session_start();
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
	<li><a href="/">Home</a></li>
	<li><a href="/precipitation">Precipitation</a></li>
    <li><a href="/map">Map</a></li>
 	<?php if(isset($username)): ?>
        <li style="float:right"><a href="/logout">Logout</a></li>
    <?php else: ?>
        <li style="float:right"><a href="/login">Login</a></li>
    <?php endif; ?>

    <?php if(isset($priv_level) && $priv_level == 2): ?>
        <li style="float:right"><a href="/admin/home">Admin</a></li>
    <?php endif; ?>
</ul>