// @include inx.form

inx.ns("inx.mod.reflex").meta = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            background:"#ededed",
            padding:20,
            spacing:20,
            border:0,
            vscroll:true,
            height:"parent",
            titleMargin:10
        } 
        
        p.vscroll = true;   
  
        p.items = [{
            type:"inx.mod.reflex.meta.route",
            title:"Адрес страницы",
            index:p.index,
            style:{
                border:0,
                background:"none"
            }
        },{
            type:"inx.mod.reflex.meta.title",
            title:"Заголовки",
            index:p.index,
            style:{
                border:0,
                background:"none"
            }
        }];
        
        this.base(p);
    }
     
});
