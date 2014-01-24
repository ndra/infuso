// @include inx.form,inx.tabs
/*-- /mod/bundles/reflex/inx.mod.reflex/meta.js --*/


inx.ns("inx.mod.reflex").meta = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            background:"#ededed",
            padding:20,
            spacing:20,
            border:0,
            vscroll:true,
            height:"parent",
            titleMargin:10
        } 
        
        p.vscroll = true;   
  
        p.items = [{
            type:"inx.mod.reflex.meta.route",
            title:"<div style='font-size:18px;' >Адрес страницы</div>",
            index:p.index,
            style:{
                border:0,
                background:"none"
            }
        },{
            type:"inx.mod.reflex.meta.title",
            title:"<div style='font-size:18px;' >Мета-данные</div>",
            index:p.index,
            style:{
                border:0,
                background:"none"
            }
        }];
        
        this.base(p);
    }
     
});


/*-- /mod/bundles/reflex/inx.mod.reflex/meta/route.js --*/


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

/*-- /mod/bundles/reflex/inx.mod.reflex/meta/title.js --*/


inx.mod.reflex.meta.title = inx.tabs.extend({

    constructor:function(p) {
        p.selectNew = false;      
        this.base(p);        
        this.cmd("requestData");
    },
        
    cmd_requestData:function() {
        this.call({
            cmd:"lang:controller:getAll",
        },[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data) {
    
        var that = this;
        var n = 0;
        for(var i in data) {
            n++;
            that.cmd("add",{
                type:"inx.mod.reflex.meta.title.lang",
                index:that.index,
                lang:i,
                title:data[i],
                lazy:true
            });
        }
        
        if(n < 2) {
            this.cmd("hideTabs");
        }        
        
    }
     
});

/*-- /mod/bundles/reflex/inx.mod.reflex/meta/title/lang.js --*/


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

