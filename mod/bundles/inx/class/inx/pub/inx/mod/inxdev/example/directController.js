// @include inx.tabs,inx.direct
/*-- /inxdev/inx.mod.inxdev/example/directController.js --*/


inx.ns("inx.mod.inxdev.example").directController = inx.tabs.extend({

    constructor:function(p) {
        p.style = {
            width:800
        }       
        
        p.items = [{
            title:"Первая таба"
        },{
            title:"Вторая таба"
        },{
            title:"Третья таба"
        }]
        
        this.base(p);
    }
    
});

