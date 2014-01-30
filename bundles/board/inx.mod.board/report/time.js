// @include inx.date

inx.ns("inx.mod.board.report").time = inx.panel.extend({

    constructor:function(p) {       
    
        p.layout = "inx.layout.column";
        
        p.style = {
            border:0,
            background:"none",
            spacing:4,
            width:250
        }
    
        p.items = [{
            type:"inx.date",
            name:"from"
        },{
            type:"inx.date",
            name:"to"
        },{
            type:"inx.button",
            text:"Показать",
            onclick:function() {
                this.bubble("submit");
            }
        }];
    
        this.base(p);        
    }
         
});