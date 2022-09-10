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
                orders.product_id,
                products.name AS product_name,
                products.price AS product_price,
                products.image_url AS product_image_url,
                orders.payment_method_id,
                payment_methods.name AS payment_method_name,
                orders.phone_number,
                orders.first_name,
                orders.second_name,
                orders.is_entity,
                orders.contract_number,
                orders.created_at
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
        $order["phone_number"] = trim($order["phone_number"]);
        $order["first_name"] = trim($order["first_name"]);
        $order["second_name"] = trim($order["second_name"]);
        $order["contract_number"] =
            $order["contract_number"] == null ? null :
            trim($order["contract_number"]);

        $stm->bindParam("product_id" . $i, $order["product_id"]);
        $stm->bindParam("payment_method_id" . $i, $order["payment_method_id"]);
        $stm->bindParam("phone_number" . $i, $order["phone_number"]);
        $stm->bindParam("first_name" . $i, $order["first_name"]);
        $stm->bindParam("second_name" . $i, $order["second_name"]);
        $stm->bindParam("is_entity" . $i, $order["is_entity"], PDO::PARAM_BOOL);

        $stm->bindParam("contract_number" . $i,
            $order["contract_number"],
            $order["contract_number"] == null ?
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

