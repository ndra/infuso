// @link_with_parent
// @include inx.dialog

inx.mod.inxdev.example.filemanager.preview = inx.dialog.extend({
    
    constructor:function(p) {
        p.width = 350;
        p.height = 350;
        p.items = [{
            type:"inx.gallery",
            data:[{id:1,img:p.filename}]
        }];
        this.base(p);
        inx.hotkey("esc",[this.id(),"destroy"]);
    }
    
})