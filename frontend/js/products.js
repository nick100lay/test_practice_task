"use strict";


const container = $(".content > .container");
container.waitMe({
    effect : "roundBounce",
    text : "",
});

getProducts()
    .always(() => { container.waitMe("hide") })
    .done((products) => {
        products.forEach((product) => {
            container.append("<p>" + product.name + "</p>");
        });
    })
    .fail(() => { container.append("<p>ERROR</p>") });
container.waitMe("hide");
