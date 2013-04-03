// @link_with_parent
// @include inx.dialog,inx.form

inx.mod.moduleManager.templateManager.editor.preferences = inx.dialog.extend({

    constructor:function(p) {       
        p.width = 500;
        p.title = "Свойства шаблона";        
        this.form = inx({
            type:"inx.form",
            labelWidth:0            
        });
        p.items = [this.form];
        this.base(p);
        this.cmd("requestData");
    },
    
    cmd_requestData:function() {
        this.call({cmd:"moduleManager:templateManager:getPreferences",id:this.templateID},[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(p) {
        this.form.cmd("add",{type:"inx.textarea",value:p.comments,name:"comments",label:"Комментарии к шаблону"});
        this.form.cmd("add",{type:"inx.button",text:"Сохранить",onclick:[this.id(),"save"]});
    },
    
    cmd_save:function() {
        this.call({cmd:"moduleManager:templateManager:savePreferences",id:this.templateID,data:this.info("data")},[this.id(),"destroy"]);
    }
    
})