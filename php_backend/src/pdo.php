<?php


require_once "response.php";


function create_pdo() {
    $dsn = getenv("DB_DSN");
    $username = getenv("DB_USERNAME");
    $password = getenv("DB_PASSWORD");
    if ($dsn === false || $username === false) {
        response_error(ERROR_NO_DB,
            "environment variable DB_DSN or DB_USERNAME is not set");
    }
    try {
        $pdo = new PDO(
            $dsn,
            $username,
            $password,
        );
    } catch (PDOException $e) {
        response_error(ERROR_PDO_FAILED,
            "failed to create pdo: " . $e->getMessage());
    }
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) != "mysql") {
        response_error(ERROR_PDO_DRIVER_UNSUPPORTED,
            "wrong pdo driver, only mysql is supported");
    }
    $GLOBALS["pdo"] = $pdo;
}


function get_pdo() {
    return $GLOBALS["pdo"];
}


?>
