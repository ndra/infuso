// @include inx.dialog
// @link_with_parent

inx.mod.board.projects.editor = inx.dialog.extend({

    constructor:function(p) {    

        p.layout = "inx.layout.form";
        
        p.style = {
            width:400,
            padding:10,
            spacing:10      
        }

        this.base(p); 
        this.cmd("handleData");
    },
    
    cmd_handleData:function(data) {
    
        this.cmd("add",{
            type:"inx.textfield",
            label:"Название проекта",
            name:"title",
            width:"parent"
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