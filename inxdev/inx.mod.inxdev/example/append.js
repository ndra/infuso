// @include inx.panel

inx.ns("inx.mod.inxdev.example").append = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            padding:20,
            spacing:10
        }
    
        this.p1 = inx({
            type:"inx.panel",
            style:{
                padding:20
            }
        })
        
        this.p2 = inx({
            type:"inx.panel",
            style:{
                padding:20
            }
        })
    
        p.items = [this.p1,this.p2];
        
        p.tbar = [{
            type:"inx.button",
            icon:"plus",
            onclick:[this.id(),"addd"]
        }]
        
        this.base(p);
    },
    
    cmd_addd:function() {
    
        var p1 = this.p1;
        var p2 = this.p2;
    
        var cmp = inx({
            type:"inx.panel",
            layout:"inx.layout.column",
            style:{
                padding:5,
                spacing:5               
            },items:[{
                type:"inx.button",
                air:true,
                text:"to panel-1",
                onclick:function() {
                    p1.cmd("add",this.owner());
                }
            },{
                type:"inx.button",
                text:"to panel-2",
                onclick:function() {
                    p2.cmd("add",this.owner());
                }
            },{
                type:"inx.textfield",
                value:Math.random()
            }]
        });
        this.p2.cmd("add",cmp);
    }

});