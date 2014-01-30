// @include inx.panel

inx.ns("inx.mod.board").board = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            vscroll:true
        }
    
        this.taskList = inx({
            type:"inx.mod.board.taskList",
            status:p.status
        });
        
        p.tbar = [{
            type:"inx.textfield",
            name:"search",
            onchange:[this.id(),"load"],
            style:{
                width:100
            }
        },{
            type:"inx.mod.board.tagSelector",
            name:"tag",
            onchange:[this.id(),"load"]
        },{
            type:"inx.pager",
            name:"pager",
            onchange:[this.id(),"load"],
            hidden:true
        }];
        
        this.create = inx({
            type:p.type+".create"
        });
        
        p.items = [this.create,this.taskList];
        
        this.taskList.on("beforeload",[this.id(),"handleBeforeLoad"]);
        this.taskList.on("load",[this.id(),"handleLoad"]);
        this.on("show",[this.id(),"handleShow"]);
    
        this.base(p);
    },
    
    cmd_handleBeforeLoad:function(data) {
        var tbar = inx(this).axis("tbar").info("data");
        data.search = tbar.search;
        data.tag = tbar.tag;
        data.page = tbar.pager;
    },
    
    cmd_handleLoad:function(data) {
        var pager = inx(this).axis("tbar").items().eq("name","pager");
        pager.cmd("setTotal",data.pages);
        pager.cmd(data.pages > 1 ? "show" : "hide");
    },
    
    cmd_load:function() {
        this.taskList.cmd("load");
    },
    
    /**
     * При открытии вкладки, перезагружаем содержимое
     **/
    cmd_handleShow:function() {
        this.task("load");
    }

         
});