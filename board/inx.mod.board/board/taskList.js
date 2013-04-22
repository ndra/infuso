// @include inx.list
// @link_with_parent

inx.mod.board.board.taskList = inx.list.extend({

    constructor:function(p) {    

        p.loader = {
            cmd:"board_controller_task::listTasks",
            status:p.status
        };
        
        /*this.pager = inx({
            type:"inx.pager"
        });
        
        p.tbar = [
            {
                type:"inx.textfield",
                width:150
            },
            this.pager
        ] */
        
        p.layout = "inx.layout.column";
        
        if(!p.style)
            p.style = {};
        p.style.spacing = 10;
        
        p.sortable = true;
    
        this.base(p);
        
        this.on("itemclick",[this.id(),"handleItemClick"]);
        this.on("data",[this.id(),"handleData"]);
    },
    
    cmd_handleData:function(data,fullData) {
    
        //inx.msg(fullData.pages);
    
        for(var i in data) {
            if(data[i].id=="new") {
                return;
            }
        }
                
        data.unshift({
            id:"new",
            data:{
                text:"Новая задача"
            }            
        });
        
    },
    
    info_itemType:function() {
        return "inx.mod.board.board.taskList.task";
    },
    
    cmd_handleItemClick:function(id) {
        this.cmd("editTask",id);
    },
    
    cmd_editTask:function(taskID) {
    
        var clip = this.info("itemComponent",taskID).info("param","el");
    
        var task = inx({
            type:"inx.mod.board.task",
            taskID:taskID,
            clipTo:clip
        });
        
        task.cmd("render");
    }
         
});