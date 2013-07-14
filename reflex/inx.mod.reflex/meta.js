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
            title:"<div style='font-size:18px;' >Адрес страницы</div>",
            index:p.index,
            style:{
                border:0,
                background:"none"
            }
        },{
            type:"inx.mod.reflex.meta.title",
            title:"<div style='font-size:18px;' >Мета-данные</div>",
            index:p.index,
            style:{
                border:0,
                background:"none"
            }
        }];
        
        this.base(p);
    }
     
});
