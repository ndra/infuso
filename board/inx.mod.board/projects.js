// @include inx.list

inx.ns("inx.mod.board").projects = inx.list.extend({

    constructor:function(p) {    
    
        p.tbar = [{
            icon:"plus",
            text:"Новый проект",
            onclick:[this.id(),"newProject"]
        },"|",{
            icon:"delete",
            onclick:[this.id(),"deleteProject"]
        }]
    
        p.loader = {
            cmd:"board/controller/project/listProjects"
        }
        this.base(p); 
    },
    
    cmd_newProject:function() {
        this.cmd("editProject",{
            projectID:"new"
        })
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
    
    cmd_deleteProject:function(idList) {
    
        this.call({
            cmd:"board/controller/project/deleteProjects",
            idList:idList
        },[this.id(),"load"])
    }
         
});