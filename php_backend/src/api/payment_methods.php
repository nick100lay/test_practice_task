<?php


require_once "preset.php";
require_once "response.php";
require_once "request.php";
require_once "pdo.php";
require_once "payment_methods_db.php";

preset();


$method = request_method();


if ($method != "GET") {
    response_unsupported_method();
}


create_pdo();


if ($method == "GET") {

    $result = get_payment_methods();

    response(array("payment_methods" => $result));
}

?>
