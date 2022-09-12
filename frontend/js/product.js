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


function clearPopupErrors() {
    popup.find(".popup > .popup-err-msg").remove();
}
clearPopupErrors();


function renderPopupError(msg) {
    const err = document.createElement("span");
    err.classList.add("popup-err-msg");
    err.innerHTML = msg;
    return err;
}


class ValidationError extends Error {
    constructor(message) {
        super(message);
        this.name = "ValidationError";
    }
}


function serializePopup() {

    const convert = {
        firstName: (val) => val.trim(),
        secondName: (val) => val.trim(),
        phoneNumber: (val) => val.trim(),
        paymentMethodId: (val) => val.trim(),
        isEntity: (val) => val == "1",
        contractNumber: (val) => val.trim(),
    };
    const validators = {
        firstName: (val) => {
            if (val.length == 0) {
                throw new ValidationError("Имя должно быть указано");
            }
            if (val.length >= 60) {
                throw new ValidationError("Имя слишком длинное");
            }
        },
        secondName: (val) => {
            if (val.length == 0) {
                throw new ValidationError("Фамилия должна быть указана");
            }
            if (val.length >= 60) {
                throw new ValidationError("Фамилия слишком длинная");
            }
        },
        paymentMethodId: (val) => {
            if (val.length == 0) {
                throw new ValidationError("Способ оплаты должен быть указан");
            }
        },
        phoneNumber: (val) => {
            if (val.length == 0) {
                throw new ValidationError("Номер телефон должен быть указан");
            }
            const regex = /^\+7 [0-9]{3} [0-9]{3}\-[0-9]{2}\-[0-9]{2}$/;
            if (!regex.test(val)) {
                throw new ValidationError("Номер телефон должен быть указан в формате '+7 xxx xxx-xx-xx'");
            }
        },
        isEntity: (val) => {},
        contractNumber: (val) => {
            if (val.length == 0) {
                throw new ValidationError("Номер договора должен быть указан");
            }
            const regex = /^[0-9]{1,20}$/;
            if (!regex.test(val)) {
                throw new ValidationError("Неправильный формат номера договора");
            }
        },
    }
    const json = {}
    let result = json;

    popup.find(".popup").children().each((idx, entry) => {
        const name = entry.name;
        if (!name) {
            return;
        }
        if (entry.disabled) {
            return;
        }
        const val = convert[name](entry.value);
        try {
            validators[name](val);
        } catch (e) {
            if (e instanceof ValidationError) {
                result = null;
                entry.before(renderPopupError(e.message));
            } else {
                throw e;
            }
        }
        json[name] = val;
    });
    return result;
}

popup.find("#send-btn").on("click", () => {
    clearPopupErrors();
    const json = serializePopup();
    if (json === null) {
        return;
    }
    json["productId"] = productId;
    popup.waitMe({
        effect : "roundBounce",
        text : "",
    });
    postOrders(json)
        .always(() => {
            popup.waitMe("hide");
        })
        .done(() => {
            popup.hide();
        })
        .fail((err) => {
            popup.find(".popup").prepend(
                renderPopupError("Не удалось создать заказ"));
        });
});


function renderPaymentMethod(pm) {
    return `
      <option value="${pm.id}">${pm.name}</option>
    `;
}

popup.hide();

const productId = urlParams.get("id");

if (productId === null) {
    content.append("<p>ID товара не указан</p>");
    return;
}

content.waitMe({
    effect : "roundBounce",
    text : "",
});

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

