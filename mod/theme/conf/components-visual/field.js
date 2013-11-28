$(function() {

    $(".lud3vtwmim .delete").click(function() {
    
        if(!window.confirm("Удалить элемент?")) {
            return;
        }
    
        var container = $(this).parents(".lud3vtwmim");
    
        mod.cmd({
            cmd:"mod/conf/controller/removeItem",
            id:container.attr("data:id")
        },function() {
            window.location.reload();
        })
    });
    
    $(".lud3vtwmim .edit").click(function() {
    
        var container = $(this).parents(".lud3vtwmim");
        container.find(".form").show();
       
    });
    
    $(".lud3vtwmim .edit-submit").click(function() {
    
        var container = $(this).parents(".lud3vtwmim");
        mod.cmd({
            cmd:"mod/conf/controller/changeItem",
            id:container.attr("data:id"),
            value:container.find(".new-value").val(),
            type:container.attr("data:type")
        },function() {
            window.location.reload();
        })
       
    });

});