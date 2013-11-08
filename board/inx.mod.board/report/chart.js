// @include inx.iframe,inx.date
// @include inx.mod.board.tagSelector

inx.ns("inx.mod.board.report").chart = inx.panel.extend({

    constructor:function(p) {  
    
        p.style = {
            padding:20
        }  
    
        p.tbar = [{
            type:"inx.select",
            width:100,
            data:[{
                text:"За день"
            },{
                text:"За неделю"
            },{
                text:"За месяц"
            }]
        },{
            type:"inx.mod.board.report.time"
        }];
        
        p.items = [{
            type:"inx.mod.board.report.chart.conf"
        }]
    
        this.base(p);        
    },
    
    cmd_setParams:function(params) {
    
        /*var src = {};
    
        src.id = params.id;
        
        var ret = "/board_controller_report/projectChart";
        for(var i in src) {
            ret += "/" + i + "/" + src[i];
        }
        
        this.cmd("setURL",ret); */
        
    }
         
});