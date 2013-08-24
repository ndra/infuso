// @include inx.panel

inx.separator = inx.panel.extend({

    constructor:function(p) {
        
        p.style = {
            background:"none",
            border:0
        }
        var src = "/inx/res/img/components/separator/hr.png";
        p.html = $("<div style='padding:10px 0;' ><img src='"+src+"' style='width:100%;height:1px;opacity:.1;' /></div>");
             
        this.base(p);
    }
    
});