// @link_with_parent
// @include inx.dialog

inx.mod.reflex.editor.list.editCell = inx.dialog.extend({

    constructor:function(p) {
        p.width = 350;
        p.style = {
            padding:10,
            border:0,
            background:"#ededed"
        }
        p.autoDestroy = true;
        p.modal = false;
        p.title = "Редактирование ячейки";
        p.bbar = [
            {text:"Сохранить",icon:"save",onclick:[this.id(),"save"]},"|",
            {text:"Отмена",onclick:[this.id(),"destroy"]},
        ];
        this.base(p);
        
        this.call({
            cmd:"reflex:editor:controller:getField",
            editor:this.editor,
            name:this.fieldName
        },[this.id(),"handleField"]);   
         
        this.on("save",p.onsave);   
        this.on("submit","save");
        inx.hotkey("esc",[this.id(),"destroy"]);
    },
    
    cmd_handleField:function(data) {  
    
        if(!data || !data.editor) {
            this.task("destroy");
            return;
        }
         
        if(data.editor) {        
            data.editor.width = "parent";
            var field = inx(data.editor);        
            this.cmd("add",field);
            field.task("focus").task("select");
            this.fi = field;
        } 
    },
    
    cmd_save:function() {
        this.call({
            cmd:"reflex:editor:controller:saveField",
            editor:this.editor,
            name:this.fieldName,
            value:this.fi.info("value")
        },[this.id(),"handleSave"]);
    },
    
    cmd_handleSave:function() {
        this.fire("save");
        this.task("destroy");
    }
    
});