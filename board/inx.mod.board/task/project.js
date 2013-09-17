// @link_with_parent
// @include inx.panel

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
        e.html(this.taskID + " / " + data.projectTitle);
        this.eProjectTitle = e;
        
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
        }
    }
     
});