// @link_with_parent

inx.mod.board.task.extra = inx.panel.extend({

    constructor:function(p) {
        
        p.layout = "inx.layout.column";      
        p.style = {
            valign:"top",
            border:0,
            spacing:10,
        }  
        p.labelWidth = 100;
        
        this.base(p);
        this.cmd("createForm",p.data);
        
    },
    
    cmd_createForm:function(data) {
    
        var column1 = this.cmd("add",{
            type:"inx.form",
            labelWidth:40,
            style:{
                padding:0,
                background:"none",
                border:0
            },
            width:130
        });

        var column3 = this.cmd("add",{
            type:"inx.form",
            labelWidth:100,
            style:{
                padding:0,
                background:"none",
                border:0
            },
            width:140
        });
        
        var column2 = this.cmd("add",{
            type:"inx.form",
            labelWidth:100,
            style:{
                padding:0,
                background:"none",
                border:0
            },
            width:180
        });
        
        var column4 = this.cmd("add",{
            type:"inx.form",
            labelWidth:100,
            style:{
                padding:0,
                background:"none",
                border:0
            },
            width:110
        });
    
        column1.cmd("add",{
            label:"Цвет",
            value:data.color,
            labelAlign:"left",
            name:"color",
            type:this.info("type")+".color"
        });
        
        column2.cmd("add",{
            data:data,
            type:this.info("type")+".deadline"
        });
        
        column3.cmd("add",{
            type:"inx.textfield",
            label:"Планирую (ч)",
            width:"parent",
            value:data.timeScheduled,
            name:"timeScheduled"
        });
        
        column4.cmd("add",{
            data:data,
            type:"inx.button",
            text:"Сохранить",
            icon:"save",
            style:{
                color:"white",
                background:"red",
                fontSize:16
            },onclick:function() {
                this.owner().owner().owner().cmd("save");
            }
        });
        
    },
    
    cmd_changeStatus:function(status) {
        this.fire("changeStatus",status);
    }
    
     
});