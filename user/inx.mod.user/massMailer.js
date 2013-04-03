// @include inx.form,inx.textarea,inx.button,inx.textfield

inx.ns("inx.mod.user");
inx.mod.user.massMailer = inx.form.extend({

    constructor:function(p) {
        p.items =[
            {label:"Тема",name:"subject"},
            {label:"Текс",name:"message",type:"inx.textarea"},
            {type:"inx.button",text:"Отправить",onclick:[this.id(),"send"]}
        ];
        
        p.border = 0;
        p.height = 400;

        this.base(p);
    },
    
    cmd_send:function() {
        var data = this.info("data");
        data.cmd = "user_massMailer:send";
        this.call(data);
    }
        
});
