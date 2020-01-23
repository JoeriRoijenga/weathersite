<?php
session_start();
if ($_SESSION['priv_level'] != 2) {
	header("Location: ../login.php");
};
?>