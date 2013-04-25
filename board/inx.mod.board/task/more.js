// @link_with_parent

inx.mod.board.task.more = inx.panel.extend({

    constructor:function(p) {
        
        p.layout = "inx.layout.form";      
        p.style = {
            background:"#ededed",
            border:0,
            padding:20
        }  
        p.labelWidth = 100;
        
        this.base(p);
        this.cmd("createForm",p.data);
        
    },
    
    cmd_createForm:function(data) {
        this.cmd("add",{
            type:"inx.textfield",
            name:"timeScheduled",
            value:data.timeScheduled,
            width:30,            
            label:"Планирую (ч.)"
        });
        this.cmd("add",{            
            type:"inx.combo",
            width:150,
            value:data.projectID,
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
        
        this.cmd("add",{
            type:"inx.button",
            air:true,
            text:"Изменить статус",
            icon:"trigger",
            menu:menu
        });
        
    },
    
    cmd_changeStatus:function(status) {
        this.fire("changeStatus",status);
    }
    
     
});