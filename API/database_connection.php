<?php
$host = "localhost"; // database host
$user = "rj"; // created user
$password = "connect2"; // password
$dbname = "todo"; // database name

$connection = mysqli_connect($host, $user, $password, $dbname); // establish connection with database

// check if connection is established
if ($connection === false) {
    die("ERROR: " . mysqli_connect_error()); // throw error if not
}
