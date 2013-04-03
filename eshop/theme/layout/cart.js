mod.on("eshop_cartChanged",function() {
    var fn = function(p) { $(".kb6ejlbr6e").html(p); }
    mod.cmd({cmd:"eshop:order:action:getCartSmall"},fn);
});