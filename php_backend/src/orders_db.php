<?php


require_once "pdo.php";
require_once "response.php";


function get_orders($query, $query_type) {
    $pdo = get_pdo();

    $query = trim($query);

    $sql = "";
    if ($query_type == "contract") {
        $sql = "WHERE orders.contract_number = :query";
    } else {
        $sql = "WHERE orders.phone_number = :query";
    }

    $sql = "SELECT
                orders.id,
                orders.product_id AS productId,
                products.name AS productName,
                products.price AS productPrice,
                products.image_url AS productImageURL,
                orders.payment_method_id AS paymentMethodId,
                payment_methods.name AS paymentMethodName,
                orders.phone_number AS phoneNumber,
                orders.first_name AS firstName,
                orders.second_name AS secondName,
                orders.is_entity AS isEntity,
                orders.contract_number as contractNumber,
                orders.created_at AS createdAt
                FROM orders
                INNER JOIN payment_methods
                ON orders.payment_method_id = payment_methods.id
                INNER JOIN products
                ON orders.product_id = products.id
            " . $sql;

    $stm = $pdo->prepare($sql);
    $stm->bindParam("query", $query);
    $stm->execute();
    $errors = $stm->errorInfo();
    if ($errors[2] !== null) {
        response_error(ERROR_DB_FAIL,
            "failed to get products");
    }
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}


function put_orders($orders) {
    $pdo = get_pdo();

    $values_sql = array();
    $i = 0;

    foreach ($orders as $order) {
        $values_sql[] = "(:product_id" . $i .
                        ", :payment_method_id" . $i .
                        ", :phone_number" . $i .
                        ", :first_name" . $i .
                        ", :second_name" . $i .
                        ", :is_entity" . $i .
                        ", :contract_number" . $i . ")";
        ++$i;
    }

    if (empty($values_sql)) {
        return;
    }

    $sql = "INSERT INTO orders(
                product_id,
                payment_method_id,
                phone_number,
                first_name,
                second_name,
                is_entity,
                contract_number) VALUES " . implode(", ", $values_sql);

    $stm = $pdo->prepare($sql);

    $i = 0;
    foreach ($orders as $order) {
        $order["phoneNumber"] = trim($order["phoneNumber"]);
        $order["firstName"] = trim($order["firstName"]);
        $order["secondName"] = trim($order["secondName"]);
        $order["contractNumber"] =
            $order["contractNumber"] == null ? null :
            trim($order["contractNumber"]);

        $stm->bindParam("product_id" . $i, $order["productId"]);
        $stm->bindParam("payment_method_id" . $i, $order["paymentMethodId"]);
        $stm->bindParam("phone_number" . $i, $order["phoneNumber"]);
        $stm->bindParam("first_name" . $i, $order["firstName"]);
        $stm->bindParam("second_name" . $i, $order["secondName"]);
        $stm->bindParam("is_entity" . $i, $order["isEntity"], PDO::PARAM_BOOL);

        $stm->bindParam("contract_number" . $i,
            $order["contractNumber"],
            $order["contractNumber"] == null ?
                PDO::PARAM_NULL : PDO::PARAM_STR);
        ++$i;
    }

    $stm->execute();
    $errors = $stm->errorInfo();
    if ($errors[2] !== null) {
        response_error(ERROR_DB_FAIL,
            "failed to put orders");
    }
}

?>

