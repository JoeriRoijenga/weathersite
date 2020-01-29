<?php

namespace Controllers\Admin;

use Database\Database;

class UserAddController extends \Controllers\Controller
{

    public function adduser()
    {
        $db = Database::getDB();
        include __DIR__ . '/../../Views/Admin/adminsession.php';
        $err = 0;

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $input_username = $_POST['username'];
            $input_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $input_priv = $_POST['priv'];

            $sql = "INSERT INTO users(username, password, priv_level) VALUES('$input_username', '$input_password', $input_priv)";
            mysqli_query($db, $sql);
        }

        $this->html("/Admin/adduser", [
            'err' => $err
        ]);
    }
}