// @include inx.combo

inx.ns("inx.mod.board").tagSelector = inx.combo.extend({

    constructor:function(p) {
    
        p.width = 150;
    
        if(!p.value) {
            p.value = "*";
        }
    
        p.loader = {
            cmd:"board/controller/tag/enumTags"
        }        
        this.base(p);        
    }
         
});