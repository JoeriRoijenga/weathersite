<div class="login-box">
    <div class="center-logo">
        <div class="logo-img-login">
            <img src="/assets/abclogo.svg" alt="logo" height="60">
        </div>
        <div class="logo-text-login">ABC</div>
    </div>
    <div class="form-box center">
        <h1>Login</h1>
        <form action="/login" method="post">
            <label>Username:</label><br>
            <input type="text" name="username" class="form-control" placeholder="Enter username" autofocus="true"><br>
            <label>Password:</label><br>
            <input type="password" name="password" class="form-control" placeholder="Enter password"><br><br>
            <button type="submit" class="btn btn-primary">Log In</button>
            <label style="color:red"><?php if($err == 1) { echo "Invalid login";}?></label>
        </form>
    </div>
</div>