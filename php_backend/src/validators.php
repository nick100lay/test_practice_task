<?php


require_once "response.php";

function validate_base(&$obj, $rules, $on_error) {
    $keys = array();
    foreach ($obj as $key => $val) {
        $keys[$key] = true;
    }

    foreach ($rules as $key => $rule) {
        $rule_type = $rule[0];
        $rule_mode = $rule[1];
        if (array_key_exists($key, $obj)) {
            $keys[$key] = true;
            $rule_json = null;
            if (gettype($rule_type) == "array") {
                $rule_json = $rule_type;
                $rule_type = "array";
            }
            if (gettype($obj[$key]) != $rule_type) {
                $on_error();
            } elseif ($rule_json !== null) {
                validate($obj[$key], $rule_json);
            }
            if (array_key_exists(2, $rule) && !$rule[2]($obj[$key])) {
                $on_error();
            }
        } elseif ($rule_mode != "optional") {
            $on_error();
        } elseif (array_key_exists("def", $rule)) {
            $obj[$key] = $rule["def"];
        }
        $keys[$key] = false;
    }

    foreach ($keys as $key => $val) {
        if ($val) {
            $on_error();
        }
    }
}


function validate_get($rules) {
    validate_base($_GET, $rules, function() { error_bad_get(); });
}


function validate_json($json, $rules) {
    validate_base($json, $rules, function() { error_bad_json(); });
}

?>
