<?php

namespace Controllers;

use Database\Database;

class AccountController extends Controller
{

    public function login()
    {
        if (!empty($_SESSION)){
            header("Location: /");
            return;
        }
        $err = 0;

        $db = Database::getDB();

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            if (ctype_alnum($_POST['username'])) {
                $input_username = $_POST['username'];
            } else {
                $err = 1;
            }

            $input_password = $_POST['password'];
			
			if ($err == 0){
				$sql = "SELECT user_id, username, password, priv_level FROM users WHERE username = '$input_username'";
				$data = mysqli_fetch_array(mysqli_query($db, $sql),MYSQLI_ASSOC);
				if (password_verify($input_password, $data["password"])) {
					$_SESSION['username'] = $data['username'];
					$_SESSION['priv_level'] = $data['priv_level'];
					header("Location: /");
				} else {
					$err = 1;
				}
			}
        };

        $this->html("login", ['err' => $err]);
    }

    public function logout()
    {
        // Initialize the session.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();
        header("Location: /");
    }
}