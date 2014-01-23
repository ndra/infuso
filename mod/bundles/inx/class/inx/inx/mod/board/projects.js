// @include inx.list,inx.dialog
/*-- /board/inx.mod.board/projects.js --*/


inx.ns("inx.mod.board").projects = inx.list.extend({

    constructor:function(p) {    
    
        p.tbar = [{
            icon:"plus",
            text:"Новый проект",
            air:true,
            onclick:[this.id(),"newProject"]
        },"|",{
            icon:"refresh",
            air:true,
            onclick:[this.id(),"load"]
        },"|",{
            icon:"delete",
            air:true,
            onclick:[this.id(),"deleteSelectedProjects"]
        }]
    
        p.loader = {
            cmd:"board/controller/project/listProjects"
        }
        
        this.on("itemdblclick","handleDblClick");
        this.on("itemСlick","handleItemClick");
        
        this.base(p); 
    },
    
    cmd_handleDblClick:function(id) {
        this.cmd("editProject",{
            projectID:id
        });        
    },
    
    cmd_handleItemClick:function(id,e) {
        col = e.col;        
        if(col == "subscribe") {
            this.call({
                cmd:"board/controller/project/subscribeProject",
                projectID:id
            },[this.id(),"load"]);
        }
    },
    
    cmd_newProject:function() {
        this.cmd("editProject",{
            projectID:"new"
        });
    },
    
    cmd_editProject:function(p) {
        inx({
            type:"inx.mod.board.projects.editor",
            projectID:p.projectID,
            listeners:{
                update:[this.id(),"load"]
            }
        }).cmd("render")
    },
    
    cmd_deleteSelectedProjects:function() {
    
        var idList = this.info("selection");
        
        if(!idList.length) {
            inx.msg("Проекты не выбраны",1);
            return;
        }
        
        if(!confirm("Удалить выбранные проекты (" + idList.length + ")")) {
            return;
        }
    
        this.call({
            cmd:"board/controller/project/deleteProjects",
            idList:idList
        },[this.id(),"load"])
    }
         
});

/*-- /board/inx.mod.board/projects/editor.js --*/


inx.mod.board.projects.editor = inx.dialog.extend({

    constructor:function(p) {  
    
        p.title = "Редактирование проекта";

        p.layout = "inx.layout.form";
        
        p.style = {
            width:400,
            padding:10,
            spacing:10      
        }

        p.autoDestroy = true;

        this.base(p); 
        
        if(this.projectID=="new") {
            this.cmd("handleData");
        } else {
            this.cmd("requestData");
        }
    },
    
    cmd_requestData:function() {
    
        this.call({
            cmd:"board/controller/project/getProject",
            projectID:this.projectID,
        },[this.id(),"handleData"]);
    
    },
    
    cmd_handleData:function(data) {
    
        this.cmd("add",{
            type:"inx.textfield",
            label:"Название проекта",
            name:"title",
            width:"parent",
            value:data.title
        });
        
        this.cmd("add",{
            type:"inx.textfield",
            label:"Адрес сайта",
            name:"url",
            width:"parent",
            value:data.url
        });
        
        this.cmd("add",{
            type:"inx.textfield",
            label:"Закрывать задачи через данное количество дней",
            name:"completeAfter",
            width:"parent",
            value:data.completeAfter
        });
        
        this.cmd("add",{
            type:"inx.button",
            text:"Сохранить",
            onclick:[this.id(),"saveProject"]
        })
    
    },
    
    cmd_saveProject:function() {
        this.call({
            cmd:"board/controller/project/saveProject",
            projectID:this.projectID,
            data:this.info("data")
        },[this.id(),"handleSave"]);
    },
    
    cmd_handleSave:function() {
        this.task("destroy");
        this.fire("update");
    }
         
});

