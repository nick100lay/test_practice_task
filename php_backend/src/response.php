<?php


require_once "error_codes.php";


function set_html_cont_type() {
    header("Content-Type: text/html");
}


function response($resp) {
    $resp = array_merge(
        array("status" => ERROR_NONE),
        $resp);
    echo json_encode($resp, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}


function response_error($code, $msg) {
    $resp = array("status" => $code, "error_msg" => $msg);
    echo json_encode($resp);
    exit;
}


function response_unsupported_method() {
    set_html_cont_type();
    http_response_code(405);
    echo "<h1>Method not allowed</h1>";
    exit;
}


?>
