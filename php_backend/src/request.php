<?php


require_once "response.php";


function request_to_json() {
    $json_str = file_get_contents('php://input');
    $json = json_decode($json_str, true);
    if (json_last_error() != JSON_ERROR_NONE) {
        error_bad_json();
    }
    return $json;
}


function request_method() {
    return $_SERVER['REQUEST_METHOD'];
}


?>
