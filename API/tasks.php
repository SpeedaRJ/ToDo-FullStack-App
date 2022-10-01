<?php
require_once "database_connection.php"; // include database connection
require_once "helper.php"; // include helper

$tasks_output = array('error' => false,  'tasks' => false); // make response array to send to client

// if method is post
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // get post data
    $user_id = safetyCheck($_POST['user_id']);
    $task_name = safetyCheck($_POST['task_name']);
    $task_description = safetyCheck($_POST['task_description']); // description isn't checked as null is allowed
    $last_changed = date('Y-m-d H:i:s'); // make date time of task creation
    $completed = 0; // make completion status for new task

    // validate if data is provided and correct
    if (empty($user_id) || !is_numeric(trim($user_id))) {
        $tasks_output["error"] = "User ID not set";
    } elseif (empty($task_name)) {
        $tasks_output["error"] = "Task name is required";
    }

    // if error not present
    if (!$tasks_output["error"]) {
        // prepare insert query
        $insertion = "INSERT INTO `tasks` (`task_name`, `last_changed`, `description`, `completed`, `user_id`) VALUES ('$task_name', '$last_changed', '$task_description', '$completed', '$user_id')";

        mysqli_query($connection, $insertion); // execute query
    }
}

// if method is get
if ($_SERVER['REQUEST_METHOD'] == "GET") {

    // get get data
    $user_id = safetyCheck($_GET['user_id']);

    // validate if ID is not set or is not numeric
    if (empty($user_id) || !is_numeric(trim($user_id))) {
        $tasks_output["error"] = "User ID not set or not numeric";
    }

    // if error not present
    if (!$tasks_output["error"]) {
        $sql = "SELECT * FROM `tasks` WHERE `user_id` = '$user_id'"; // prepare query

        $result = mysqli_query($connection, $sql); // execute query

        // check if user has tasks
        if ($result->num_rows > 0) {
            // transform query into array of objects
            $query = [];
            while ($entry = mysqli_fetch_object($result)) {
                $query[] = $entry;
            }

            $tasks_output["tasks"] = $query; // set array for the client
        } else {
            $tasks_output["tasks"] = []; // if no tasks, set empty array
        }
    }
}

header("Content-type: application/json"); // set response header
echo json_encode($tasks_output); // set response data
die(); // kill script, php does closes connection
