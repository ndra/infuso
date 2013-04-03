// @include inx.dialog,inx.form

inx.ns("inx.mod.board").task = inx.dialog.extend({

    constructor:function(p) {
    
        p.title = "Редактирование задачи";
        p.width = 900;
        p.resizable = true;        
        p.modal = false;
        
        p.style = {
            border:0,
            background:"#cccccc"
        }
        
        this.actions = inx({
            type:"inx.panel",
            region:"bottom",
            layout:"inx.layout.column",
            style:{
                padding:15,
                background:"none"
            }
            
        });
        
        this.form = inx({
            type:"inx.form",
            style: {
                border:0,
                background:"#ededed"
            },
            labelWidth:120,
            side:[this.actions]
        });
        p.items = [this.form];
        
        this.log = inx({
            title:"Лог",
            type:"inx.mod.board.task.log",
            taskID:p.taskID,            
            lazy:true
        });
        
        p.keepLayout = "x456qmab";
        
        p.side = [{
            type:"inx.tabs",
            region:"right",
            selectNew:false,
            width:500,
            resizable:true,
            items:[
                this.log,
                {title:"Расход времени",lazy:true}
            ]
        },{
            type:"inx.mod.file.manager",
            storage:"board_task:"+p.taskID,
            region:"bottom",
            hideControls:true,
            maxHeight:150            
        }];
        
        this.base(p);
        
        if(p.taskID=="new") {
            this.cmd("handleData",{text:"",project:p.projectID,status:p.status});
        } else {
            this.cmd("requestData");
        }
            
        inx.hotkey("esc",this.id(),"destroy");        
        this.on("submit",[this.id(),"save"]);    
        this.on("smoothBlur",[this.id(),"destroy"]);    
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board:controller:task:getTask",
            taskID:this.taskID
        },[this.id(),"handleData"])
    },
    
    cmd_handleData:function(data) {
    
        this.cmd("setTitle",data.title);
    
        this.data = data;
    
        this.form.cmd("add",{
            type:"inx.textarea",
            value:data.text,
            label:"Описание задачи",
            name:"text",
            style : {
                autoWidth:true,
                height:"content"
            }
        }).cmd("focus");
        
        this.form.cmd("add",{
            type:"inx.mod.board.task.color",
            value:data.color,
            label:"Цвет",
            name:"color"
        });
        
        this.form.cmd("add",{
            type:"inx.textfield",
            value:data.timeSceduled,
            width:50,
            label:"Планирую сделать&nbsp;за&nbsp;(ч.)",
            name:"timeSceduled",
            autoHeight:true
        });
        
        this.form.cmd("add",{
            type:"inx.panel",
            labelAlign:"left",
            label:"Дэдлайн ",
            border:0,
            background:"none",
            layout:"inx.layout.column",
            items:[{
                type:"inx.checkbox",
                label:"Дэдлайн",
                value:data.deadline,
                name:"deadline"
            },{
                type:"inx.date",
                value:data.deadlineDate,
                name:"deadlineDate"
            }]
        });
        
        this.form.cmd("add",{
            type:"inx.select",
            width:200,
            name:"project",
            value:data.project,
            loader:{cmd:"board/controller/project/listProjectsSimple"},
            label:"Проект"
        });
        
        this.form.cmd("add",{
            type:"inx.checkbox",
            name:"bonus",
            value:data.bonus,
            label:"Бонус (гарантия)"
        });
        
        this.form.cmd("add",{
            type:"inx.button",
            icon:"ok",
            text:"Сохранить",
            onclick:[this.id(),"save"]
        }); 
        
        var menu = [];
        for(var i in data.statuses) {
            var button = {
                type:"inx.button",
                text:data.statuses[i].title,
                onclick:inx.cmd(this.id(),"changeStatus",data.statuses[i].id)
            };
            if(data.nextStatus==data.statuses[i].id)
                this.actions.cmd("add",button);
            else
                menu.push(button);
        }
        
        this.actions.cmd("add",{type:"inx.button",text:"Другой статус",icon:"trigger",air:true,menu:menu});
    },
    
    cmd_changeStatus:function(status) {
    
        if(this.data.status==1) {
            var t = window.prompt("Сколько было потрачено времени (в часах)?");
            t = parseFloat(t);
            if(!t) {
                inx.msg("Вы не можете перевести задание в категорию «Выполнено», не указав потраченное время.",1);
                return;
            }
        }
    
        this.call({
            cmd:"board:controller:changeTaskStatus",
            taskID:this.taskID,
            status:status,
            time:t
        },[this.id(),"handleSave"]);
    },
    
    cmd_handleSave:function(ret) {
        if(ret) {
            this.task("destroy");
            this.fire("change");
        }
    },
    
    cmd_save:function() {
        this.call({
            cmd:"board:controller:task:saveTask",
            data:this.form.info("data"),
            taskID:this.taskID,
            status:this.status
        },[this.id(),"handleSave"]);
    }
         
});