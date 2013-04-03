// @include inx.list

inx.mod.board.reports.project = inx.panel.extend({

    constructor:function(p) {
        p.tbar = [{
            type:"inx.select",
            name:"project",
            width:200,
            loader:{cmd:"board:controller:listProjectsShort",optionAll:true},
            onchange:[this.id(),"load"],
            value:"*"
        }]
        p.padding = 10;
        this.base(p); 
        inx.hotkey("f5",this.id(),"handleF5");
        this.task("load");
    },
    
    cmd_load:function(data) {
        this.call({
            cmd:"board:controller:reportDoneForProject",
            projectID:inx(this).axis("tbar").items().eq("name","project").info("value"),
        },[this.id(),"handleLoad"]);
    },
    
    cmd_handleLoad:function(html) {
        this.cmd("html",html);
    },
    
    cmd_handleF5:function() {
        this.task("load");
        return false;
    }
         
});