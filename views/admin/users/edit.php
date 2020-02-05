<div class="form-box center">
	<h1>Edit user:</h1>
	<form action="/admin/user/<?= $userid ?>/edit" method="post">
		<label>ID:</label><br>
		<input type='text' name='id' readonly value='<?= $userid ?>'><br>
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
</div>