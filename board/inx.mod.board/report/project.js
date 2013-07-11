// @include inx.iframe,inx.date
// @include inx.mod.board.tagSelector

inx.ns("inx.mod.board.report").project = inx.iframe.extend({

    constructor:function(p) {       
    
        p.tbar = [{
            type:"inx.date",
            name:"from"
        },{
            type:"inx.date",
            name:"to"
        },{
            type:"inx.mod.board.tagSelector",
            name:"tag",
            onchange:[this.id(),"load"]
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
            params.from.setSeconds(new Date().getSeconds() - 3600 * 24 * 30 );
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
        
        if(!params.tag) {
            params.tag = "*";
        }
        var field = inx(this).axis("tbar").items().eq("name","tag");
        field.cmd("setValue",params.tag);
        src.tag = params.tag;
        
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
        params.tag = inx(this).axis("tbar").items().eq("name","tag").info("value");
        inx.direct.setAction(p.action,params);
    }
         
});