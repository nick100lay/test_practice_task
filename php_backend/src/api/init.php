<?php

require_once "preset.php";
require_once "response.php";
require_once "request.php";
require_once "pdo.php";
require_once "init_db.php";

preset();


$method = request_method();


if ($method != "POST") {
    response_unsupported_method();
}


create_pdo();

init_db();

response(array());


?>
