<?php


function set_html_cont_type() {
    header("Content-Type: text/html; charset=urf-8");
}

function set_json_cont_type() {
    header("Content-Type: application/json; charset=utf-8");
}


function response_successful($msg) {
    set_html_cont_type();
    echo $msg;
}

function response_json($resp) {
    set_json_cont_type();
    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
    exit;
}


function error_bad_request($msg) {
    http_response_code(400);
    set_html_cont_type();
    echo $msg;
    exit;
}

function error_bad_json() {
    error_bad_request("Bad JSON");
}


function error_bad_get() {
    error_bad_request("Bad GET");
}


function error_server($msg) {
    http_response_code(500);
    set_html_cont_type();
    echo $msg;
    exit;
}


function error_unsupported_method() {
    http_response_code(405);
    set_html_cont_type();
    echo "Method not allowed";
    exit;
}


?>
