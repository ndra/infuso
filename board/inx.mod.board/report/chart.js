// @include inx.iframe,inx.date
// @include inx.mod.board.tagSelector

inx.ns("inx.mod.board.report").chart = inx.iframe.extend({

    constructor:function(p) {       
    
        this.base(p);        
    },
    
    cmd_setParams:function(params) {
    
        var src = {};
    
        src.id = params.id;
        
        var ret = "/board_controller_report/projectChart";
        for(var i in src) {
            ret += "/" + i + "/" + src[i];
        }
        
        this.cmd("setURL",ret);
        
    }
         
});