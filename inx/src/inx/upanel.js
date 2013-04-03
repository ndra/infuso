// @include inx.panel

inx.css(".hjpvh34vr3k5z7pubopg td{vertical-align:top}")

inx.upanel = inx.panel.extend({

    constructor:function(p) {
        p.layout = "inx.upanel.layout";
        p.autoHeight = 1;
        this.base(p);
    },
    
    cmd_render:function(c) {
        this.base(c);
        this.__body.css({overflow:"hidden"});
    }
});

inx.upanel.layout = {

    create:function() {
        this.mmm = $("<div>").addClass("hjpvh34vr3k5z7pubopg").html(this.layoutHTML+"").appendTo(this.__body);
        this.containers = this.mmm.find("td");
        inx.msg(this.containers.length);
        this.aa = 0;
    },
    
    add:function(id) {
        id = inx(id).id();
        var e = $(this.containers[this.aa]).html("");
        inx(id).cmd("render",e);
        this.aa++;
    },
    
    remove:function(cmp) {
    },
    
    sync:function() {
    }

}
