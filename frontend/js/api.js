"use strict";


const BASE_URL = "http://localhost/";
const TIMEOUT = 500;


function getPaymentMethods() {
    return $.ajax(BASE_URL + "payment_methods.php", {
        type: "GET",
        dataType: "json",
        timeout: TIMEOUT,
    });
}

function getProducts(query) {
    return $.ajax(BASE_URL + "products.php", {
        type: "GET",
        data: query ? { query } : {},
        dataType: "json",
        timeout: TIMEOUT,
    });
}


function getProductById(id) {
    return $.ajax(BASE_URL + "products.php", {
        type: "GET",
        data: { id },
        dataType: "json",
        timeout: TIMEOUT,
    });
}


function postProducts(...products) {
    return $.ajax(BASE_URL + "products.php", {
        type: "POST",
        data: JSON.stringify(products),
        timeout: TIMEOUT,
    });
}


function getOrdersByPhoneNumber(phoneNumber) {
    return $.ajax(BASE_URL + "orders.php", {
        type: "GET",
        data: { phoneNumber },
        dataType: "json",
        timeout: 500,
    });
}


function getOrdersByContractNumber(contractNumber) {
    return $.ajax(BASE_URL + "orders.php", {
        type: "GET",
        data: { contractNumber },
        dataType: "json",
        timeout: TIMEOUT,
    });
}


function postOrders(...orders) {
    return $.ajax(BASE_URL + "orders.php", {
        type: "POST",
        data: JSON.stringify(orders),
        timeout: TIMEOUT,
    });
}
