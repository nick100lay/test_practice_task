<?php


require_once "response.php";
require_once "request.php";
require_once "pdo.php";
require_once "products_db.php";
require_once "validators.php";


require_once "headers.php";


$method = request_method();


if ($method != "GET" && $method != "POST") {
    error_unsupported_method();
}


if ($method == "GET") {

    $str_is_not_empty = function($query) {
        return trim($query) != "";
    };


    validate_get(array(
        "id" => array("string", "optional", $str_is_not_empty, "def" => ""),
        "query" => array("string", "optional", $str_is_not_empty, "def" => ""),
    ));

    $query = $_GET["query"];
    $id = $_GET["id"];
    $result = array();

    if ($id != "") {
        $result = get_products($id, "id", 1);
    } else {
        $result = get_products($query, "search", 30);
    }

    response_json($result);
}

if ($method == "POST") {
    $json = request_to_json();

    $name_validator = function($val) {
        $val = trim($val);
        return strlen($val) >= 3 && strlen($val) < 100;
    };

    $price_validator = function($val) {
        $val = trim($val);
        if (!is_numeric($val)) {
            return false;
        }
        if ($val[0] == "-" || $val[0] == "0") {
            return false;
        }
        $pos = strpos($val, ".");
        if ($pos !== false && $pos > 8) {
            return false;
        }
        return true;
    };

    $rating_validator = function($val) {
        return intval($val) >= 0 && intval($val) <= 5;
    };

    $description_validator = function($val) {
        $val = trim($val);
        return strlen($val) < 200;
    };


    $image_url_validator = function($val) {
        $val = trim($val);
        return strlen($val) < 200;
    };

    foreach ($json as &$product_json) {
        validate_json($product_json, array(
            "name" => array("string", "required", $name_validator),
            "price" => array("string", "required", $price_validator),
            "rating" => array("string", "required", $rating_validator),
            "description" => array("string", "optional", $description_validator, "def" => null),
            "imageURL" => array("string", "optional", $image_url_validator, "def" => null),
        ));
    }

    post_products($json);
    response_successful("Post products successfully");
}

?>
