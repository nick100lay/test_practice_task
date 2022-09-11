<?php


require_once "response.php";


function create_pdo() {
    $dsn = getenv("DB_DSN");
    $username = getenv("DB_USERNAME");
    $password = getenv("DB_PASSWORD");
    if ($dsn === false || $username === false) {
        error_server("environment variable DB_DSN or DB_USERNAME is not set");
    }
    try {
        $pdo = new PDO(
            $dsn,
            $username,
            $password,
        );
    } catch (PDOException $e) {
        error_server("failed to create pdo: " . $e->getMessage());
    }
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) != "mysql") {
        error_server("wrong pdo driver, only mysql is supported");
    }
    $GLOBALS["pdo"] = $pdo;
}


function get_pdo() {
    if (!array_key_exists("pdo", $GLOBALS)) {
        create_pdo();
    }
    return $GLOBALS["pdo"];
}


?>
