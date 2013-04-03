// @include inx.panel

inx.ns("inx.mod.inxdev.example").dd = inx.box.extend({

    constructor:function(p) {
        p.width = 400;
        p.height = 400;
        this.base(p);
    },
    
    cmd_render:function() {
        this.base();
        inx.dd.enable(this.el,this,"handleDD",{helper:true});
    },
    
    cmd_handleDD:function(p) { 
      
        this.el.html("");
        for(var i in p) {
            $("<div>").html(i+": "+p[i]).appendTo(this.el);
        }
        
       // return false;
        
    }

});