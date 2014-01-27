// Отслеживаем обновление количества
$(function() {
    var input = $("#eshop-quantity");
    var check = function() {
        var itemID = input.data("itemID");
        if(!itemID) return;
        var v1 = input.val();
        var v2 = input.data("lastval");
        if(v1!=v2) {
            $("#eshop-quantity-error").html("");
            mod.cmd({
                cmd:"eshop:order:action:setQuantity",
                orderItemID:itemID,
                n:input.val()
            });
        }
        input.data("lastval",v1);
    }    
    setInterval(check,1000);
});

mod.on("eshop_cartItemError",function(p){
    $("#eshop-quantity-error").html(p.text);
});

// Кнопки "+" и "-"
$(function() {
    var input = $("#eshop-quantity");
    $("#eshop-quantity-minus").click(function() {
        var val = input.val()*1 || 0;
        val--;
        val = Math.max(val,1);
        input.val(val);
    });
});

$(function() {
    var input = $("#eshop-quantity");
    $("#eshop-quantity-plus").click(function() {
        var val = input.val()*1 || 0;
        val++;
        val = Math.max(val,1);
        input.val(val);
    });
});