<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/API/database_connection.php"; // include database connection
require_once $_SERVER['DOCUMENT_ROOT'] . "/API/helper.php"; // include helper

$updater_output = array('error' => false); // make response array to send to client

// if method is post
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // get post data
    $task_id = safetyCheck($_POST['id']);
    $update_type = safetyCheck($_POST['type']);
    $update_data = safetyCheck($_POST['value']);
    $last_changed = date('Y-m-d H:i:s'); // create date time for task update

    // validate if task ID is provided and correct
    if (empty($task_id) || !is_numeric(trim($task_id))) {
        $tasks_output["error"] = "Task ID not valid.";
    } elseif (empty($update_type) && empty($update_data)) { // data and type have to be provided
        $tasks_output["error"] = "No data is provided.";
    } elseif (!in_array($update_type, ["description", "task_name", "completed"])) { // update type has to be valid
        $tasks_output["error"] = "Invalid data type.";
    }

    // if error not present
    if (!$updater_output["error"]) {
        // if updating completion status
        if ($update_type == "completed") {
            $update_data = ($update_data == 'true') ? 1 : 0; // parse boolean to tiny int
        }

        $update = "UPDATE `tasks` SET `$update_type` = '$update_data', `last_changed` = '$last_changed' WHERE `id` = '$task_id'"; // prepare query
        echo $update;
        mysqli_query($connection, $update); // execute update query
    }
}

header("Content-type: application/json"); // set response header
echo json_encode($updater_output); // set response data
die(); // kill script, php does closes connection
