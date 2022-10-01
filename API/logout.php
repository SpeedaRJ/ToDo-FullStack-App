<?php
session_start(); // initialize session

$logout_output = array('error' => false); // make response array for client

// if method is post
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // clear session and cookies
    session_start();
    setcookie(session_name(), '', 100);
    session_unset();
    session_destroy();
    $_SESSION = array();
}

header("Content-type: application/json"); // set response header
echo json_encode($logout_output); // set response data
die(); // kill script
