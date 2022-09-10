<?php


require_once "response.php";

function validate(&$json, $rules) {
    $keys = array();
    foreach ($json as $key => $val) {
        $keys[$key] = true;
    }

    foreach ($rules as $key => $rule) {
        $rule_type = $rule[0];
        $rule_mode = $rule[1];
        if (array_key_exists($key, $json)) {
            $keys[$key] = true;
            $rule_json = null;
            if (gettype($rule_type) == "array") {
                $rule_json = $rule_type;
                $rule_type = "array";
            }
            if (gettype($json[$key]) != $rule_type) {
                response_error(ERROR_INVALID_JSON_VALUES,
                    "invalid input json");
            } elseif ($rule_json !== null) {
                validate($json[$key], $rule_json);
            }
            if (array_key_exists(2, $rule) && !$rule[2]($json[$key])) {
                response_error(ERROR_INVALID_JSON_VALUES,
                    "invalid input json");
            }
        } elseif ($rule_mode != "optional") {
            response_error(ERROR_INVALID_JSON_VALUES,
                "invalid input json");
        } elseif (array_key_exists("def", $rule)) {
            $json[$key] = $rule["def"];
        }
        $keys[$key] = false;
    }

    foreach ($keys as $key => $val) {
        if ($val) {
            response_error(ERROR_INVALID_JSON_VALUES,
                "invalid input json");
        }
    }
}


?>
