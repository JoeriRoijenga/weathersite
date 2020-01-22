<?php
$dbhost="localhost";
$dbuser="root";
$dbpass="";
$dbname="abcweather";

$db= mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if(mysqli_connect_errno()) {
    die("De verbinding met de database is mislukt. ". 
        mysqli_connect_error()."(".
        mysqli_connect_errno().")");
    }
?>