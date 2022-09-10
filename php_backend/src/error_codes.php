<?php

const ERROR_NONE = 0;

// Failed to decode input json
const ERROR_WRONG_INPUT = 1;

// Invalid JSON values
const ERROR_INVALID_JSON_VALUES = 2;

// Environment variable
// DB_DSN or DB_USERNAME is not set
const ERROR_NO_DB = 3;

// Failed to create pdo
const ERROR_PDO_FAILED = 4;

// pdo driver is unsupported
const ERROR_PDO_DRIVER_UNSUPPORTED = 5;

// Fail with DB
const ERROR_DB_FAIL = 6;


?>
