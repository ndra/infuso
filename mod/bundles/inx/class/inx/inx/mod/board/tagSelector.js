// @include inx.combo
/*-- /board/inx.mod.board/tagSelector.js --*/


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
        this.on("render",[this.id(),"createList"]);
        
    }
         
});

