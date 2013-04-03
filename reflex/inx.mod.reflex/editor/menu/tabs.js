// @link_with_parent
// @include inx.list

inx.mod.reflex.editor.menu.tabs = inx.list.extend({

    constructor:function(p) {
        p.width = 110;
        this.base(p);   
        this.on("data","selectFirst");
    },
    
    renderer:function(e,data) {
        e = $("<center>").appendTo(e);
        $("<img>").attr("src",data.icon).appendTo(e);
        $("<div>").html(data.text+"").css({fontSize:10}).appendTo(e);
    },
    
    cmd_selectFirst:function() {        
        this.task("selectFirst2",100);
    },
    
    cmd_selectFirst2:function() {        
        this.cmd("setPosition",0);
    }
        
});