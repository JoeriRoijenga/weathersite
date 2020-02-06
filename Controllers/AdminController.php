<?php

namespace Controllers;

use Database\Database;

/**
 * Controller for all the admin pages
 *
 * Class AdminController
 * @package Controllers
 */
class AdminController extends Controller
{
    /**
     * Checking privilege levels
     *
     * AdminController constructor.
     */
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

    /**
     * Home method for the admin home page
     */
    public function home()
    {
        $this->html("/admin/home");
    }

    /**
     * user method for the user overview page
     */
    public function usersOverview()
    {
        $db = Database::getDB();
        $err = 0;
        $sql = "SELECT * FROM users";
        $result = mysqli_query($db, $sql);

        // Send result to html page
        $this->html('/admin/users/overview', ['result' => $result]);
    }

    /**
     * Adding a user to the database
     */
    public function userAdd()
    {
        // Get database
        $db = Database::getDB();
        $err = 0;

        // Check request mode
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            // Setting variables
            $input_username = $_POST['username'];
            $input_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $input_priv = $_POST['priv'];

            // Inserting into database
            $sql = "INSERT INTO users(username, password, priv_level) VALUES('$input_username', '$input_password', $input_priv)";
            mysqli_query($db, $sql);
            // Login success, return to user overview
            header("Location: /admin/users");
            return;
        }

        // Send data to html page
        $this->html("/admin/users/add", [
            'err' => $err
        ]);
    }

    /**
     * User edit changes
     *
     * @param $userid
     * @param $action
     */
    public function userEdit($userid, $action)
    {
        // Retrieve database
        $db = Database::getDB();
        $err = 0;

        // Check action
        if ($action == "delete") {
            // Remove user from table
            $sql = "DELETE FROM users WHERE user_id ='$userid'";
            mysqli_query($db, $sql);
            header("Location: /admin/users");
            return;
        }

        // Get user by id
        $sql = "SELECT * FROM users WHERE user_id ='$userid'";
        $query = mysqli_query($db, $sql);
        $result = mysqli_fetch_assoc($query);
        $username = $result['username'];

        // Check request mode
        if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
            // Setting variables
            $userid = $_POST['id'];
            $input_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $input_priv = $_POST['priv'];

            // Update user
            $sql = "UPDATE users SET password = '$input_password', priv_level = '$input_priv' WHERE user_id = '$userid'";
            mysqli_query($db, $sql);

            header("Location: /admin/users");
            return;
        }

        // Send data to html page
        $this->html("/admin/users/edit", [
            'userid' => $userid,
            'username' => $username,
            'err' => $err
        ]);
    }

}