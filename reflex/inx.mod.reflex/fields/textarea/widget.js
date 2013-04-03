// @link_with_parent
// @include inx.dialog

inx.mod.reflex.fields.textarea.widget = inx.dialog.extend({

    constructor:function(p) {    
        p.width = 320;
        p.title = "Выберите виджет";
        p.items = [{
            type:"inx.list",
            loader:{cmd:"reflex/editor/fieldController/textfield/listWidgets"},
            onitemclick:[this.id(),"selectWidget"]
            
        }]    
        this.base(p);
    },
    
    cmd_selectWidget:function(name) {
        this.fire("selectWidget",{params:{name:name}});
        this.task("destroy");
    }
    
})