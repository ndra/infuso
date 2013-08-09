// @include inx.panel

inx.separator = inx.panel.extend({

    constructor:function(p) {
        
        p.style = {
            background:"none",
            border:0
        }
        p.html = $("<div style='padding-bottom:10px;' ><div style='border-bottom:1px solid #ccc;padding-top:10px;' ></div></div>");
             
        this.base(p);
    }
    
});