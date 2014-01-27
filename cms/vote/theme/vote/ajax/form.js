$(function() {

    $(".urxp1-submit").live("click",function(e) {
        
        // Определяем контейнер опроса
        var container = $(this).parents("form").first();
        var data = {
            options:[]
        };        
        
        // Собираем данные чекбоксов
        container.find(":checkbox:checked").each(function(){
            data.options.push($(this).attr("name"));
        });
        
        // Собираем данные радиокнопок
        container.find(":radio:checked").each(function(){
            data.options.push($(this).attr("value"));
        });
        
        // Собираем данные из текстовых полей
        data.text = container.find(":text").val();
        
        var voteID = container.find("[name=voteID]").attr("value");
        data.cmd = "vote:controller:vote";
        data.voteID = voteID;
        mod.cmd(data,function(r) {
            container.hide("fast");
            var div = $("<div>").html(r).hide();
            container.after(div);
            div.show("fast");
        });
        
    });
});