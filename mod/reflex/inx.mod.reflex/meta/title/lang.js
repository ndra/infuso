// @link_with_parent

inx.mod.reflex.meta.title.lang = inx.panel.extend({

    constructor:function(p) {
        if(!p.style)
            p.style = {};
        p.style.background = "none";
        p.bbar = [
            {text:"Сохранить (Ctrl+S)",icon:"save",onclick:[this.id(),"save"]},"|",
            {text:"Удалить метаданные",icon:"delete",onclick:[this.id(),"deleteMeta"]},
        ];
        this.base(p);
        inx.hotkey("ctrl+s",[this.id(),"save"]);
        this.cmd("requestData");
    },
    
    cmd_deleteMeta:function() {
        if(!confirm("Удалить этого метаданные объекта? (Сам объект при этом останется)")) return;
        this.call({cmd:"reflex:meta:delete",index:this.index,lang:this.lang},[this.id(),"requestData"]);
    },
    
    cmd_save:function() {
        this.call({cmd:"reflex:meta:save",index:this.index,lang:this.lang,data:this.info("data")},[this.id(),"handleSave"]);
        return false;
    },
    
    cmd_handleSave:function() {
        this.bubble("menuChanged");
    },
    
    cmd_createMeta:function() {
        this.call({cmd:"reflex:meta:save",index:this.index,lang:this.lang},[this.id(),"requestData"]);
    },
    
    cmd_requestData:function() {
        this.call({cmd:"reflex:meta:get",index:this.index,lang:this.lang},[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
    
        inx(this).axis("bbar").cmd(data.form ? "show" : "hide");
    
        this.items().cmd("destroy");
        if(data.error) {
            this.cmd("add",{
                type:"inx.panel",
                html:data.error,
                style:{
                    border:0,
                    padding:10,
                    background:"none"
                }
            });
            this.cmd("add",{
                type:"inx.button",
                text:"Создать",
                onclick:[this.id(),"createMeta"]
            });
            return;
        }
    
        this.cmd("add",data.form);
        
    }
     
});