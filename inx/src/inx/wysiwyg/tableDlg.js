// @include inx.dialog
// @link_with_parent

inx.wysiwyg.tableDlg = inx.dialog.extend({

    constructor:function(p) {
        p.title = "Вставить таблицу";
        p.width = 200;
        
        var rows = inx({
            type:"inx.textfield",
            width:50,
            label:"Строки",
            name:"rows",
            value:2
        }).task("focus");
        
        this.form = inx({
            type:"inx.form",
            labelWidth:100,
            border:0,
            listeners:{
                submit:[this.id(),"insertTable"]
            },
            items:[rows,{
                type:"inx.textfield",
                width:50,
                label:"Столбцы",
                name:"cols",
                value:2
            },{
                type:"inx.button",
                labelAlign:"left",
                text:"Вставить",
                onclick:[this.id(),"insertTable"]
            }]
        })
        
        p.items = [this.form];
        this.base(p);
        
    },
    
    cmd_insertTable:function() {
        this.fire("insertTable",this.info("data"));
        this.task("destroy");
    }
    
});