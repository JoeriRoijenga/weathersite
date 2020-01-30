<?php

namespace Controllers;

use Database\Database;

class AdminController extends Controller
{
    public function __construct()
    {
        // Former adminsession.php
        if (isset($_SESSION['priv_level'])) {
            if ($_SESSION['priv_level'] != 2) {
                header("Location: /login");
                exit(401);
            }
        }
    }

    public function home()
    {
        $this->html("/admin/home");
    }

    public function usersOverview()
    {
        $db = Database::getDB();
        $err = 0;
        $sql = "SELECT * FROM users";
        $result = mysqli_query($db, $sql);

        $this->html('/admin/users/overview', ['result' => $result]);
    }

    public function userAdd()
    {
        $db = Database::getDB();
        $err = 0;

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $input_username = $_POST['username'];
            $input_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $input_priv = $_POST['priv'];

            $sql = "INSERT INTO users(username, password, priv_level) VALUES('$input_username', '$input_password', $input_priv)";
            mysqli_query($db, $sql);
            header("Location: /admin/users");
            return;
        }

        $this->html("/admin/users/add", [
            'err' => $err
        ]);
    }

    public function userEdit($userid, $action)
    {
        $db = Database::getDB();
        $err = 0;

        if ($action == "delete") {
            $sql = "DELETE FROM users WHERE user_id ='$userid'";
            $query = mysqli_query($db, $sql);
            header("Location: /admin/users");
            return;
        }

        $sql = "SELECT * FROM users WHERE user_id ='$userid'";
        $query = mysqli_query($db, $sql);
        $result = mysqli_fetch_assoc($query);
        $username = $result['username'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $userid = $_POST['id'];
            $input_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $input_priv = $_POST['priv'];

            $sql = "UPDATE users SET password = '$input_password', priv_level = '$input_priv' WHERE user_id = '$userid'";
            mysqli_query($db, $sql);

            header("Location: /admin/users");
            return;
        }


        $this->html("/admin/users/edit", [
            'userid' => $userid,
            'username' => $username,
            'err' => $err
        ]);
    }

}