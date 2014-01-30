// Если мы нажали на кнопку добавления товара
$(".eshop-buy").live("click",function() {
    var id = $(this).attr("eshop:id");
    mod.cmd({cmd:"eshop:order:action:addItem",itemID:id});
});

mod.on("eshop_addToCart",function(params) {
    $(".tiv5mvln5q-"+params.itemID).addClass("tiv5mvln5q-in");
});