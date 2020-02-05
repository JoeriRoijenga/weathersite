<div class="container-fluid w-50">
	<div class="jumbotron">
	<h1>Edit user:</h1>
	<form action="/admin/user/<?= $userid ?>/edit" method="post">
		<div class="form-group">
			<label for="id">ID:</label>
			<input type='text' name='id' class="form-control" readonly value='<?= $userid ?>'>
		</div>
		<div class="form-group">
			<label for="username">Username:</label>
			<input type='text' readonly name='username' class="form-control" id="username" value='<?php echo $username;?>' autofocus='true'>
		</div>
		<div class="form-group">
			<label for="password">Password:</label>
			<input type="password" id="password" name="password" class="form-control" placeholder="Enter password">
		</div>
		<div class="form-group">
			<label for="priv">Privilege:</label><br>
			<select name="priv" class="form-control" id="priv">
				<option value=0>Guest</option>
				<option value=1>User</option>
				<option value=2>Admin</option>
			</select>
		</div>
		<button class="btn btn-primary" type="submit">Edit</button>
		<label style="color:red"><?php if($err == 1) { echo "Invalid login";}?></label>
	</form>
</div>
</div>
