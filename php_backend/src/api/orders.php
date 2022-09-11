<?php


require_once "preset.php";
require_once "response.php";
require_once "request.php";
require_once "pdo.php";
require_once "orders_db.php";
require_once "validators.php";

preset();


$method = request_method();


if ($method != "GET" && $method != "PUT") {
    response_unsupported_method();
}


create_pdo();


$json = request_to_json();


$contract_number_validator = function($val) {
    $val = trim($val);
    if (!is_numeric($val)) {
        return false;
    }
    if ($val[0] == "-" || $val[0] == "0") {
        return false;
    }
    $pos = strpos($val, ".");
    if ($pos !== false) {
        return false;
    }
    return strlen($val) < 20;
};


$phone_number_validator = function($val) {
    $val = trim($val);
    return preg_match("/^\+7 \d\d\d \d\d\d\-\d\d\-\d\d$/", $val);
};


if ($method == "GET") {
    validate($json, array(
        "contractNumber" => array("string", "optional", $contract_number_validator, "def" => ""),
        "phoneNumber" => array("string", "optional", $phone_number_validator, "def" => ""),
    ));

    $result = array();

    $contract_number = $json["contractNumber"];
    $phone_number = $json["phoneNumber"];

    if ($contract_number != "") {
        $result = get_orders($contract_number, "contract");
    } elseif ($phone_number != "") {
        $result = get_orders($phone_number, "phone");
    }


    response(array("orders" => $result));
}

if ($method == "PUT") {
    $str_is_not_empty = function($query) {
        return trim($query) != "";
    };

    foreach ($json as &$order_json) {
        validate($order_json, array(
            "productId" => array("string", "required"),
            "paymentMethodId" => array("string", "required"),
            "phoneNumber" => array("string", "required", $phone_number_validator),
            "firstName" => array("string", "required", $str_is_not_empty),
            "secondName" => array("string", "required", $str_is_not_empty),
            "isEntity" => array("boolean", "required",),
            "contractNumber" => array("string", "optional", $contract_number_validator, "def" => null),
        ));
        if ($order_json["isEntity"] && $order_json["contractNumber"] == null) {
            response_error(ERROR_INVALID_JSON_VALUES,
                "invalid input json");
        }
    }

    put_orders($json);
    response(array());
}

?>
