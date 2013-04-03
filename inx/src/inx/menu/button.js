// @include inx.button
// @link_with_parent

inx.menu.button = inx.button.extend({

    constructor:function(p) {
        p.air = true;
        this.base(p);
        this.on("click",function() { this.bubble("closeMenu"); });
    },    
    
    __defaultChildType:"inx.button"    
    
});