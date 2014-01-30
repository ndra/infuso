// @include inx.iframe,inx.date

inx.ns("inx.mod.board.report").done = inx.iframe.extend({

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
    
        this.base(p);        
    },
    
    cmd_setParams:function(params) {
    
        var src = {};
    
        // От
        if(!params.from) {
            params.from = new Date();
            params.from.setSeconds(new Date().getSeconds() - 3600 * 24 );
        }        
        var field = inx(this).axis("tbar").items().eq("name","from");
        field.cmd("setValue",params.from);
        src.from = field.info("value");
        
        // До
        if(!params.to) {
            params.to = new Date();
        }        
        var field = inx(this).axis("tbar").items().eq("name","to");
        field.cmd("setValue",params.to);
        src.to = field.info("value");
        
        src.projectID = this.projectID;
        
        var ret = "/board_controller_report/done";
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