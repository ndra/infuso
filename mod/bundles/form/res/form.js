var form = function(selector,hash) {

    $(selector).submit(function(e) {

        var form = $(this);
        e.preventDefault();
        var data = $(this).serializeArray();
        //собираем с полей типа файла данные
        $(selector).find("[type='file']").each(function(){
            var obj = {};
            obj.name = $(this).attr("name");
            obj.value = $(this).val();
            data.push(obj);
        });
        var ret = {};
        for(var i in data) {
            ret[data[i].name] = data[i].value;
        }
        var data = ret;
        delete data.cmd;

        mod.cmd({
            cmd:"form_validate:validate",
            data:data,
            hash:hash
        },function(d) {

            form.find(".lbdmv238az").hide("fast");
            form.find("input.error, textarea.error").removeClass("error");

            // Если форма валидна, отправляем ее
            if(d.valid) {
               
               //При срабатывании события afterValidation достаем событие 
               var event;
               $(selector).on("afterValidation.eventSaver", function(e){
                   event = e;
               });
               
               //запускаем событие afterValidation
               $(selector).trigger("afterValidation", data);
              
               
               //Если событие неыбло прервано то сабмитим форму
               if(!event.isDefaultPrevented()){
                   form.unbind("submit");
                   form.submit();        
               } 
               
               $(selector).off("afterValidation.eventSaver");     
                
            // Если форма не валидна, показываем сообщение об ошибке
            } else {

                var field = form.find("[name="+d.name+"]");
                var msg = form.find(".error-"+d.name);

                if(!field.length) {
                    mod.msg("Element <b>[name="+d.name+"]</b> not found inside <b>"+selector+"</b>",1);
                }

                if(!msg.length) {
                    mod.msg("Element <b>.error-"+d.name+"</b> not found inside <b>"+selector+"</b>",1);
                }

                // Фокусируемся на элементе с ошибкой если он видимый
                if(field.filter(":visible").length) {
                    field.focus();
                } else {
                    $("<input>").appendTo(msg).focus().remove();
                }

                msg.html(d.html).hide().addClass("lbdmv238az").show("fast", function(){
                    //запускаем событие fieldError
                    $(selector).trigger("fieldError", data);    
                });
                
                field.addClass("error");
            }
        });
    });

}
