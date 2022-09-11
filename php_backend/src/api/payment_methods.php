<?php


require_once "response.php";
require_once "request.php";
require_once "pdo.php";
require_once "payment_methods_db.php";


require_once "headers.php";


$method = request_method();


if ($method != "GET") {
    error_unsupported_method();
}


if ($method == "GET") {

    $result = get_payment_methods();

    response_json($result);
}

?>
