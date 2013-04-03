// @link_with_parent
// @include inx.list

inx.mod.reflex.editor.list.items = inx.list.extend({

    constructor:function(p) {
        if(!p.style)
            p.style = {}
        p.style.spacing = 4;
        p.style.valign = "top";
        
        this.base(p);
    }
    
});