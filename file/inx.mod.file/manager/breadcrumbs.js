// @link_with_parent

inx.mod.file.manager.breadcrumbs = inx.box.extend({

    constructor:function(p) {
        if(!p)p={};
        p.height = 22;
        p.value = "/";
        this.base(p);
        if(p.onchange) this.on("change",p.onchange);
    },
    
    cmd_render:function(c) {
        this.base(c);
        this.el.css({background:"#ededed"});
        this.cmd("update");
    },
    
    cmd_mousedown:function(e) {
        var path = $(e.target).data("path");
        if(!path) return;
        this.cmd("setValue",path);
    },
    
    cmd_setValue:function(val) {
        this.value = val;
        this.cmd("update");        
        this.fire("change",this.value);        
    },
    
    info_value:function() {
        return this.value;
    },
    
    cmd_goDeeper:function(dir) {
        this.value = this.value+"/"+dir;
        this.cmd("update");
        this.fire("change",this.value);
    },
    
    cmd_update:function() {
    
        if(this.value=="/")
            this.cmd("hide");
        else
            this.cmd("show")
    
        var path = this.value.split("/");
        this.el.html("");
        var e = $("<div>").css({padding:4}).appendTo(this.el);
        e.css({cursor:"pointer"});
        $("<b>[В начало]: </b>").data("path","/").appendTo(e);
        var folder = "/";
        for(var i in path) {
            var piece = path[i];
            if(!piece) continue;
            var s = $("<span>").css({cursor:"pointer"}).html("/"+piece);
            folder += "/"+piece;
            s.data("path",folder);
            s.appendTo(e);
        }

    }

})