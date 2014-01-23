// @link_with_parent
// @include inx.dialog

inx.mod.reflex.editor.actions.csv = inx.dialog.extend({

    constructor:function(p) {
        p.title = "Экспорт в CSV";
        p.width = 400;
        p.autoHeight = true;
        this.base(p);
        this.cmd("step",1);
    },
    
    cmd_step:function(page) {
        this.call({
            cmd:"reflex:editor:export:doExport",
            collection:this.serializedCollection,
            page:page,
            name:this.filename
        },[this.id(),"handleStep"]);
    },
    
    cmd_handleStep:function(data) {    
        this.filename = data.name;    
        var html = (Math.round(data.page / data.pages *10000)/100)+"%";    
        this.cmd("html","<div style='font-size:100px;' >"+html+"</div>");        
        
        if(data.page<=data.pages) {
            this.cmd("step",data.page);
        } else {
            window.location.href = data.csv;
            this.task("destroy");
        }
    },
    
    cmd_complete:function() {
        this.fire("complete");
        this.task("destroy");
    }    
    
});