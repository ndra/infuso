// @include inx.dialog,inx.combo

inx.mod.board.access.editor = inx.dialog.extend({

    constructor:function(p) {    
        
        p.style = {
            width:400,
            padding:20,
            spacing:5,
            border:0,
            background:"white"
        }
        
        p.layout = "inx.layout.form";
        p.title = "Редактирование доступа";
        this.base(p); 
        this.cmd("requestData");
    },
    
    cmd_requestData:function() {
        this.call({
            cmd:"infuso/board/controller/access/getAccessData",
            accessID:this.accessID
        },[this.id(),"handleData"])
    },
    
    cmd_handleData:function(data) {
    
        this.cmd("add",{
            type:"inx.combo",
            style:{
                width:"parent"
            },
            loader:{
                cmd:"infuso/board/controller/user/getUserList"
            },
            name:"userID",
            value:data.userID,
            text:data.userText,
            label:"Пользователь"
        });
        
        this.cmd("add",{
            type:"inx.combo",
            style:{
                width:"parent"
            },
            loader:{
                cmd:"infuso/board/controller/project/listProjectsSimple"
            },
            name:"projectID",
            value:data.projectID,
            text:data.projectText,
            label:"Проект"
        });
        
        this.cmd("add",{
            type:"inx.checkbox",
            label:"Просмотр комментариев",
            value:data.showComments,
            name:"showComments"
        });
        
        this.cmd("add",{
            type:"inx.checkbox",
            label:"Просмотр потраченного времени",
            value:data.showSpentTime,
            name:"showSpentTime"
        });
        
        this.cmd("add",{
            type:"inx.checkbox",
            label:"Редактирование задач",
            value:data.editTasks,
            name:"editTasks"
        });
        
        this.cmd("add",{
            type:"inx.checkbox",
            label:"Редактирование задач",
            value:data.editTasks,
            name:"editTasks"
        });
        
        this.cmd("add",{
            type:"inx.checkbox",
            label:"Редактирование тэгов и заметок",
            value:data.editTags,
            name:"editTags"
        });
        
        this.cmd("add",{
            type:"inx.button",
            style:{
                fontSize:18,
                height:24
            }, text:"Сохранить",
            onclick:[this.id(),"save"]
        });
        
    },
    
    cmd_save:function() {
        var data = this.info("data");
        this.call({
            cmd:"infuso/board/controller/access/save",
            data:data,
            accessID:this.accessID
        },[this.id(),"handleSave"])
    },
    
    cmd_handleSave:function(ret) {
        if(ret===true) {
            this.task("destroy");
        }
    }
         
});
