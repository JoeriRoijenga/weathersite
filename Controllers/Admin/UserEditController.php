<?php

namespace Controllers\Admin;

use Database\Database;

class UserEditController extends \Controllers\Controller
{
    public function edituser()
    {
        $db = Database::getDB();
        include __DIR__ . '/../../Views/Admin/adminsession.php';
        $err = 0;

        if(isset($_GET['id'], $_GET['action'])){
            $userid = $_GET['id'];
            $action = $_GET['action'];
            if ($action == "delete") {
                $sql = "DELETE FROM users WHERE user_id ='$userid'";
                $query = mysqli_query($db, $sql);
                header("Location: listuser.php");
            }
            $sql = "SELECT * FROM users WHERE user_id ='$userid'";
            $query = mysqli_query($db, $sql);
            $result = mysqli_fetch_assoc($query);
            $username = $result['username'];
        } else {
            header("Location: /");
            exit(401);
        }

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $userid = $_POST['id'];
            $input_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $input_priv = $_POST['priv'];

            $sql = "UPDATE users SET password = '$input_password', priv_level = '$input_priv' WHERE user_id = '$userid'";
            mysqli_query($db, $sql);
        }


        $this->html("/Admin/edituser", [
            'userid' => $userid,
            'username' => $username,
            'err' => $err
        ]);
    }
}