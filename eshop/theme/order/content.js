// Обновляем корзину при получении сообщения об ее изменении
mod.on("eshop_cartChanged",function() {
    var fn = function(p) { $(".x9d31rs9hj").html(p); }
    mod.cmd({cmd:"eshop:order:action:getCart"},fn);
});

// Если мы нажали на кнопку удаления товара
$(".eshop-delete").live("click",function() {
    var id = $(this).attr("eshop:id");
    mod.cmd({cmd:"eshop:order:action:deleteItem",itemID:id})
});

// Если мы нажали на кнопку редактирования товара
$(function() {
    var editor = $("#eshop-itemEditor");
    var editID = null;
    
    var updatePosition = function() {
        var o = $("#eshop-edit-"+editID).position();
        if(!o) return;
        editor.css({
            top:o.top,
            left:o.left-25
        });
    }
    setInterval(updatePosition,100);
    
    $(".eshop-change").live("click",function() {        
        editID = $(this).attr("eshop:id");
        editor.appendTo("body").css({display:"block"});
        updatePosition();
        var input = $("#eshop-quantity");
        input.data("itemID",editID);        
        input.val($(this).val()).data("lastval",$(this).val()).focus().select();
    });
});
