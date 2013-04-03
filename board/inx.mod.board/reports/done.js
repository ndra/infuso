// @include inx.list

inx.mod.board.reports.done = inx.list.extend({

    constructor:function(p) {
        p.tbar = [{
            type:"inx.select",
            name:"project",
            width:200,
            loader:{cmd:"board:controller:listProjectsShort",optionAll:true},
            onchange:[this.id(),"load"],
            value:"*"
        }]
        p.loader = {cmd:"board:controller:reportDone"}
        this.on("beforeload",[this.id(),"beforeLoad"]);
        this.base(p); 
        inx.hotkey("f5",this.id(),"handleF5");
    },
    
    cmd_beforeLoad:function(data) {
        data.projectID = inx(this).axis("tbar").items().eq("name","project").info("value");
    },
    
    cmd_handleF5:function() {
        this.task("load");
        return false;
    }
         
});