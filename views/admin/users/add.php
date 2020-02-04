<h1>Add user:</h1>
<form action="/admin/user/add" method="post">
    <label>Username:</label><br>
    <input type='text' name='username' placeholder='Enter username' autofocus='true'><br>
    <label>Password:</label><br>
    <input type="password" name="password" placeholder="Enter password"><br><br>
    <label>Privilege:</label><br>
    <select name="priv">
        <option value=0>Guest</option>
        <option value=1>User</option>
        <option value=2>Admin</option>
    </select>
    <button type="submit">Add</button>
    <label style="color:red"><?php if($err == 1) { echo "Invalid login";}?></label>
</form>