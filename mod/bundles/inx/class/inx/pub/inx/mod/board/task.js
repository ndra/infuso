// @include inx.dialog,inx.form,inx.textarea,inx.checkbox,inx.panel,inx.list,inx.mod.board.taskList
/*-- /board/inx.mod.board/task.js --*/


inx.ns("inx.mod.board").task = inx.dialog.extend({

    constructor:function(p) {
    
        p.clipTo = false;
        p.clipToOwner = false;
    
        p.title = "Редактирование задачи";
        p.showTitle = false;
        p.modal = true;
        
        p.style = {
            padding:20,
            spacing:10,
            width: 1000,
            vscroll:true
        }
        
        p.style.maxHeight = $(window).height()-50;
        
        p.destroyOnEscape = true;
        
        this.base(p);

        this.cmd("requestData");
                 
        this.on("submit",[this.id(),"save"]); 
      
        this.extend({
            getMainComponent:function() {
                return inx(this).axis("parents").eq("type","inx.mod.board.main");
            }
        });
        
        inx.hotkey("ctrl+s",[this.id(),"save"]);
        
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"board/controller/task/getTask",
            taskID:this.taskID
        },[this.id(),"handleData"])
    },
    
    cmd_handleData:function(data) {

        if(!data) {
            this.task("destroy");
            return;
        }
        
        if(!data.color) {
            data.color = "white";
        }
        
        this.style("background",data.color);
        
        this.items().cmd("destroy");
        
        this.data = data;

        // Описание задачи и время
        this.cmd("add",{
            type:this.info("type")+".project",
            taskID:this.taskID,
            data:data
        });
        
        // Описание задачи и время
        this.cmd("add",{
            type:"inx.textarea",
            name:"text",
            style: {
                width:"parent",
                fontSize:16,
                height:"content"
            }, value:data.text
        }).cmd("focus");

        this.cmd("add",{
            type:"inx.mod.board.task.extra",
            data:data,
            taskID:this.taskID
        });

        this.cmd("add",{
            type:"inx.mod.board.taskControls",
            big:true,
            region:"top",
            tools:data.tools,
            showMain:true,
            showAdditional:true,
            taskID:this.taskID,
            listeners:{
                action:[this.id(),"save"]
            }
        });

        this.cmd("add",{
            type:"inx.mod.board.task.subtasks",
            parentTaskID:this.taskID
        });
               
        this.cmd("add",{
            type:"inx.mod.board.task.tags",
            name:"tags",
            taskID:this.taskID,
            region:"bottom"
        });        

        this.cmd("add",{
            type:"inx.mod.board.attachments",
            dropArea:this,
            name:"attachments",
            taskID:this.taskID,
            region:"bottom",
            onload:function(data) {
                if(data.length) {
                    this.cmd("show");
                } else {
                    this.cmd("hide");
                }
            }
        });   
        
        if(!inx(this).axis("side").eq("name","comments").exists()) {
            this.cmd("addSidePanel",{
                type:"inx.mod.board.comments",
                name:"comments",
                resizable:true,
                taskID:this.taskID,
                region:"right",
                width:300
            })
        }
        
    },
    
    cmd_toggleMore:function() {
        var side = inx(this.more);
        side.cmd(side.info("visible") ? "hide" : "show");
    },
    
    cmd_save:function() {
    
        var data = this.info("data");
    
        this.call({
            cmd:"board:controller:task:saveTask",
            data:data,
            taskID:this.taskID
        });
        
        return false;
        
    },
    
    cmd_handleTimeInput:function() {
        this.cmd("save");
        this.cmd("handleSetStatus");
    },
    
    cmd_changeStatus:function(status) {
    
        var time = 0;
    
        if(this.currentStatus==1) {
            inx({
                type:"inx.mod.board.timeInput",
                taskID:this.taskID,
                taskStatus:status,
                listeners:{
                    save:[this.id(),"handleTimeInput"]
                }
            }).cmd("render");
            return;
        }
    
        this.cmd("save");
    
        this.call({
            cmd:"board/controller/task/changeTaskStatus",
            taskID:this.taskID,
            status:status,
            time:time
        },[this.id(),"handleSetStatus"]);
                
    },
    
    cmd_handleSetStatus:function() {
        this.cmd("requestData");
    }
         
});

/*-- /board/inx.mod.board/task/extra.js --*/


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

/*-- /board/inx.mod.board/task/extra/color.js --*/


inx.mod.board.task.extra.color = inx.list.extend({

    constructor:function(p) {
    
        p.style = {
            padding:0,
            background:"none"
        }
       
        p.layout = "inx.layout.column";
       
        p.data = [{
            id:"#ffffff",
            color:"#ffffff"
        }, {
            id:"#FFFACD",
            color:"#fffacd"
        }, {
            id:"#FFC0CB",
            color:"#ffc0Cb"
        },{
            id:"#BFEFFF",
            color:"#BFEFFF"
        }]
        
        this.base(p);
        this.private_value = p.value;
        this.on("afterdata","renderValue");
        this.on("select",function(id) {
            this.private_value = id;
        });
    },
    
    info_value:function() {
        return this.private_value;
    },
    
    cmd_setValue:function(val) {
        this.private_value = val;
        this.cmd("renderValue");
    },
    
    cmd_renderValue:function() {    
        this.cmd("select",this.info("value"));
    },
    
    info_itemConstructor:function(data) {
    
        var html = $("<div>").css({
                width:18,
                height:18,
                background:data.color,
                border:"1px solid #ccc"
            }).addClass("color");
    
        var ret = {
            type:"inx.panel",
            html:html,
            style:{
                width:20,
                height:20,
                border:0
            },
            cmd_select:function() {
                this.el.find(".color").css({
                    border:"1px solid black",
                    width:18,
                    height:18
                });
            },
            cmd_unselect:function() {
                this.el.find(".color").css({
                    width:20,
                    height:20,
                    border:"none"
                });
            }
        };
        
        return ret;
        
    }
     
});

/*-- /board/inx.mod.board/task/extra/deadline.js --*/


inx.mod.board.task.extra.deadline = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
        
        p.style = {
            background:"none",
            border:0,
            valign:"top",
            spacing:10
        }
    
        p.items = [{
            type:"inx.checkbox",
            name:"deadline",
            value:p.data.deadline,
            onchange:[this.id(),"updateDateVisibility"],
            label:"Дэдлайн"
        },{
            type:"inx.date",
            value:p.data.deadlineDate,
            name:"deadlineDate"
        }]
        
        this.base(p);
        this.cmd("updateDateVisibility");
    },
    
    cmd_updateDateVisibility:function() {
        var checkbox = inx(this).items().eq("type","inx.checkbox");
        var date = inx(this).items().eq("type","inx.date");
        date.cmd(checkbox.info("value") ? "show" : "hide");
    }
     
});

/*-- /board/inx.mod.board/task/project.js --*/


inx.css(".u2qp12x4 {cursor:pointer;}")
inx.css(".u2qp12x4:hover {text-decoration:underline;}")

inx.mod.board.task.project = inx.panel.extend({

    constructor:function(p) {  
        this.base(p);  
        this.cmd("handleData",p.data);
        this.on("click","openEditor");
        inx.on("board/taskChanged",[this.id(),"handleTaskChanged"]);
    },
    
    cmd_handleData:function(data) {
    
        var e = $("<div>").attr("title","Изменить проект").addClass("u2qp12x4");
        $("<span>").html(this.taskID).appendTo(e);
        $("<span>").html(" / ").appendTo(e);
        this.eProjectTitle = $("<span>").html(data.projectTitle).appendTo(e);
        $("<span>").html(" — ").appendTo(e);
        this.eStatus = $("<span>").html(data.statusText).appendTo(e);
        
        this.cmd("html",e);
    
    },
    
    cmd_openEditor:function() {
        inx({
            type:"inx.mod.board.projectSelector",
            listeners:{
                select:[this.id(),"changeProject"]
            }
        }).cmd("render");
    },
    
    cmd_changeProject:function(projectID) {
        this.call({
            cmd:"board/controller/task/changeProject",
            taskID:this.taskID,
            projectID:projectID
        });
    },
    
    cmd_handleTaskChanged:function(p) {   

        if(p.taskID==this.taskID) {
            if(this.eProjectTitle) {
                this.eProjectTitle.html(p.sticker.project.title);
            }
            if(this.eStatus) {
                this.eStatus.html(p.sticker.status.title);
            }
        }
    }
     
});

/*-- /board/inx.mod.board/task/subtasks.js --*/


inx.mod.board.task.subtasks = inx.mod.board.taskList.extend({

    constructor:function(p) {
    
        p.status = 0;
        
        p.style = {
            border:0
        }
        
        p.viewMode = "compact";

        p.side = [{
            type:p.type+".toolbar",
            taskID:p.parentTaskID,
            region:"top",
            listeners:{
                subtaskAdded:[this.id(),"handleChanges"]
            }
        }]
        
        this.base(p);

   }
     
});

/*-- /board/inx.mod.board/task/subtasks/toolbar.js --*/


inx.mod.board.task.subtasks.toolbar = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
        
        p.style = {
            background:"none",
            spacing:2,
            padding:0
        }
        
        p.items = [{
            type:"inx.button",
            text:"Взять подзадачу",
            icon:"/board/res/img/icons16/add.png",
            air:true,
            listeners:{
                click:[this.id(),"openDialog"]
            },
            style:{
                background:0,
                border:0
            }
        }/*, {
            width:20
        },{
            type:"inx.panel",
            html:"<span style='padding:4px;background:#ccc;display:inline-block;border-radius:5px;' >Активные (5)</span> Выполненные (124)",
            width:220
        }, {
            type:"inx.pager",
            total:5
        } */]
        
        this.base(p);

    },
    
    cmd_openDialog:function(e) {
    
        var cmp = this;
    
        inx({
            type:"inx.mod.board.addSubtask",
            clipTo:e.target,
            taskID:this.taskID,
            listeners:{
                subtaskAdded:function() {
                    cmp.fire("subtaskAdded");
                }
            }
        }).cmd("render")
    }
     
});

/*-- /board/inx.mod.board/task/tags.js --*/


inx.mod.board.task.tags = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
    
        p.style = {
            background:"none",
            spacing:5,
            padding:5
        };
       
        this.base(p);        
        this.cmd("requestData");
    },
    
    cmd_requestData:function() {
    
        this.call({
            cmd:"board_controller_tag/getTaskTags",
            taskID:this.taskID
        },[this.id(),"handleData"]);
    
    },
    
    cmd_handleData:function(data) {
    
        this.cmd("add",{
            type:"inx.panel",
            width:16,
            html:"<img src='/board/res/img/icons16/tag.png' />",
        });
        
        for(var i=0;i<data.tags.length;i++) {
        
            this.cmd("add",{
                type:"inx.checkbox",
                label:data.tags[i].tagTitle,
                tagID:data.tags[i].tagID,
                taskID:this.taskID,
                value:data.tags[i].value,
                onchange:function() {
                    this.call({
                        cmd:"board_controller_tag/updateTag",
                        taskID:this.taskID,
                        tagID:this.tagID,
                        value:this.info("value")
                    });
                }
            });
        }
    
    }
     
});

