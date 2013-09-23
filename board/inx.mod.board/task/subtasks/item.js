// @link_with_parent
// @include inx.panel

inx.css(".webqkv2ny {table-layout:fixed;width:100%;}");
inx.css(".webqkv2ny td{vertical-align:middle;}");
inx.css(".webqkv2ny td.a{width:30px;background:red;}");
inx.css(".webqkv2ny td.b{width:100px;}");
inx.css(".webqkv2ny td.c{width:100px;}");
inx.css(".webqkv2ny td.d{width:100px;}");
inx.css(".webqkv2ny td.e{width:100px;height:40px;}");

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
        
        if(this.data.my) {
            table.css("border","1px solid rgb(0,0,100)");
        }
        
        var tr = $("<tr>").appendTo(table);
        
        var td = $("<td>").addClass("a").html(this.data.id).appendTo(table);
        var td = $("<td>").addClass("b").html(this.data.status.title).appendTo(table);
        var td = $("<td>").addClass("c").html("<img src='"+this.data.responsibleUser.userpic+"' />").appendTo(table);
        var td = $("<td>").addClass("d").html(this.data.text).appendTo(table);
        this.toolsContainer = $("<td>").addClass("e").appendTo(table);
        
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
            this.controls.cmd("render");
            this.controls.cmd("appendTo",this.toolsContainer);
            
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