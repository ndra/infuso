$(function() {
    $(".i96nzebywb .button").live("click",function() {        
        
        if(!confirm("Вы действительно хотите удалить подписку?")) {
            return;
        }
        
        var id = $(this).attr("subscription:id");
        
        mod.cmd({
            cmd:"user_subscription_action:unsubscribe",
            subscriptionID:id            
        },function(r) {
            if(r)
                window.location.reload();
        })
    })
})