// @include inx.panel

inx.ns("inx.mod.inxdev.example").hotkey = inx.panel.extend({

    constructor:function(p) {
        p.html = "Нажмите ctrl+s";
        this.base(p);
        inx.hotkey("ctrl+s",[this.id(),"key"]);
    },
    
    cmd_key:function() {
        inx.msg("ctrl+s pressed");
        return false;
    }

});