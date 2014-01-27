// @include inx.dialog
// @link_with_parent

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