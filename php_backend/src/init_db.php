<?php


require_once "response.php";
require_once "pdo.php";


function init_db() {

    $pdo = get_pdo();

    $sql =
    "
    CREATE TABLE products (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

        name VARCHAR(100) NOT NULL CHECK (CHAR_LENGTH(name) >= 3),
        price DECIMAL (11, 2) NOT NULL CHECK (price > 0),

        rating DECIMAL (3, 2) NOT NULL CHECK (rating >= 0 AND rating <= 5),

        description VARCHAR(200),
        image_url VARCHAR(200),

        FULLTEXT INDEX query_search_idx (name, description),
        INDEX rating_idx (rating)
    );
    CREATE TABLE payment_methods (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL
    );
    CREATE TABLE orders (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        product_id INT UNSIGNED NOT NULL,
        payment_method_id INT UNSIGNED NOT NULL,

        phone_number VARCHAR(20) NOT NULL CHECK (phone_number REGEXP '^\\\\+7 [0-9]{3} [0-9]{3}\\\\-[0-9]{2}\\\\-[0-9]{2}$'),
        first_name VARCHAR(60) NOT NULL CHECK (CHAR_LENGTH(first_name) > 0),
        second_name VARCHAR(60) NOT NULL CHECK (CHAR_LENGTH(second_name) > 0),

        is_entity BOOLEAN NOT NULL,

        contract_number varchar(20) CHECK (contract_number REGEXP '^[1-9]+[0-9]*$'),

        created_at DATETIME NOT NULL DEFAULT NOW(),

        CONSTRAINT entity_has_contract
            CHECK (NOT is_entity OR is_entity AND contract_number IS NOT NULL),

        CONSTRAINT individual_has_no_contract
            CHECK (is_entity OR NOT is_entity AND contract_number IS NULL),

        FOREIGN KEY (product_id)
            REFERENCES products(id),

        FOREIGN KEY (payment_method_id)
            REFERENCES payment_methods(id),

        INDEX phone_number_idx (phone_number),

        INDEX contract_number_idx (contract_number)
    );
    INSERT INTO payment_methods (name) VALUES ('MIR'), ('VISA'), ('MASTERCARD');
    ";


    $stm = $pdo->prepare($sql);
    $stm->execute();
    $errors = $stm->errorInfo();
    if ($errors[2] !== null) {
        error_server("failed to initialize DB");
    }
}


?>
