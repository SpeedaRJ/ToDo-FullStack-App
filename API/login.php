<?php
session_start(); // initialize session
require_once "database_connection.php"; // include database connection
require_once "helper.php"; // include helper

$login_output = array('error' => false,  'id' => false, 'username' => false, 'acronym' => false); // make response array to send to client

// do if method post
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // get post data
    $username = safetyCheck($_POST['username']);
    $password = safetyCheck($_POST['password']);

    // validate if data is provided
    if (empty($username)) {
        $login_output["error"] = "Username field empty.";
    } elseif (empty($password)) {
        $login_output["error"] = "Password field empty.";
    } elseif (strlen($username) > 60) {
        $login_output["error"] = "Username too long.";
    } elseif (strlen($password) < 8) {
        $login_output["error"] = "Password too short.";
    }

    // if error not present
    if (!$login_output["error"]) {
        $sql = "SELECT * FROM `users` WHERE `username` = '$username' LIMIT 1"; // prepare user query

        $result = mysqli_query($connection, $sql); // execute query

        // if result is not empty
        if ($result->num_rows > 0) {
            $query = $result->fetch_assoc(); // fetch the next row of result

            $password_verify = password_verify($password, $query['password']); // verify password

            // if password verification
            if ($password_verify) {
                // set user data for the client
                $login_output['acronym'] = $query['acronym'];
                $login_output['id'] = $query['id'];

                // set user data in session for automatic login
                $_SESSION['username'] = $username;
                $_SESSION['acronym'] = $query['acronym'];
                $_SESSION['id'] = $query['id'];
                $_SESSION['logged_in'] = true;
            } else {
                $login_output['error'] = "Incorrect password."; // else password is incorrect
            }
        } else {
            $login_output['error'] = "This username isn't registered."; // if result is empty, the user isn't registered
        }
    }
}

// do if method get
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // set data from session for client
    $login_output['acronym'] = $_SESSION['acronym'];
    $login_output['id'] = $_SESSION['id'];
}

header("Content-type: application/json"); // set response header
echo json_encode($login_output); // set response data
die(); // kill script, php does closes connection