"use strict";

(() => {

const urlParams = new URLSearchParams(window.location.search);
const queryText = urlParams.get("query");

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

getProducts(queryText)
    .always(() => { content.waitMe("hide"); })
    .done((products) => {
        if (!products.length) {
            productsContainer.append("<p>Ничего не было найдено</p>");
        }
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

function queryProducts(query) {
    query = encodeURIComponent(query);
    window.location.href = `products.html?query=${query}`;
}


if (queryText) {
    $("#query").val(queryText);
}


$("#query").keypress(function(e) {
    if (e.which == 13) {
        queryProducts(this.value);
    }
})


$("#query-btn").on("click", () => {
    queryProducts($("#query").val());
});




})();
