"use strict";

(() => {

const urlParams = new URLSearchParams(window.location.search);
const queryText = urlParams.get("query");
const queryType = urlParams.get("queryType");


const content = $(".content");
const ordersContainer = $(".content > .orders-cont > .orders");


function queryOrders() {
    let query = $("#query").val();
    let queryType = $("#query-type").val();
    query = encodeURIComponent(query);

    window.location.href = `orders.html?query=${query}&queryType=${queryType}`;
}


$("#query").keypress(function(e) {
    if (e.which == 13) {
        queryOrders();
    }
})


$("#query-btn").on("click", () => {
    queryOrders();
});

if (!queryText || (queryType != "phoneNumber" && queryType != "contractNumber")) {
    ordersContainer.append("<p>Укажите запрос по номеру телефона или договора</p>");
    return;
}

if (queryType == "phoneNumber") {
    const regex = /^\+7 [0-9]{3} [0-9]{3}\-[0-9]{2}\-[0-9]{2}$/;
    if (!regex.test(queryText)) {
        ordersContainer.append("<p>Формат номера телефона должен быть '+7 xxx xxx-xx-xx'</p>");
        return;
    }
} else {
    const regex = /^[0-9]{1,20}$/;
    if (!regex.test(queryText)) {
        ordersContainer.append("Неправильный формат номера договора");
        return;
    }
}

$("#query").val(queryText);
$("#query-type").val(queryType).change();

content.waitMe({
    effect : "roundBounce",
    text : "",
});


function renderOrder(order) {
    return `
          <div class="order">
            <span class="order-text">Название: </span>
            <span class="order-title"><a href="product.html?id=${order.productId}">${order.productName}</a></span><br>

            <span class="order-text">Цена: </span>
            <span class="order-data">${order.productPrice} ₽</span><br>

            <span class="order-text">Способ оплаты: </span>
            <span class="order-data">${order.paymentMethodName}</span><br>

            <span class="order-text">Номер телефона: </span>
            <span class="order-data">${order.phoneNumber}</span><br>

            <span class="order-text">Имя: </span>
            <span class="order-data">${order.firstName}</span><br>

            <span class="order-text">Фамилия: </span>
            <span class="order-data">${order.secondName}</span><br>

            <span class="order-text">Юр лицо: </span>
            <span class="order-data">${order.isEntity == "1" ? "Да" : "Нет"}</span><br>

            ${order.isEntity == "1" ?
            `<span class="order-text">Номер договора: </span>
            <span class="order-data">${order.contractNumber}</span><br>` : ""
            }
          </div>
    `
}

(queryType == "phoneNumber" ?
    getOrdersByPhoneNumber(queryText) :
    getOrdersByContractNumber(queryText)
)
    .always(() => { content.waitMe("hide"); })
    .done((orders) => {
        if (!orders.length) {
            ordersContainer.append("<p>Ничего не было найдено</p>");
        }
        orders.forEach((order) => {
            ordersContainer.append(renderOrder(order));
        });
    })
    .fail(() => { ordersContainer.append("<p>Не удалось загрузить товары</p>") });




})();
