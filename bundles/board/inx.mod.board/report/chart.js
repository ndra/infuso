// @include inx.iframe,inx.date
// @include inx.mod.board.tagSelector

inx.ns("inx.mod.board.report").chart = inx.panel.extend({

    constructor:function(p) {  
    
        p.style = {
            padding:20,
            spacing:10
        }  
    
        p.tbar = [{
            type:"inx.select",
            name:"group",
            width:100,
            data:[{
                text:"За день",
                id:"day"
            },{
                text:"За неделю",
                id:"week"
            },{
                text:"За месяц",
                id:"month"
            }]
        },{
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
        }];
        
        this.iframe = inx({
            type:"inx.iframe",
            style:{
                padding:10,
                height:400,
                border:1
            }
        });
        
        p.items = [this.iframe];
        
        /*p.items = [this.iframe, {
            type:"inx.mod.board.report.chart.conf"
        }] */
    
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
        
        if(!params.group) {
            params.group = "week";
        }
        var field = inx(this).axis("tbar").items().eq("name","group");
        field.cmd("setValue",params.group);
        src.group = params.group;
        
        src.projectID = params.id;
        
        var ret = "/board_controller_report/projectChart";
        for(var i in src) {
            ret += "/" + i + "/" + src[i];
        }
        
        this.iframe.cmd("setURL",ret);
    },
    
    cmd_search:function() {
        var p = inx.direct.get();
        var params = p.params;
        params.from = inx(this).axis("tbar").items().eq("name","from").info("value");
        params.to = inx(this).axis("tbar").items().eq("name","to").info("value");
        params.group = inx(this).axis("tbar").items().eq("name","group").info("value");
        inx.direct.setAction(p.action,params);
    }
         
});