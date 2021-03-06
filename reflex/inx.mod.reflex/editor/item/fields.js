// @link_with_parent
// @include inx.form

inx.mod.reflex.editor.item.fields = inx.panel.extend({
    
    constructor:function(p) {
    
        p.title = "Редактирование"; 
              
        p.style = {
            background:"white",
            vscroll:true,
            padding:0,
            spacing:20
        }
        
        
        var tbar = [];        
        for(var i in p.toolbar) {
            switch(p.toolbar[i]) {
            
                case "save":
                    tbar.push({
                        text:"Сохранить (Ctrl+S)",
                        icon:"save",
                        onclick:[this.id(),"save"]
                    });
                    break;
                    
                case "delete":
                    tbar.push({
                        text:"Удалить",
                        icon:"delete",
                        onclick:[this.id(),"deleteSelf"]
                    });
                    break; 
                    
                case "actions":
                    this.actions = inx({
                        type:"inx.mod.reflex.editor.actions",
                        actions:p.actions
                    }).cmd("sel",[p.index]);                    
                    tbar.push(this.actions); 
                    break;
                    
                default:
                    tbar.push(p.toolbar[i]);
                    break;
            }            
        }
            
        if(tbar.length)
            p.tbar = tbar;
        
        this.base(p);
        this.cmd("handleData",p.data);
    },
    
    cmd_handleData:function(data) {
        inx.hotkey("ctrl+s",[this.id(),"save"]);
        this.cmd("add",{
            type:"inx.button",
            text:"Сохранить",
            icon:"save",
            onclick:[this.id(),"save"]
        });
    },
    
    cmd_close:function() {
        this.owner().owner().cmd("stepBack");
    },
    
    cmd_save:function(close) {
    
        inx.mod.reflex.saveTime = new Date().getTime();
        var data = this.info("data");
        
        this.call({
            cmd:"reflex:editor:controller:save",
            index:this.index,
            data:data
        },[this.id(),"handleSave"]);
        
        return false;
    },
    
    cmd_handleSave:function(data) {
        if(!data) return;
        this.bubble("menuChanged");
        this.items().cmd("load");
    },
    
    /*cmd_handleSaveAndClose:function(data) {
        if(!data) return;
        this.bubble("menuChanged");
        this.cmd("close");
    }, */
    
    cmd_deleteSelf:function() {
        if(!confirm("Удалить элемент?")) return;
        this.call({cmd:"reflex:editor:controller:delete",ids:[this.index]},[this.id(),"handleDeleteSelf"]);
    },
    
    cmd_handleDeleteSelf:function() {
        this.bubble("menuChanged");
        this.cmd("close");
    }
    
});