// @include inx.form

inx.ns("inx.mod.moduleManager").newModule = inx.form.extend({
    constructor:function(p) {
    
        p.width = 500;
    
        this.moduleName = inx({
            type:"inx.textfield",
            label:"Имя модуля"
        });
        
        p.items = [
            this.moduleName,
            {type:"inx.button",
            text:"Создать",
            labelAlign:"left",
            onclick:[this.id(),"newModule"]
        }];
        
        p.autoHeight = true;
        
        this.base(p);
    },
    
    cmd_newModule:function() {
        this.call({
            cmd:"moduleManager_newModule:create",
            name:this.moduleName.info("value")
        });
    }
    
});
