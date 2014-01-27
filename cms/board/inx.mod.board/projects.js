// @include inx.list

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