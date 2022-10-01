<?php
require_once "database_connection.php"; // include database connection
require_once "helper.php"; // include helper

$register_output = array('error' => false, 'username' => false); // make response array to send to client

// if method is post
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // get post data
    $username = safetyCheck($_POST['username']);
    $password = safetyCheck($_POST['password']);
    $password_rep = safetyCheck($_POST['repeat_password']);

    // validate if data is provided
    if (empty($username)) {
        $register_output["error"] = "Username is required";
    } elseif (empty($password)) {
        $register_output["error"] = "Password is required";
    } elseif (empty($password_rep)) {
        $register_output["error"] = "Password repeat is required";
    } elseif ($password != $password_rep) {
        $register_output["error"] = "The two passwords do not match"; // validate if passwords match
    } elseif (strlen($username) > 60) {
        $login_output["error"] = "Username too long.";
    } elseif (strlen($password) < 8) {
        $login_output["error"] = "Password too short.";
    } elseif (strlen($password_rep) < 8) {
        $login_output["error"] = "Repeat of password too short.";
    }

    // if error not present
    if (!$register_output["error"]) {
        $user_check = "SELECT * FROM `users` WHERE `username` = '$username' LIMIT 1"; // prepare user query

        $result = mysqli_query($connection, $user_check); // execute query

        if ($result->num_rows > 0) {
            $register_output["error"] = "Username already exists"; // if query result isn't empty username already exists
        } else {
            // if query is empty
            $password = password_hash($password, PASSWORD_DEFAULT); // hash password
            $acronym = strtoupper(substr($username, 0, 2)); // get acronym from username

            $insertion = "INSERT INTO `users` (`username`, `acronym`, `password`) VALUES ('$username', '$acronym', '$password')"; // add user to database

            mysqli_query($connection, $insertion); // execute query

            $register_output["username"] = $username; // set username for the client
        }
    }
}

header("Content-type: application/json"); // set response header
echo json_encode($register_output); // set response data
die(); // kill script, php does closes connection
