// @link_with_parent
// @include inx.list

inx.mod.board.task.subtasks = inx.list.extend({

    constructor:function(p) {
    
        p.style = {
            border:0,
            padding:0,
            spacing:1,
            maxHeight:300,
            background:"none"
        };
        
        p.sortable = true;
        
        p.loader = {
            cmd:"board/controller/task/getEpicSubtasks",
            taskID:p.taskID
        };
        
        p.side = [{
            type:p.type+".toolbar",
            taskID:p.taskID,
            region:"top",
            listeners:{
                subtaskAdded:[this.id(),"handleChanges"]
            }
        },{
            region:"top",
            height:10
        }]
        
        this.base(p);
        
        this.on("sortcomplete",[this.id(),"handleSortComplete"]);
        inx.on("board/taskChanged",[this.id(),"handleTaskChanged"]);        
        
    },
    
    cmd_handleTaskChanged:function(params) {  
    
        if(!this.info("visibleRecursive")) {
            return;
        }
        
        if(params.changed.indexOf("status") != -1) {
            this.cmd("load");
        }
        
    },
    
    info_itemConstructor:function(p) {
        return {
            type: this.info("type")+".item",
            itemData:p
        };
    },
    
    cmd_handleItemMouseOver:function(e,data) {
        if(!e.data("bnfgh3-controls")) {
        
            var cmp = this;
        
            var controls = $("<div>")
                .css({
                    position:"absolute",
                    right:0,
                    top:0
                })
                .appendTo(e);

            $("<div title='Буду делать' >").css({
                position:"absolute",
                width:16,
                height:16,
                right:40,
                top:0,
                cursor:"pointer",
                background:"url(/board/res/img/icons16/runner.png)"
            }).click(function() {
                cmp.cmd("doEpicSubtask",data.id);
            }).appendTo(controls);
            
            $("<div>").css({
                position:"absolute",
                width:16,
                height:16,
                right:20,
                top:0,
                cursor:"pointer",
                background:"url("+inx.img("ok")+")"
            }).click(function() {
                cmp.cmd("completeEpicSubtask",data.id);
            }).appendTo(controls);
         
            $("<div>").css({
                position:"absolute",
                width:16,
                height:16,
                right:0,
                top:0,
                background:"url("+inx.img("delete")+")"
            }).click(function() {
                cmp.cmd("cancelEpicSubtask",data.id);
            }).appendTo(controls);
            
        
            e.data("bnfgh3-controls",controls);
            
            var cmp = this;
            e.mouseleave(function() {
                cmp.cmd("handleItemMouseOut",e);
            })
        }
        
        e.data("bnfgh3-controls").stop(true,true).fadeIn("fast");
        e.data("time").stop(true,true).fadeOut("fast");
    },
    
    cmd_handleItemMouseOut:function(e) {
        e.data("bnfgh3-controls").stop(true,true).fadeOut("fast");
        e.data("time").stop(true,true).fadeIn("fast");
    },
    
    cmd_handleChanges:function() {
        this.fire("change");
        this.cmd("load");
    },
    
    cmd_handleSortComplete:function() {
    
        var idList = [];
        this.items().each(function() {
            idList.push(this.data("itemID"));
        });
    
        this.call({
            cmd:"board/controller/task/saveSort",
            idList:idList
        });
    }
    
     
});