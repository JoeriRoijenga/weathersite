<?php
//session_start();
if (isset($_SESSION['priv_level'])) {
    if ($_SESSION['priv_level'] != 2) {
        header("Location: ../login");
        exit(401);
    }
}