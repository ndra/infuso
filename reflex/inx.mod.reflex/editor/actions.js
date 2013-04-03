// @link_with_parent
// @include inx.button

inx.mod.reflex.editor.actions = inx.button.extend({

    constructor:function(p) {
    
        p.text = "Функции";
        p.icon = "gear";
        p.air = true;        
        p.menu = [];
        p.selection = [];
        
        //------------------------------------------------------------------- Стандартные действия
        
        // Просмотр объекта
        p.menu.push({
            icon:"view",
            text:"Просмотр",
            onclick:[this.id(),"preview"]
        })
        
        // Выделить все
        p.menu.push("|");
        if(p.listData!==undefined)
            p.menu.push({
                text:"Выделить все",
                onclick:[this.id(),"selectAll"]
            })
        
        // Вырезать
        p.menu.push({
            text:"Вырезать",
            icon:"cut",
            onclick:[this.id(),"cut"]
        })
        
        // Операции с коллекциями
        if(p.listData!==undefined) {

            // Вставить        
            p.menu.push({
                text:"Вставить",
                icon:"paste",
                onclick:[this.id(),"paste"]
            })
        
            // Разделитель
            p.menu.push("|");
        
            // Вставить
            if(p.listData!==undefined)
                p.menu.push({
                    text:"Экспорт данных в CSV",
                    onclick:[this.id(),"export"]
                })
            
        }

        if(p.actions.length)
            p.menu.push("|");

        //------------------------------------------------------------------- Пользовательские действия
        
        for(var i in p.actions) {
            var action = p.actions[i];
            if(!action.onclick) action.onclick = inx.cmd(this,"action",action);
            p.menu.push(action);
        }
                                
        this.base(p);
    },
    
    cmd_cut:function() {
        inx.mod.reflex.editor.buffer = this.selection;
        inx.msg("Скопировано объектов: "+this.selection.length);
    },
    
    cmd_paste:function() {
        items = inx.mod.reflex.editor.buffer;
        if(!items) return;
        if(!items.length) return;
        var p = this.owner().owner().info("list");
        p.cmd = "reflex:editor:controller:paste";
        p.items = items;
        this.call(p,[this.id(),"handleAction"]);
        inx.mod.reflex.editor.buffer = null;
    },
    
    cmd_preview:function() {
        for(var i in this.selection)
            window.open("/reflex_editor_controller/view/id/"+this.selection[i]);
    },
    
    cmd_selectAll:function() {
        this.owner().owner().cmd("selectReallyAll");
    },
    
    cmd_sel:function(sel) {
        this.selection = sel;
    },
    
    cmd_setSerializedCollection:function(c) {
        this.serializedCollection = c;
    },
    
    cmd_action:function(p) {
    
        if(p.dlg) {
            p.dlg.ids = this.selection;            
            inx(inx.deepCopy(p.dlg)).cmd("render").setOwner(this);
            return;
        }
    
        inx({
            type:"inx.mod.reflex.editor.actions.dlg",
            action:p.action,
            ids:this.selection
        }).cmd("render").on("complete",[this.id(),"handleAction"]);         
    },
    
    cmd_handleAction:function() {    
        this.bubble("refresh");
        this.bubble("menuChanged");
    },
    
// ----------------------------------------------------------------- Экспорт
    
    cmd_export:function() {
        inx({
            type:"inx.mod.reflex.editor.actions.csv",
            serializedCollection:this.serializedCollection
        }).cmd("render");
    },
    
    cmd_handleExport:function(file) {
        window.location.href = file;
    }

// -----------------------------------------------------------------
    
});