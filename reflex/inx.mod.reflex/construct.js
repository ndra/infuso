// @include inx.panel

inx.ns("inx.mod.reflex").construct = inx.panel.extend({

    constructor:function(p) {
        p.style = {
            padding:20,
            border:0,
            spacing:20,
            height:"parent",
            vscroll:true            
        }        
        this.base(p);
        this.cmd("add",this.form)
        this.cmd("add",{
            type:"inx.button",
            text:"Создать (Ctrl+S)",
            labelAlign:"left",
            onclick:[this.id(),"requestCreate"]
        });
        
        inx.hotkey("ctrl+s",[this.id(),"requestCreate"]);
        this.on("submit",[this.id(),"requestCreate"]);
    },
    
    cmd_requestCreate:function() {
        var data = this.info("data");
        this.call({
            cmd:"reflex:editor:controller:createItem",
            data:this.info("data"),
            constructorID:this.constructorID            
        },[this.id(),"handleCreate"]);
        return false;
    },
    
    cmd_handleCreate:function(action) {
        if(!action)
            return;
            
        inx.service("reflex").action(action);    
            
        this.bubble("menuChanged");
        this.owner().cmd("destroy");
    }
     
});