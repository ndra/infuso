// @include inx.form

inx.ns("inx.mod.reflex").conf = inx.form.extend({

    constructor:function(p) {
        this.base(p);
        inx.hotkey("ctrl+s",[this.id(),"save"]);
        this.cmd("requestData");
    },
    
    cmd_save:function() {
        this.call({
            cmd:"reflex:conf:save",
            confID:this.confID,
            value:this.info("data").field
        },[
            this.id(),
            "handleSave"
        ]);
        return false;
    },
    
    cmd_handleSave:function() {
        this.bubble("menuChanged");
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"reflex:conf:get",
            confID:this.confID
        },[
            this.id(),
            "handleData"]
        );
    },
    
    cmd_handleData:function(p) {
    
        this.items().cmd("destroy");
        this.field = inx(p);
        
        this.cmd("add",this.field)
        this.cmd("add",{type:"inx.button",text:"Сохранить",onclick:[this.id(),"save"]});
        return;
     }
     
});