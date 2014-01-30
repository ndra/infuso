// @include inx.panel
// @link_with_parent

inx.mod.board.report.chart.conf = inx.tabs.extend({

    constructor:function(p) {  
    
        p.items = [{
            title:"Общий"
        },{
            title:"По пользователям"
        }]
       
        this.base(p);        
    }
         
});