// @link_with_parent
// @include inx.checkbox

inx.mod.board.task.extra.deadline = inx.panel.extend({

    constructor:function(p) {
    
        p.layout = "inx.layout.column";
        
        p.style = {
            background:"none",
            border:0,
            valign:"top",
            spacing:10
        }
    
        p.items = [{
            type:"inx.checkbox",
            name:"deadline",
            value:p.data.deadline,
            onchange:[this.id(),"updateDateVisibility"],
            label:"Дэдлайн"
        },{
            type:"inx.date",
            value:p.data.deadlineDate,
            name:"deadlineDate"
        }]
        
        this.base(p);
        this.cmd("updateDateVisibility");
    },
    
    cmd_updateDateVisibility:function() {
        var checkbox = inx(this).items().eq("type","inx.checkbox");
        var date = inx(this).items().eq("type","inx.date");
        date.cmd(checkbox.info("value") ? "show" : "hide");
    }
     
});