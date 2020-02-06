<?php

namespace Database;

/**
 * Database
 *
 * Class Database
 * @package Database
 */
class Database
{
    /**
     * Get database
     *
     * @return false|\mysqli
     */
    public static function getDB()
    {
        // Standard database variables
        $dbhost = "localhost";
        $dbuser = "root";
        $dbpass = "root";
        $dbname = "abcweather";

        // Connect database
        $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

        // Error check
        if (mysqli_connect_errno()) {
            die("Database connectino failed." .
                mysqli_connect_error() . "(" .
                mysqli_connect_errno() . ")");
        }

        return $db;
    }

}