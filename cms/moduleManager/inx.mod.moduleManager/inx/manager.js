// @include inx.tree,inx.code

inx.ns("inx.mod.moduleManager.inx").manager = inx.tree.extend({

    constructor:function(p) {
        p.root = "inx";
        p.tbar = [
            {icon:"plus",onclick:[this.id(),"newComponent"]},
            {icon:"delete",onclick:[this.id(),"deleteComponent"]}
        ];    
        p.loader = {cmd:"moduleManager_inxManager:listItems",module:p.module};
        if(!p.listeners) p.listeners = {};
        p.listeners.dblclick = [this.id(),"selectionChange"];
        p.listeners.beforeload = function(data) {data.path = this.info("path",data.id)};
        p.listeners.editComplete = [this.id(),"handleRename"];
        this.base(p);
    },
    
    cmd_selectionChange:function(a) {
        var path = this.info("path",a);
        if(!path) return;
        this.fire("openEditor",{
            type:"inx.mod.moduleManager.inx.editor",
            path:path,
            module:this.module,
            closable:true,
            name:"inx:"+path
        });
    },
    
    cmd_newComponent:function() {
        var sel = this.info("selection");
        if(!sel.length) return;
        var path = this.info("path",sel[0]);
        this.call(
            {cmd:"moduleManager_inxManager:newComponent",path:path,module:this.module},
            [this.id(),"handleNewComponent"],
            null,
            {parent:sel[0]}
        );
    },    
    cmd_handleNewComponent:function(data,meta) { this.cmd("load",meta.parent); },
    
    cmd_deleteComponent:function() {
        if(!confirm("Удалить компонент?"))
            return;
        var sel = this.info("selection");
        if(!sel.length) return;
        var path = this.info("path",sel[0]);
        var parent = this.info("node",sel[0]).parent;
        this.call(
            {cmd:"moduleManager_inxManager:deleteComponent",path:path,module:this.module},
            [this.id(),"handleDelete"],
            null,
            {path:path,parent:parent}
        );
    },
    cmd_handleDelete:function(path,meta) {
        this.fire("closeEditor","inx:"+meta.path);
        this.cmd("load",meta.parent);
    },    
    
    cmd_handleRename:function(id,newText,old) {
        var path = this.info("path",id);
        path = path.split("/");
        path.pop();
        path.push(old);
        var oldPath = path.join("/");
        var newPath = this.info("path",id);

        this.call({cmd:"moduleManager_inxManager:renameComponent",module:this.module,old:oldPath,"new":newPath});        
        this.fire("changeParams","inx:"+oldPath,{path:newPath});
    }
})
