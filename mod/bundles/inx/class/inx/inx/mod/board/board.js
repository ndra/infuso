// @include inx.panel
/*-- /board/inx.mod.board/board.js --*/


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

/*-- /board/inx.mod.board/board/create.js --*/


inx.css(".waz3yvrjpurq { cursor:pointer; }");
inx.css(".waz3yvrjpurq:hover { text-decoration:underline; }");

inx.mod.board.board.create = inx.panel.extend({

    constructor:function(p) {    
        p.style = {
            padding:15
        };
        
        p.side = [{
            region:"left",
            width:240,
            layout:"inx.layout.column",
            style:{
                padding:15
            },items:[{
                html:"<b>Новая задача</b>",
                width:100
            },{
                type:"inx.textfield",
                name:"search",
                onchange:[this.id(),"requestData"],
                width:110,
                buttons:[{
                    icon:"delete",
                    onclick:function() { this.owner().cmd("setValue",""); }
                }]
            }]
        }];
        
        this.base(p);
        this.cmd("requestData");
        setInterval(inx.cmd(this.id(),"requestDataInterval"),1000 * 60 * 5);
    },
    
    cmd_requestDataInterval:function() {
    
        if(!this.info("visibleRecursive")) {
            return;
        }
        
        this.cmd("requestData");
    
    },
    
    cmd_requestData:function() {
    
        this.call({
            cmd:"board/controller/project/listProjectsSimple",
            search:inx(this).axis("side").allItems().eq("name","search").info("value")
        },[this.id(),"handleData"]);
    
    },
    
    cmd_handleData:function(data) {
    
        var e = $("<div>").css({
            whiteSpace:"nowrap"
        });
        
        for(var i in data) {
            $("<div>")
                .addClass("waz3yvrjpurq")
                .css({
                    display:"inline-block",
                    fontSize:16,
                    marginRight:10
                }).html(data[i].text)
                .click(inx.cmd(this,"newTask",data[i].id))
                .appendTo(e);
        }
    
        this.base();
        this.cmd("html",e);
       
    },
    
    cmd_newTask:function(projectID) {
        this.call({
            cmd:"board/controller/task/newTask",
            projectID:projectID
        },[this.id(),"handleCreateNewTask"]);
    },
    
    cmd_handleCreateNewTask:function(data) {
        if(!data) {
            return;
        }
        window.location.hash = "task/id/" + data;
    }
         
});

