<?php

namespace Database;

class Database
{

    public static function getDB()
    {
        $dbhost = "localhost";
        $dbuser = "root";
        $dbpass = "root";
        $dbname = "abcweather";

        $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        if (mysqli_connect_errno()) {
            die("Database connectino failed." .
                mysqli_connect_error() . "(" .
                mysqli_connect_errno() . ")");
        }
        return $db;
    }

}