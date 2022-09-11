<?php


require_once "pdo.php";
require_once "response.php";


function get_products($query, $query_type, $limit) {
    $pdo = get_pdo();

    $query = trim($query);

    $sql = "";

    if ($query == "") {
        $sql =
            "
            SELECT id, name, price, rating, description, image_url AS imageURL
                FROM products
                ORDER BY rating DESC
                LIMIT :limit
            ";
    } elseif ($query_type == "id") {
        $sql =
            "
            SELECT id, name, price, rating, description, image_url AS imageURL
                FROM products
                WHERE id = :query
                LIMIT :limit
            ";
    } else {
        $sql =
            "
            SELECT id, name, price, rating, description, image_url
                FROM products
                WHERE MATCH(name, description) AGAINST (:query)
                LIMIT :limit
            ";

        $sql =
            "
            SELECT search.id, search.name, search.price, search.rating, search.description, search.image_url AS imageURL
                FROM (" . $sql . ") as search
                ORDER BY search.rating DESC
            ";
    }
    $stm = $pdo->prepare($sql);
    $stm->bindParam("limit", $limit, PDO::PARAM_INT);
    if ($query != "") {
        $stm->bindParam("query", $query);
    }
    $stm->execute();
    $errors = $stm->errorInfo();
    if ($errors[2] !== null) {
        error_server("failed to get products");
    }
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}


function post_products($products) {
    $pdo = get_pdo();

    $values_sql = array();
    $i = 0;

    foreach ($products as $product) {
        $values_sql[] = "(:name" . $i .
                        ", :price" . $i .
                        ", :rating" . $i .
                        ", :description" . $i .
                        ", :image_url" . $i .
                        ")";
        ++$i;
    }


    if (empty($values_sql)) {
        return;
    }

    $sql = "INSERT INTO products(name, price, rating, description, image_url) VALUES " . implode(", ", $values_sql);

    $stm = $pdo->prepare($sql);

    $i = 0;
    foreach ($products as $product) {
        $stm->bindParam("name" . $i, trim($product["name"]));
        $stm->bindParam("price" . $i, $product["price"]);
        $stm->bindParam("rating" . $i, $product["rating"]);
        $product["description"] = $product["description"] == null ?
                null :
                trim($product["description"]);
        $product["imageURL"] = $product["imageURL"] == null ?
                null :
                trim($product["imageURL"]);

        $stm->bindParam("description" . $i, $product["description"],
            $product["description"] == null ?
                PDO::PARAM_NULL : PDO::PARAM_STR);
        $stm->bindParam("image_url" . $i, $product["imageURL"],
            $product["imageURL"] == null ?
                PDO::PARAM_NULL : PDO::PARAM_STR);
        ++$i;
    }

    $stm->execute();
    $errors = $stm->errorInfo();
    if ($errors[2] !== null) {
        error_server("failed to post products");
    }
}

?>

