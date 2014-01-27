// @link_with_parent

inx.mod.reflex.meta.route = inx.panel.extend({

    constructor:function(p) {
    
        p.style.spacing = 10; 
    
        p.bbar = [
            {text:"Сохранить (Ctrl+S)",icon:"save",onclick:[this.id(),"save"]},"|",
            {text:"Удалить адрес",icon:"delete",onclick:[this.id(),"deleteRoute"]},
        ];
        this.base(p);
        inx.hotkey("ctrl+s",[this.id(),"save"]);
        this.cmd("requestData");
    },
    
    cmd_deleteRoute:function() {
        if(!confirm("Удалить этого метаданные объекта? (Сам объект при этом останется)")) return;
        this.call({cmd:"reflex:route:editor:delete",index:this.index},[this.id(),"requestData"]);
    },
    
    cmd_save:function() {
        if(!this.enableSave) return;
        this.call({cmd:"reflex:route:editor:save",index:this.index,data:this.info("data")},[this.id(),"handleSave"]);
        return false;
    },
    
    cmd_handleSave:function() {
        this.bubble("menuChanged");
    },
    
    cmd_createRoute:function() {
        this.call({cmd:"reflex:route:editor:save",index:this.index},[this.id(),"requestData"]);
    },
    
    cmd_requestData:function() {
        this.call({cmd:"reflex:route:editor:get",index:this.index},[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
    
        if(!data) {
            this.cmd("destroy");
            return;
        }
        
        inx(this).axis("bbar").cmd(data.form ? "show" : "hide");
        this.enableSave = false;
    
        this.items().cmd("destroy");
        if(data.error) {
            this.cmd("add",{
                type:"inx.panel",
                html:data.error,
                style:{
                    border:0,
                    background:"none"
                }
            });
            this.cmd("add",{type:"inx.button",text:"Создать",onclick:[this.id(),"createRoute"]});
            return;
        }
    
        this.enableSave = true;
        
        this.cmd("add",data.form);
        
        /*this.cmd("add",{
            type:"inx.button",
            text:"Сохранить (Ctrl+S)",
            onclick:[this.id(),"save"]
        });
        
        this.cmd("add",{
            type:"inx.button",
            text:"Удалить (Ctrl+S)",
            onclick:[this.id(),"save"]
        }); */
        
    }
     
});