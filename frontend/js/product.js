"use strict";


(() => {


const urlParams = new URLSearchParams(window.location.search);
const content = $(".content");
const popup = $(".popup-cont");
const paymentMethodList = popup.find("#payment-method");

popup.find("#cancel-btn").on("click", () => {popup.hide();});
const contractNumberInput = popup.find("#contractNumber");
contractNumberInput.prop("disabled", contractNumberInput.value == "0");
popup.find("#isEntity").change(function() {
    contractNumberInput.prop("disabled", this.value == "0");
});


function validatePopup() {
}

function serializePopup() {
    popup.find(".popup").children().each((idx, entry) => {
        console.log(entry.name);
    })
}

popup.find("#send-btn").on("click", () => {
    popup.waitMe({
        effect : "roundBounce",
        text : "",
    });
    console.log(serializePopup());
});


function renderPaymentMethod(pm) {
    return `
      <option value="${pm.id}">${pm.name}</option>
    `;
}

popup.hide();

content.waitMe({
    effect : "roundBounce",
    text : "",
});

const productId = urlParams.get("id");

if (productId === null) {
    content.append("<p>ID товара не указан</p>");
    return;
}


function renderProduct(product) {
    return `
      <div class="cont-info">
        <div class="info-image">
          <img src="${product.imageURL}" alt="">
        </div>
        <div class="info">
          <div class="info-cont">
            <span class="info-title">${product.name}</span>
            <span class="info-price">${product.price} ₽</span>
            <span class="info-desc">${product.description}</span>
          </div>

          <button id="order-btn" class="info-btn">Заказать</button>
        </div>
      </div>
    `
}


getProductById(productId)
    .always(() => { content.waitMe("hide"); })
    .done((products) => {
        if (!products.length) {
            content.append("<p>Такого товара не существует</p>");
        } else {
            let el = content.append(renderProduct(products[0]));
            let btn = el.find("#order-btn");
            btn.on("click", () => { popup.show(); });
        }
    })
    .fail(() => { content.append("<p>Не удалось загрузить товар</p>") });


getPaymentMethods()
    .done((pms) => {
        pms.forEach((pm) => {
            paymentMethodList.append(renderPaymentMethod(pm));
        });
    });


})();

