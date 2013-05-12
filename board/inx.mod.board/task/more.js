// @link_with_parent

inx.mod.board.task.more = inx.panel.extend({

    constructor:function(p) {
        
        p.layout = "inx.layout.column";      
        p.style = {
            background:"#ededed",
            valign:"top",
            border:0,
            spacing:10,
        }  
        p.labelWidth = 100;
        
        this.base(p);
        this.cmd("createForm",p.data);
        
    },
    
    cmd_createForm:function(data) {
    
        this.cmd("add",{
            height:20,
            style:{
                background:"none",
                border:0
            }
        })
    
        var column1 = this.cmd("add",{
            type:"inx.form",
            labelWidth:100,
            style:{
                padding:0,
                background:"none",
                border:0
            },
            width:235
        });
        
        var column2 = this.cmd("add",{
            type:"inx.form",
            labelWidth:100,
            style:{
                padding:0,
                background:"none",
                border:0
            },
            width:235
        });
    
        column1.cmd("add",{
            type:"inx.textfield",
            name:"timeScheduled",
            value:data.timeScheduled,
            width:30,            
            label:"Планирую (ч.)"
        });
        
       column1.cmd("add",{            
            type:"inx.combo",
            width:"parent",
            value:data.projectID,
            text:data.projectTitle,
            name:"projectID",
            loader:{
                cmd:"board/controller/project/listProjectsSimple"
            },
            label:"Проект"
        });
        
        // Строим меню из списка статусов
        var menu = [];
        for(var i=0;i<data.statuses.length;i++) {
            menu.push({
                text:data.statuses[i].text,
                onclick:inx.cmd(this.id(),"changeStatus",data.statuses[i].id)
            });
        }
        
        column1.cmd("add",{
            type:"inx.button",
            air:true,
            text:"Изменить статус",
            icon:"trigger",
            menu:menu
        });
        
        column2.cmd("add",{
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
        
    },
    
    cmd_changeStatus:function(status) {
        this.fire("changeStatus",status);
    }
    
     
});