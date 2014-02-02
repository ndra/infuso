// @link_with_parent
// @include inx.dialog

inx.mod.reflex.editor.actions.dlg = inx.dialog.extend({

    constructor:function(p) {
        p.title = "Выполнение задания";
        p.width = 400;
        p.autoHeight = true;
        this.base(p);
        this.index = 0;
        this.cmd("step");
    },
    
    cmd_step:function() {
        var id = this.ids[this.index];
        if(!id) {
            this.task("complete");
            return;
        }
        this.call({cmd:"infuso:cms:reflex:controller:doAction",action:this.action,id:id},[this.id(),"step"]);
        this.index++;                
        
        if(this.index==1) var html = "0%";
        else var html = (Math.round(this.index / this.ids.length *10000)/100)+"%";
        this.cmd("html","<div style='font-size:100px;' >"+html+"</div>");
    },
    
    cmd_complete:function() {
        this.fire("complete");
        this.task("destroy");
    }
    
});