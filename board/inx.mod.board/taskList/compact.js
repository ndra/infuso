// @link_with_parent
// @include inx.panel

inx.css(".webqkv2ny {table-layout:fixed;width:100%;border-collapse:collapse;}");
inx.css(".webqkv2ny td{vertical-align:middle;height:22px;padding:2;text-align:left;}");

inx.mod.board.taskList.compact = inx.panel.extend({

    constructor:function(p) {
    
        p.style = {
            
        }
        
        this.base(p);
    },
    
    cmd_render:function() {
        this.base();
        this.el.mouseenter(inx.cmd(this.id(),"showControls")).mouseleave(inx.cmd(this.id(),"hideControls"));
        
        var table = $("<table>").addClass("webqkv2ny");
        
        /*if(this.data.my) {
            table.css("border","2px solid rgb(0,0,100)");
        } */
        
        $("<col>").attr("width",40).appendTo(table);
        $("<col>").attr("width",20).appendTo(table);
        $("<col>").attr("width",100).appendTo(table);
        $("<col>").attr("width","100%").appendTo(table);
        $("<col>").attr("width",130).appendTo(table);
        
        var tr = $("<tr>").appendTo(table);
        
        var td = $("<td>").html(this.data.id).appendTo(tr);
        var td = $("<td>").html("<img src='"+this.data.data.responsibleUser.userpic+"' />").appendTo(tr);
        var td = $("<td>").html(this.data.data.status.title).appendTo(tr);        
        var td = $("<td>").html(this.data.data.text).appendTo(tr);
        this.toolsContainer = $("<td>").appendTo(tr);
        
        this.cmd("html",table);        
    },
    
    cmd_showControls:function() {
    
        if(!this.controls) {
    
            var cmp = inx({
                width:130,
                tools:this.data.data.tools,
                showMain:true,
                showAdditional:true,
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