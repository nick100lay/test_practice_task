<?php


require_once "pdo.php";
require_once "response.php";


function get_payment_methods() {
    $pdo = get_pdo();

    $sql =
        "
        SELECT id, name
            FROM payment_methods
        ";
    $stm = $pdo->prepare($sql);
    $stm->execute();
    $errors = $stm->errorInfo();
    if ($errors[2] !== null) {
        error_server("failed to get payment methods");
    }
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}


?>
