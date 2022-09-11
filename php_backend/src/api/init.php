<?php

require_once "response.php";
require_once "request.php";
require_once "pdo.php";
require_once "init_db.php";


$method = request_method();


if ($method != "POST") {
    error_unsupported_method();
}

init_db();

response_successful("DB init successfully");


?>
