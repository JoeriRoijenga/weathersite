
<div class="container-fluid w-50">
	<div class="jumbotron">
	<h1>Add user:</h1>
	<form action="/admin/user/add" method="post">
		<div class="form-group">
			<label for="username">Username:</label>
			<input type='text' class="form-control" name='username' id="username" placeholder='Enter username' autofocus='true'>
		</div>
		<div>
			<label for="password">Password:</label>
			<input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
		</div>
		<div class="form-group">
			<label for="priv">Privilege:</label><br>
			<select class="form-control" name="priv" id="priv">
				<option value=0>Guest</option>
				<option value=1>User</option>
				<option value=2>Admin</option>
			</select>
		</div>
		<button type="submit" class="btn btn-primary">Add</button>
		<label style="color:red"><?php if($err == 1) { echo "Invalid login";}?></label>
	</form>
</div>
</div>
