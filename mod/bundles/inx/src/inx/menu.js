// @include inx.dialog

inx.menu = inx.dialog.extend({

    constructor:function(p) {
    
        p.width = 200;
        p.style = {
            padding:4,
            border:0,
            background:"white",
            height:"content"
        }

        p.showTitle = false;
        p.modal = false;
        p.clipToOwner = true;
        p.autoHide = true;
        this.base(p);
        this.on("closeMenu","hide");
        this.on("render","focus");
    },
    
    cmd_add:function(c) {
        if(c=="|")
            c = inx({
                type:"inx.panel",
                height:10,
                html:"<div style='border-bottom:1px solid #ccc;height:5px;' >",
                style:{
                    border:0,
                    background:"none"
                }                
            });
        this.base(c);
    },
    
    __defaultChildType:"inx.menu.button"
    
});