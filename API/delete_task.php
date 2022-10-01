<?php
require_once "database_connection.php"; // include database connection
require_once "helper.php"; // include helper

$delete_output = array('error' => false); // make response array to send to client

// if method is post
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // get post data
    $task_id = safetyCheck($_POST['id']);

    // validate if task ID is provided and correct
    if (empty($task_id) || !is_numeric(trim($user_id))) {
        $tasks_output["error"] = "Task ID not valid";
    }

    // if error not present
    if (!$delete_output["error"]) {
        $update = "DELETE FROM `tasks` WHERE `id` = '$task_id'"; // prepare deletion query

        mysqli_query($connection, $update); // execute query
    }
}

header("Content-type: application/json"); // set response header
echo json_encode($delete_output); // set response data
die(); // kill script, php does closes connection
