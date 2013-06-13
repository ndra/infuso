// @include inx.iframe
// @include inx.mod.board.report.time

inx.ns("inx.mod.board.report").projects = inx.iframe.extend({

    constructor:function(p) {       
    
        p.tbar = [{
            type:"inx.mod.board.report.time"
        }];
    
        p.src = "/board_controller_report/projects";
        this.on("submit", [this.id(),"search"]);
        this.base(p);        
    },
    
    cmd_setParams:function(params) {
    
        var src = {};
    
        // От
        
        if(!params.from) {
            params.from = new Date();
            params.from.setSeconds(new Date().getSeconds() - 3600 * 24 * 30 );
        }
                
        var field = inx(this).axis("tbar").allItems().eq("name","from");
        field.cmd("setValue",params.from);
        src.from = field.info("value");
        
        // До
        
        if(!params.to) {
            params.to = new Date();
        }        
        
        var field = inx(this).axis("tbar").allItems().eq("name","to");
        field.cmd("setValue",params.to);
        src.to = field.info("value");
        
        var ret = "/board_controller_report/projects";
        for(var i in src) {
            ret += "/" + i + "/" + src[i];
        }
        
        this.cmd("setURL",ret);
        
    },
    
    cmd_search:function() {
        var p = inx.direct.get();
        var params = p.params;
        params.from = inx(this).axis("tbar").allItems().eq("name","from").info("value");
        params.to = inx(this).axis("tbar").allItems().eq("name","to").info("value");
        inx.direct.setAction(p.action,params);
    }
         
});