// @include inx.iframe

inx.ns("inx.mod.board.report").project = inx.iframe.extend({

    constructor:function(p) {       
    
        p.tbar = [{
            type:"inx.date",
            name:"from"
        },{
            type:"inx.date",
            name:"to"
        },{
            type:"inx.button",
            text:"Показать",
            onclick:[this.id(),"search"]
        }]
    
        //p.src = "/board_controller_report/projectDetailed/projectID/"+p.projectID;
        this.on("show","refresh");
        this.base(p);        
    },
    
    cmd_setParams:function(params) {
    
        var src = {};
    
        if(params.from) {
            inx(this).axis("tbar").items().eq("name","from").cmd("setValue",params.from);
            src.from = params.from;
        }
        
        if(params.to) {
            inx(this).axis("tbar").items().eq("name","to").cmd("setValue",params.to);
            src.to = params.to;
        }
        
        src.projectID = this.projectID;
        
        var ret = "/board_controller_report/projectDetailed";
        for(var i in src) {
            ret += "/" + i + "/" + src[i];
        }
        
        this.cmd("setURL",ret);
        
    },
    
    cmd_search:function() {
        var p = inx.direct.get();
        var params = p.params;
        params.from = inx(this).axis("tbar").items().eq("name","from").info("value");
        params.to = inx(this).axis("tbar").items().eq("name","to").info("value");
        inx.direct.setAction(p.action,params);
    }
         
});