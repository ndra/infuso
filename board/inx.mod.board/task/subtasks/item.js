// @link_with_parent
// @include inx.panel

inx.css(".webqkv2ny td{vertical-align:middle;};");

inx.mod.board.task.subtasks.item = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            
        }
        
        this.base(p);
    },
    
    cmd_render:function() {
        this.base();
        this.el.mouseenter(inx.cmd(this.id(),"showControls")).mouseleave(inx.cmd(this.id(),"hideControls"));
        
        var table = $("<table>").addClass("webqkv2ny").css({
            minHeight:24
        });
        
        var tr = $("<tr>").appendTo(table);
        
        var td = $("<td>").html(this.data.id).appendTo(table);
        var td = $("<td>").html(this.data.status.title).appendTo(table);
        var td = $("<td>").html("<img src='"+this.data.responsibleUser.userpick+"' />").appendTo(table);
        var td = $("<td>").html(this.data.text).appendTo(table);
        
        this.cmd("html",table);        
    },
    
    cmd_showControls:function() {
    
        if(!this.controls) {
    
            var cmp = inx({
                width:100,
                tools:this.data.data.tools,
                type:"inx.mod.board.taskControls",
                taskID:this.data.id,
                region:"right"
            });            
            
            this.controls = cmp;
            this.cmd("addSidePanel",this.controls);
        }
        
        this.controls.cmd("show");
        this.style("background","#ededed");
    },
    
    cmd_hideControls:function() {
        if(this.controls) {
            this.controls.cmd("hide");
        }
        this.style("background","none");
    }
     
});