<?php
if (!empty($_SESSION)){
    $username = $_SESSION['username'];
    $priv_level = $_SESSION['priv_level'];
}
?>
<div class="header">
    <div class="logoimg"><img src="/assets/abclogo.svg" alt="logo" height="60" about="Logo ABC"/></div>
    <div class="logotext">ABC</div>
</div>
<ul class="nav-menu">
	<li><a href="/">Home</a></li>
	<li><a href="/precipitation">Precipitation</a></li>
    <li><a href="/map">Map</a></li>
 	<?php if(isset($username)): ?>
        <li style="float:right"><a href="/logout">Logout</a></li>
    <?php else: ?>
        <li style="float:right"><a href="/login">Login</a></li>
    <?php endif; ?>

    <?php if(isset($priv_level) && $priv_level == 2): ?>
        <li style="float:right"><a href="#">Admin</a>
			<ul class="dropdown dropdown-nopadding">
				<li><a href='/admin/user/add'>Add user</a></li>
				<li><a href='/admin/users'>List users</a></li>
			</ul>
		</li>
    <?php endif; ?>
</ul>