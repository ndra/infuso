$(function() {
    $(".ml6lpfzt .button").live("click",function() {        
        
        if(!confirm("Вы действительно хотите удалить социальный профиль?")) {
            return;
        }
        
        var id = $(this).attr("social:id");
        
        mod.cmd({
            cmd:"user_social_action:unlink",
            socialID:id            
        },function(r) {
            if(r)
                window.location.reload();
        })
    })
})