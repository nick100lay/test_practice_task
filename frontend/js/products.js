"use strict";

(() => {

const content = $(".content");
const productsContainer = $(".content > .products");
content.waitMe({
    effect : "roundBounce",
    text : "",
});


function renderProduct(product) {
    return `
        <div class="product">
          <img class="product-image" src="${product.imageURL}" alt="">
          <span class="product-title">${product.name}</span>
          <span class="product-price">${product.price} ₽</span>
        </div>
    `
}

getProducts()
    .always(() => { content.waitMe("hide"); })
    .done((products) => {
        products.forEach((product) => {
            let el = $(renderProduct(product));
            productsContainer.append(el);
            let id = product.id;
            el.on("click", () => {
                window.location.href = `product.html?id=${id}`;
            });
        });
    })
    .fail(() => { productsContainer.append("<p>Не удалось загрузить товары</p>") });

})();
