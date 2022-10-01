<?php
// function to check post/get parameters
function safetyCheck($input)
{
    $input = trim($input); // strip white spaces
    $input = stripslashes($input); // unquotes a string or strips slashes from strings
    $input = htmlspecialchars($input); // checks for invalid characters in the given input
    return $input;
}
